<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(Request $request): View
    {
        $brands = Brand::withCount([
                'motorcycles',
                'offers',
                'users',
                'testRideRequests as pending_rides_count' => fn ($q) => $q->where('status', 'pending'),
            ])
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($qq) => $qq
                ->where('name', 'like', "%{$request->q}%")
                ->orWhere('name_ar', 'like', "%{$request->q}%")
                ->orWhere('origin', 'like', "%{$request->q}%")))
            ->when($request->filled('segment'), fn ($q) => $q->where('segment', $request->string('segment')))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('brands.form', ['brand' => new Brand()]);
    }

    public function store(BrandRequest $request): RedirectResponse
    {
        Brand::create($request->validated());

        return redirect()->route('admin.brands.index')
            ->with('flash', ['type' => 'success', 'message' => 'Brand created.']);
    }

    public function edit(Brand $brand): View
    {
        return view('brands.form', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand): RedirectResponse
    {
        $brand->update($request->validated());

        return redirect()->route('admin.brands.index')
            ->with('flash', ['type' => 'success', 'message' => 'Brand updated.']);
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->motorcycles()->exists()) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Cannot delete — motorcycles belong to this brand.',
            ]);
        }

        $brand->delete();

        return back()->with('flash', ['type' => 'success', 'message' => 'Brand deleted.']);
    }
}
