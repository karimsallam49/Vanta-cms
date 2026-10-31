<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MotorcycleRequest;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\Motorcycle;
use App\Models\MotorcycleImage;
use App\Models\Showroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MotorcycleController extends Controller
{
    public function index(Request $request): View
    {
        $motorcycles = Motorcycle::visibleTo($request->user())
            ->with(['brand', 'currency', 'images'])
            ->withCount('testRideRequests')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($qq) => $qq
                ->where('name', 'like', "%{$request->q}%")
                ->orWhere('name_ar', 'like', "%{$request->q}%")
                ->orWhere('tagline', 'like', "%{$request->q}%")))
            ->when($request->filled('brand_id'), fn ($q) => $q->where('brand_id', $request->integer('brand_id')))
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', (bool) $request->integer('status')))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('motorcycles.index', [
            'motorcycles' => $motorcycles,
            'brands' => $this->manageableBrands($request->user()),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Motorcycle::class);

        return view('motorcycles.form', [
            'motorcycle' => new Motorcycle(),
            'brands' => $this->manageableBrands($request->user()),
            'currencies' => Currency::orderBy('code')->get(),
            'showrooms' => Showroom::orderBy('country')->orderBy('city')->get(),
        ]);
    }

    public function store(MotorcycleRequest $request): RedirectResponse
    {
        $this->authorize('create', Motorcycle::class);

        $motorcycle = Motorcycle::create($request->safe()->except(['images', 'new_images', 'showrooms']));
        $motorcycle->showrooms()->sync($request->input('showrooms', []));
        $this->syncImages($request, $motorcycle);

        return redirect()->route('admin.motorcycles.index')
            ->with('flash', ['type' => 'success', 'message' => 'Motorcycle created.']);
    }

    public function edit(Request $request, Motorcycle $motorcycle): View
    {
        $this->authorize('update', $motorcycle);

        return view('motorcycles.form', [
            'motorcycle' => $motorcycle->load('showrooms'),
            'brands' => $this->manageableBrands($request->user()),
            'currencies' => Currency::orderBy('code')->get(),
            'showrooms' => Showroom::orderBy('country')->orderBy('city')->get(),
        ]);
    }

    public function update(MotorcycleRequest $request, Motorcycle $motorcycle): RedirectResponse
    {
        $this->authorize('update', $motorcycle);

        $motorcycle->update($request->safe()->except(['images', 'new_images', 'showrooms']));
        $motorcycle->showrooms()->sync($request->input('showrooms', []));
        $this->syncImages($request, $motorcycle);

        return redirect()->route('admin.motorcycles.index')
            ->with('flash', ['type' => 'success', 'message' => 'Motorcycle updated.']);
    }

    public function destroy(Request $request, Motorcycle $motorcycle): RedirectResponse
    {
        $this->authorize('delete', $motorcycle);

        $motorcycle->delete();

        return back()->with('flash', ['type' => 'success', 'message' => 'Motorcycle deleted.']);
    }

    /**
     * Gallery sync: apply per-image edits/deletes, store new uploads,
     * then normalize sort_order to a clean 0..n sequence.
     */
    private function syncImages(MotorcycleRequest $request, Motorcycle $motorcycle): void
    {
        foreach ((array) $request->input('images', []) as $id => $meta) {
            /** @var MotorcycleImage|null $image */
            $image = $motorcycle->images()->whereKey((int) $id)->first();
            if (! $image) {
                continue;
            }

            if (! empty($meta['delete'])) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
                continue;
            }

            $image->update([
                'color_name' => $meta['color_name'] ?? null,
                'color_hex' => $meta['color_hex'] ?? null,
                'sort_order' => (int) ($meta['sort'] ?? $image->sort_order),
            ]);
        }

        $base = (int) $motorcycle->images()->max('sort_order') + 1;
        foreach ((array) $request->file('new_images', []) as $i => $meta) {
            $file = $meta['file'] ?? null;
            if (! $file) {
                continue;
            }

            $motorcycle->images()->create([
                'path' => $file->store("motorcycles/{$motorcycle->id}", 'public'),
                'color_name' => $meta['color_name'] ?? null,
                'color_hex' => $meta['color_hex'] ?? null,
                'sort_order' => $base + (int) ($meta['sort'] ?? $i),
            ]);
        }

        $motorcycle->images()->orderBy('sort_order')->get()
            ->each(fn (MotorcycleImage $image, int $index) => $image->update(['sort_order' => $index]));

        // Paint swatches are derived from named image colors — the gallery
        // is the single source of truth for what the site shows.
        if ($motorcycle->images()->exists()) {
            $motorcycle->update([
                'colors' => $motorcycle->images()
                    ->whereNotNull('color_name')
                    ->where('color_name', '!=', '')
                    ->orderBy('sort_order')
                    ->get()
                    ->map(fn (MotorcycleImage $image) => ['name' => $image->color_name, 'hex' => $image->color_hex])
                    ->values()
                    ->all(),
            ]);
        }
    }

    /**
     * Only brands the user manages — data-entry users never see
     * brands outside their assignment in the dropdown.
     */
    private function manageableBrands($user)
    {
        return $user->isAdmin()
            ? Brand::orderBy('name')->get()
            : $user->brands()->orderBy('name')->get();
    }
}
