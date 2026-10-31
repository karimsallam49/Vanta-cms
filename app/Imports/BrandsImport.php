<?php

namespace App\Imports;

use App\Models\Brand;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Bulk brand import — admin only (routes sit behind the admin middleware).
 */
class BrandsImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public const HEADINGS = [
        'name_en', 'name_ar', 'segment', 'origin_en', 'origin_ar',
        'tagline_en', 'tagline_ar', 'accent',
    ];

    private const SEGMENTS = ['urban', 'adventure', 'performance', 'premium'];

    public int $created = 0;
    public int $updated = 0;
    public int $skipped = 0;
    public array $failures = [];

    private int $offset = 0;

    /** $settings['dup_mode'] — update|skip for rows matching an existing slug. */
    public function __construct(private array $settings = [])
    {
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $i => $row) {
            $rowNumber = $this->offset + $i + 2;
            $check = $this->evaluateRow($row->toArray());
            if ($check['errors']) {
                $this->failures[] = ['row' => $rowNumber, 'name' => $check['name'], 'errors' => $check['errors']];
                continue;
            }
            if ($check['status'] === 'skip') {
                $this->skipped++;
                continue;
            }
            $this->persistRow($check) === 'created' ? $this->created++ : $this->updated++;
        }
        $this->offset += $rows->count();
    }

    /**
     * Process one slice during the browser-driven chunked commit —
     * each request advances the offset so progress is real, not a spinner.
     */
    public function importRows(Collection $rows, int $offset): void
    {
        $this->offset = $offset;
        $this->collection($rows);
    }

    public function chunkSize(): int
    {
        return 50;
    }

    public function evaluateRow(array $row): array
    {
        $errors = [];

        $name = trim((string) ($row['name_en'] ?? ''));
        if ($name === '') {
            $errors[] = 'name_en is required';
        }

        $segment = Str::lower(trim((string) ($row['segment'] ?? '')));
        if (! in_array($segment, self::SEGMENTS, true)) {
            $errors[] = 'segment must be urban, adventure, performance or premium';
        }

        $accent = trim((string) ($row['accent'] ?? '#e10600')) ?: '#e10600';
        if (! preg_match('/^#[0-9a-fA-F]{6}$/', $accent)) {
            $errors[] = "accent '{$accent}' is not a #rrggbb color";
        }

        $exists = $name !== '' && Brand::where('slug', Str::slug($name))->exists();

        return [
            'name' => $name ?: '(unnamed)',
            'status' => $errors ? 'error' : ($exists ? (($this->settings['dup_mode'] ?? 'update') === 'skip' ? 'skip' : 'update') : 'new'),
            'errors' => $errors,
            'warnings' => [],
            'data' => [
                'name' => $name,
                'name_ar' => $this->str($row['name_ar'] ?? null),
                'slug' => Str::slug($name),
                'segment' => $segment,
                'origin' => $this->str($row['origin_en'] ?? null),
                'origin_ar' => $this->str($row['origin_ar'] ?? null),
                'tagline' => $this->str($row['tagline_en'] ?? null),
                'tagline_ar' => $this->str($row['tagline_ar'] ?? null),
                'accent' => $accent,
            ],
        ];
    }

    private function persistRow(array $check): string
    {
        $brand = Brand::updateOrCreate(['slug' => $check['data']['slug']], $check['data']);

        return $brand->wasRecentlyCreated ? 'created' : 'updated';
    }

    private function str($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
