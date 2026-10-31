<?php

namespace App\Http\Controllers\Admin;

use App\Exports\TemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\MotorcyclesImport;
use App\Models\ActivityLog;
use App\Models\Motorcycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MotorcycleImportController extends Controller
{
    private const COLUMNS = [
        ['name_en', 'Model name — required'],
        ['name_ar', 'اسم الموديل بالعربي'],
        ['brand', 'Brand name or slug (must be one you manage)'],
        ['tagline_en', 'Short line shown under the name'],
        ['tagline_ar', 'الشعار بالعربي'],
        ['description_en', 'Paragraph on the model page'],
        ['description_ar', 'الوصف بالعربي'],
        ['price', 'Number, no symbol — required'],
        ['currency', 'Code like USD / AED — created automatically if new'],
        ['monthly_from', 'Number — required'],
        ['engine_cc', 'Optional number'],
        ['power_hp', 'Optional number'],
        ['weight_kg', 'Optional number'],
        ['top_speed_kmh', 'Optional number'],
        ['quantity', 'Stock count, default 0'],
        ['is_active', '1/0 or yes/no — default 1'],
        ['showrooms', 'Comma-separated city names, e.g. "Dubai, Riyadh"'],
        ['image_url', 'Optional fallback cover URL'],
    ];

    private const SETTINGS = [
        ['name' => 'dup_mode', 'label' => 'EXISTING MODELS', 'hint' => 'When a row matches a model already in the system',
         'options' => ['update' => 'Update it with the sheet data', 'skip' => 'Leave it untouched']],
        ['name' => 'cur_mode', 'label' => 'UNKNOWN CURRENCIES', 'hint' => 'When a row uses a currency code not in the system',
         'options' => ['create' => 'Create it automatically', 'error' => 'Mark the row as an error']],
        ['name' => 'sr_mode', 'label' => 'UNKNOWN SHOWROOMS', 'hint' => 'When a row lists a showroom city not in the system',
         'options' => ['ignore' => 'Skip it — link the valid ones', 'error' => 'Mark the row as an error']],
    ];

    public function create(): View
    {
        $this->authorize('create', Motorcycle::class);

        return view('imports.upload', [
            'title' => 'Motorcycles',
            'listUrl' => route('admin.motorcycles.index'),
            'templateUrl' => route('admin.motorcycles.import.template'),
            'previewUrl' => route('admin.motorcycles.import.preview'),
            'columns' => self::COLUMNS,
            'settings' => self::SETTINGS,
        ]);
    }

    public function template(): BinaryFileResponse
    {
        $this->authorize('create', Motorcycle::class);

        return Excel::download(new TemplateExport([
            MotorcyclesImport::HEADINGS,
            ['NOMAD TRAIL 650', 'نوماد تريل 650', 'nomad', 'Mid-weight adventure twin', 'توين مغامرات متوسط',
                'A balanced middleweight built for mixed terrain.', 'موديل متوسط مبني للطرق المختلطة.',
                12400, 'USD', 259, 650, 62, 188, 171, 8, 1, 'Dubai, Riyadh', ''],
        ]), 'motorcycles-template.xlsx');
    }

    public function export(): BinaryFileResponse
    {
        // Round-trip export: same columns as the template, so the file can be
        // edited and fed straight back through Bulk import.
        $rows = Motorcycle::visibleTo(request()->user())
            ->with(['brand', 'currency', 'showrooms'])
            ->orderBy('brand_id')->orderBy('name')
            ->get()
            ->map(fn (Motorcycle $m) => [
                $m->name, $m->name_ar, $m->brand->slug,
                $m->tagline, $m->tagline_ar, $m->description, $m->description_ar,
                $m->price, $m->currency?->code ?? 'USD', $m->monthly_from,
                $m->engine_cc, $m->power_hp, $m->weight_kg, $m->top_speed_kmh,
                $m->quantity, $m->is_active ? 1 : 0,
                $m->showrooms->pluck('city')->join(', '), $m->image,
            ])
            ->all();

        ActivityLog::record('export', null, 'Exported '.count($rows).' motorcycles to Excel', ['entity' => 'motorcycles', 'rows' => count($rows)]);

        return Excel::download(new TemplateExport([MotorcyclesImport::HEADINGS, ...$rows]), 'motorcycles-export.xlsx');
    }

    public function preview(Request $request): View
    {
        $this->authorize('create', Motorcycle::class);

        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'dup_mode' => ['nullable', 'in:update,skip'],
            'cur_mode' => ['nullable', 'in:create,error'],
            'sr_mode' => ['nullable', 'in:ignore,error'],
        ]);

        $settings = array_filter($request->only('dup_mode', 'cur_mode', 'sr_mode'));

        $path = $data['file']->store('imports');
        $import = new MotorcyclesImport($request->user(), $settings);
        $sheet = Excel::toArray($import, Storage::path($path))[0] ?? [];

        $rows = collect($sheet)->map(fn ($row, $i) => [
            'num' => $i + 2,
            ...$import->evaluateRow((array) $row),
        ]);

        return view('imports.preview', [
            'title' => 'Motorcycles',
            'listUrl' => route('admin.motorcycles.index'),
            'commitUrl' => route('admin.motorcycles.import.commit'),
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
        $this->authorize('create', Motorcycle::class);

        $data = $request->validate([
            'path' => ['required', 'string'],
            'offset' => ['nullable', 'integer', 'min:0'],
            'dup_mode' => ['nullable', 'in:update,skip'],
            'cur_mode' => ['nullable', 'in:create,error'],
            'sr_mode' => ['nullable', 'in:ignore,error'],
        ]);

        $settings = array_filter($request->only('dup_mode', 'cur_mode', 'sr_mode'));

        $path = 'imports/' . basename($data['path']);
        abort_unless(Storage::exists($path), 404);

        $import = new MotorcyclesImport($request->user(), $settings);
        $rows = collect(Excel::toArray($import, Storage::path($path))[0] ?? []);
        $total = $rows->count();
        $offset = (int) ($data['offset'] ?? 0);

        $import->importRows($rows->slice($offset, self::CHUNK)->values(), $offset);

        $processed = min($offset + self::CHUNK, $total);
        $done = $processed >= $total;
        if ($done) {
            Storage::delete($path);
            ActivityLog::record('import', null, "Imported motorcycles sheet — {$import->created} created, {$import->updated} updated", [
                'entity' => 'motorcycles', 'rows' => $total,
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
