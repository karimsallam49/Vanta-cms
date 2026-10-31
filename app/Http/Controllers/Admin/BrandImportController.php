<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\BrandsImport;
use App\Models\ActivityLog;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BrandImportController extends Controller
{
    private const COLUMNS = [
        ['name_en', 'Brand name — required'],
        ['name_ar', 'اسم البراند بالعربي'],
        ['segment', 'urban / adventure / performance / premium — required'],
        ['origin_en', 'e.g. Salzburg, Austria'],
        ['origin_ar', 'المنشأ بالعربي'],
        ['tagline_en', 'Short line under the name'],
        ['tagline_ar', 'الشعار بالعربي'],
        ['accent', 'Hex color, e.g. #e8963c'],
    ];

    private const SETTINGS = [
        ['name' => 'dup_mode', 'label' => 'EXISTING BRANDS', 'hint' => 'When a row matches a brand already in the system',
         'options' => ['update' => 'Update it with the sheet data', 'skip' => 'Leave it untouched']],
    ];

    public function create(): View
    {
        return view('imports.upload', [
            'title' => 'Brands',
            'listUrl' => route('admin.brands.index'),
            'templateUrl' => route('admin.brands.import.template'),
            'previewUrl' => route('admin.brands.import.preview'),
            'columns' => self::COLUMNS,
            'settings' => self::SETTINGS,
        ]);
    }

    public function template(): BinaryFileResponse
    {
        return Excel::download(new TemplateExport([
            BrandsImport::HEADINGS,
            ['DRIFTER', 'دريفتر', 'adventure', 'Tbilisi, Georgia', 'تبليسي، جورجيا',
                'Built where the roads end', 'مصمم حيث تنتهي الطرق', '#3c8fe8'],
        ]), 'brands-template.xlsx');
    }

    public function export(): BinaryFileResponse
    {
        // Round-trip export: same columns as the template, so the file can be
        // edited and fed straight back through Bulk import.
        $rows = Brand::orderBy('name')->get()
            ->map(fn (Brand $b) => [
                $b->name, $b->name_ar, $b->segment, $b->origin, $b->origin_ar,
                $b->tagline, $b->tagline_ar, $b->accent,
            ])
            ->all();

        ActivityLog::record('export', null, 'Exported '.count($rows).' brands to Excel', ['entity' => 'brands', 'rows' => count($rows)]);

        return Excel::download(new TemplateExport([BrandsImport::HEADINGS, ...$rows]), 'brands-export.xlsx');
    }

    public function preview(Request $request): View
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'dup_mode' => ['nullable', 'in:update,skip'],
        ]);

        $settings = array_filter($request->only('dup_mode'));

        $path = $data['file']->store('imports');
        $import = new BrandsImport($settings);
        $sheet = Excel::toArray($import, Storage::path($path))[0] ?? [];

        $rows = collect($sheet)->map(fn ($row, $i) => [
            'num' => $i + 2,
            ...$import->evaluateRow((array) $row),
        ]);

        return view('imports.preview', [
            'title' => 'Brands',
            'listUrl' => route('admin.brands.index'),
            'commitUrl' => route('admin.brands.import.commit'),
            'filePath' => $path,
            'importSettings' => $settings,
            'rows' => $rows->take(40),
            'shown' => min(40, $rows->count()),
            'total' => $rows->count(),
            'errors' => $rows->where('status', 'error')->count(),
            'new' => $rows->where('status', 'new')->count(),
            'updates' => $rows->where('status', 'update')->count(),
            'toSkip' => $rows->where('status', 'skip')->count(),
        ]);
    }

    private const CHUNK = 50;

    /**
     * One call = one slice of the sheet. The browser loops until `done`,
     * so the progress number on screen is the real row count.
     */
    public function commit(Request $request)
    {
        $data = $request->validate([
            'path' => ['required', 'string'],
            'offset' => ['nullable', 'integer', 'min:0'],
            'dup_mode' => ['nullable', 'in:update,skip'],
        ]);

        $settings = array_filter($request->only('dup_mode'));

        $path = 'imports/' . basename($data['path']);
        abort_unless(Storage::exists($path), 404);

        $import = new BrandsImport($settings);
        $rows = collect(Excel::toArray($import, Storage::path($path))[0] ?? []);
        $total = $rows->count();
        $offset = (int) ($data['offset'] ?? 0);

        $import->importRows($rows->slice($offset, self::CHUNK)->values(), $offset);

        $processed = min($offset + self::CHUNK, $total);
        $done = $processed >= $total;
        if ($done) {
            Storage::delete($path);
            ActivityLog::record('import', null, "Imported brands sheet — {$import->created} created, {$import->updated} updated", [
                'entity' => 'brands', 'rows' => $total,
                'created' => $import->created, 'updated' => $import->updated,
                'skipped' => $import->skipped, 'failures' => count($import->failures ?? []),
            ]);
        }

        return response()->json([
            'processed' => $processed,
            'total' => $total,
            'created' => $import->created,
            'updated' => $import->updated,
            'skipped' => $import->skipped,
            'failures' => $import->failures,
            'done' => $done,
        ]);
    }
}
