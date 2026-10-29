<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Models\Brand;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(Request $request): View
    {
        $offers = Offer::visibleTo($request->user())
            ->with('brand')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('offers.index', compact('offers'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Offer::class);

        return view('offers.form', [
            'offer' => new Offer(['is_active' => true]),
            'brands' => $this->manageableBrands($request->user()),
        ]);
    }

    public function store(OfferRequest $request): RedirectResponse
    {
        $this->authorize('create', Offer::class);

        Offer::create($request->validated());

        return redirect()->route('admin.offers.index')
            ->with('flash', ['type' => 'success', 'message' => 'Offer created.']);
    }

    public function edit(Request $request, Offer $offer): View
    {
        $this->authorize('update', $offer);

        return view('offers.form', [
            'offer' => $offer,
            'brands' => $this->manageableBrands($request->user()),
        ]);
    }

    public function update(OfferRequest $request, Offer $offer): RedirectResponse
    {
        $this->authorize('update', $offer);

        $offer->update($request->validated());

        return redirect()->route('admin.offers.index')
            ->with('flash', ['type' => 'success', 'message' => 'Offer updated.']);
    }

    public function destroy(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorize('delete', $offer);

        $offer->delete();

        return back()->with('flash', ['type' => 'success', 'message' => 'Offer deleted.']);
    }

    private function manageableBrands($user)
    {
        return $user->isAdmin()
            ? Brand::orderBy('name')->get()
            : $user->brands()->orderBy('name')->get();
    }
}
