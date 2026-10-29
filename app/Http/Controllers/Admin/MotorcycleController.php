<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MotorcycleRequest;
use App\Models\Brand;
use App\Models\Motorcycle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MotorcycleController extends Controller
{
    public function index(Request $request): View
    {
        $motorcycles = Motorcycle::visibleTo($request->user())
            ->with('brand')
            ->withCount('testRideRequests')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('motorcycles.index', compact('motorcycles'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Motorcycle::class);

        return view('motorcycles.form', [
            'motorcycle' => new Motorcycle(),
            'brands' => $this->manageableBrands($request->user()),
        ]);
    }

    public function store(MotorcycleRequest $request): RedirectResponse
    {
        $this->authorize('create', Motorcycle::class);

        Motorcycle::create($request->validated());

        return redirect()->route('admin.motorcycles.index')
            ->with('flash', ['type' => 'success', 'message' => 'Motorcycle created.']);
    }

    public function edit(Request $request, Motorcycle $motorcycle): View
    {
        $this->authorize('update', $motorcycle);

        return view('motorcycles.form', [
            'motorcycle' => $motorcycle,
            'brands' => $this->manageableBrands($request->user()),
        ]);
    }

    public function update(MotorcycleRequest $request, Motorcycle $motorcycle): RedirectResponse
    {
        $this->authorize('update', $motorcycle);

        $motorcycle->update($request->validated());

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
