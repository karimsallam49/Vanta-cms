<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Enums\OfferScope;
use App\Models\Brand;
use App\Models\Motorcycle;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(Request $request): View
    {
        $offers = Offer::visibleTo($request->user())
            ->with('brand', 'motorcycle')
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', "%{$request->q}%"))
            ->when($request->filled('brand_id'), fn ($q) => $q->where('brand_id', $request->integer('brand_id')))
            ->when($request->filled('scope'), fn ($q) => $q->where('scope', $request->query('scope')))
            ->when($request->query('status') === 'active', fn ($q) => $q->active())
            ->when($request->query('status') === 'expired', fn ($q) => $q->whereNotNull('expires_on')->where('expires_on', '<', today()))
            ->when($request->query('status') === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('offers.index', [
            'offers' => $offers,
            'brands' => $this->manageableBrands($request->user()),
            'scopes' => OfferScope::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Offer::class);

        return view('offers.form', [
            'offer' => new Offer(['is_active' => true, 'scope' => OfferScope::Brand]),
            'brands' => $this->manageableBrands($request->user()),
            'motorcycles' => $this->manageableMotorcycles($request->user()),
            'scopes' => OfferScope::cases(),
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
            'motorcycles' => $this->manageableMotorcycles($request->user()),
            'scopes' => OfferScope::cases(),
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

    private function manageableMotorcycles($user)
    {
        return Motorcycle::visibleTo($user)->with('brand')->orderBy('name')->get();
    }
}
