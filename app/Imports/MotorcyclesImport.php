<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Currency;
use App\Models\Motorcycle;
use App\Models\Showroom;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Bulk motorcycle import. The same evaluateRow() check powers both the
 * dry-run preview and the real chunked commit — what you see is what lands.
 */
class MotorcyclesImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public const HEADINGS = [
        'name_en', 'name_ar', 'brand', 'tagline_en', 'tagline_ar',
        'description_en', 'description_ar', 'price', 'currency', 'monthly_from',
        'engine_cc', 'power_hp', 'weight_kg', 'top_speed_kmh',
        'quantity', 'is_active', 'showrooms', 'image_url',
    ];

    public int $created = 0;
    public int $updated = 0;
    public int $skipped = 0;
    public array $failures = [];

    private Collection $brands;
    private Collection $currencies;
    private Collection $showrooms;
    private int $offset = 0;

    /**
     * $settings — user-chosen behaviour from the upload screen:
     *   dup_mode  update|skip   existing slug → overwrite or leave alone
     *   cur_mode  create|error  unknown currency code → auto-create or fail the row
     *   sr_mode   ignore|error  unknown showroom city → drop it or fail the row
     */
    public function __construct(private User $user, private array $settings = [])
    {
        // Data-entry users may only import into their assigned brands.
        $this->brands = $user->isAdmin() ? Brand::all() : $user->brands()->get();
        $this->currencies = Currency::all();
        $this->showrooms = Showroom::all();
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $i => $row) {
            $rowNumber = $this->offset + $i + 2; // +1 header, +1 zero-index
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

    /**
     * Validate + normalize one sheet row without touching the database.
     * Used by the preview screen and again inside the chunked commit.
     */
    public function evaluateRow(array $row): array
    {
        $errors = [];
        $warnings = [];

        $name = trim((string) ($row['name_en'] ?? ''));
        if ($name === '') {
            $errors[] = 'name_en is required';
        }

        $brandValue = trim((string) ($row['brand'] ?? ''));
        $brand = $this->findBrand($brandValue);
        if ($brandValue === '') {
            $errors[] = 'brand is required (name or slug)';
        } elseif (! $brand) {
            $errors[] = "brand '{$brandValue}' not found or not assigned to you";
        }

        $price = $row['price'] ?? null;
        if (! is_numeric($price) || $price < 0) {
            $errors[] = 'price must be a number ≥ 0';
        }
        $monthly = $row['monthly_from'] ?? null;
        if (! is_numeric($monthly) || $monthly < 0) {
            $errors[] = 'monthly_from must be a number ≥ 0';
        }

        $code = Str::upper(trim((string) ($row['currency'] ?? 'USD'))) ?: 'USD';
        $currency = $this->currencies->firstWhere('code', $code);
        if (! $currency) {
            $this->setting('cur_mode') === 'error'
                ? $errors[] = "currency '{$code}' not found — add it first or enable auto-create"
                : $warnings[] = "currency '{$code}' will be created";
        }

        foreach (['engine_cc', 'power_hp', 'weight_kg', 'top_speed_kmh', 'quantity'] as $col) {
            $v = $row[$col] ?? null;
            if ($v !== null && $v !== '' && ! is_numeric($v)) {
                $errors[] = "{$col} must be a number";
            }
        }

        $showroomIds = $this->resolveShowrooms($row['showrooms'] ?? null, $warnings, $errors);

        if (($row['image_url'] ?? '') !== '' && ! filter_var($row['image_url'], FILTER_VALIDATE_URL)) {
            $warnings[] = 'image_url is not a valid URL — skipped';
        }

        $exists = $name !== '' && Motorcycle::where('slug', Str::slug($name))->exists();

        return [
            'name' => $name ?: '(unnamed)',
            'brand' => $brand,
            'currency_code' => $code,
            'showroom_ids' => $showroomIds,
            'status' => $errors ? 'error' : ($exists ? ($this->setting('dup_mode') === 'skip' ? 'skip' : 'update') : 'new'),
            'errors' => $errors,
            'warnings' => $warnings,
            'data' => [
                'brand_id' => $brand?->id,
                'name' => $name,
                'name_ar' => $this->str($row['name_ar'] ?? null),
                'slug' => Str::slug($name),
                'tagline' => $this->str($row['tagline_en'] ?? null),
                'tagline_ar' => $this->str($row['tagline_ar'] ?? null),
                'description' => $this->str($row['description_en'] ?? null),
                'description_ar' => $this->str($row['description_ar'] ?? null),
                'price' => (int) $price,
                'monthly_from' => (int) $monthly,
                'engine_cc' => $this->num($row['engine_cc'] ?? null),
                'power_hp' => $this->num($row['power_hp'] ?? null),
                'weight_kg' => $this->num($row['weight_kg'] ?? null),
                'top_speed_kmh' => $this->num($row['top_speed_kmh'] ?? null),
                'quantity' => (int) ($row['quantity'] ?? 0),
                'is_active' => $this->parseBool($row['is_active'] ?? null),
                'image' => filter_var($row['image_url'] ?? '', FILTER_VALIDATE_URL) ? $row['image_url'] : null,
            ],
        ];
    }

    private function persistRow(array $check): string
    {
        // Auto-create the currency the sheet asked for.
        $currency = $this->currencies->firstWhere('code', $check['currency_code'])
            ?? tap(Currency::create(['code' => $check['currency_code'], 'name' => $check['currency_code'], 'symbol' => $check['currency_code']]),
                fn (Currency $c) => $this->currencies->push($c));

        $data = $check['data'];
        $data['currency_id'] = $currency->id;

        $motorcycle = Motorcycle::updateOrCreate(['slug' => $data['slug']], $data);
        $motorcycle->showrooms()->sync($check['showroom_ids']);

        return $motorcycle->wasRecentlyCreated ? 'created' : 'updated';
    }

    private function findBrand(string $value): ?Brand
    {
        $value = Str::lower($value);

        return $this->brands->first(
            fn (Brand $b) => Str::lower($b->name) === $value || $b->slug === $value
        );
    }

    private function resolveShowrooms($value, array &$warnings, array &$errors): array
    {
        if (! $value) {
            return [];
        }

        $ids = [];
        foreach (array_filter(array_map('trim', explode(',', (string) $value))) as $city) {
            $showroom = $this->showrooms->first(fn (Showroom $s) => Str::lower($s->city) === Str::lower($city));
            if ($showroom) {
                $ids[] = $showroom->id;
            } elseif ($this->setting('sr_mode') === 'error') {
                $errors[] = "showroom '{$city}' not found";
            } else {
                $warnings[] = "showroom '{$city}' not found — skipped";
            }
        }

        return $ids;
    }

    private function setting(string $key): string
    {
        return $this->settings[$key] ?? match ($key) {
            'dup_mode' => 'update',
            'cur_mode' => 'create',
            'sr_mode' => 'ignore',
            default => '',
        };
    }

    private function parseBool($value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        return in_array(Str::lower(trim((string) $value)), ['1', 'true', 'yes', 'y'], true);
    }

    private function str($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function num($value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }
}
