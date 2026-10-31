<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSection;
use App\Support\SiteContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SiteContentController extends Controller
{
    // Routes are wrapped in the `admin` middleware — site chrome is
    // administrator territory, separate from brand-scoped content.

    public function index(): View
    {
        $sections = collect(SiteContent::sections())
            ->map(function (array $def, string $key) {
                $row = SiteSection::where('key', $key)->first();
                $payload = $row?->payload ?? [];

                return [
                    'key' => $key,
                    'label' => $def['label'],
                    'group' => $def['group'],
                    'fields' => $def['fields'],
                    'payload' => $payload,
                    'filled' => collect($def['fields'])
                        ->keys()
                        ->filter(fn ($f) => ! empty($payload[$f]))
                        ->count(),
                    'thumb' => collect($def['fields'])
                        ->keys()
                        ->filter(fn ($f) => $def['fields'][$f][0] === 'image')
                        ->map(fn ($f) => $payload[$f] ?? null)
                        ->filter()
                        ->first(),
                    'updated_at' => $row?->updated_at,
                ];
            })
            ->groupBy('group');

        return view('content.index', ['groups' => $sections]);
    }

    public function edit(string $key): View
    {
        $def = SiteContent::sections()[$key] ?? abort(404);
        $payload = SiteSection::where('key', $key)->value('payload') ?? [];

        return view('content.edit', [
            'sectionKey' => $key,
            'def' => $def,
            'payload' => $payload,
        ]);
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $def = SiteContent::sections()[$key] ?? abort(404);

        $data = $request->validate(SiteContent::rulesFor($def['fields']));

        $payload = SiteSection::where('key', $key)->value('payload') ?? [];

        foreach ($def['fields'] as $name => [$type]) {
            if ($type === 'image') {
                if ($request->boolean("{$name}_remove")) {
                    $this->deleteImage($payload[$name] ?? null);
                    $payload[$name] = null;
                }
                if ($request->hasFile($name)) {
                    $this->deleteImage($payload[$name] ?? null);
                    $payload[$name] = $request->file($name)->store('site', 'public');
                }
            } else {
                $payload[$name] = $data[$name] ?? null;
                if (SiteContent::localizable($type)) {
                    $payload["{$name}_ar"] = $data["{$name}_ar"] ?? null;
                }
            }
        }

        SiteSection::updateOrCreate(['key' => $key], ['payload' => $payload]);

        return redirect()->route('admin.content.index')
            ->with('flash', ['type' => 'success', 'message' => $def['label'].' saved.']);
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
