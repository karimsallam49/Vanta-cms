<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CurrencyRequest;
use App\Models\Currency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CurrencyController extends Controller
{
    // Routes are wrapped in the `admin` middleware — pricing currencies
    // are group-level configuration, managed by administrators only.

    public function index(Request $request): View
    {
        $currencies = Currency::withCount('motorcycles')
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($qq) => $qq
                ->where('code', 'like', "%{$request->q}%")
                ->orWhere('name', 'like', "%{$request->q}%")))
            ->orderBy('code')
            ->paginate(15)
            ->withQueryString();

        return view('currencies.index', compact('currencies'));
    }

    public function create(): View
    {
        return view('currencies.form', ['currency' => new Currency()]);
    }

    public function store(CurrencyRequest $request): RedirectResponse
    {
        Currency::create($request->validated());

        return redirect()->route('admin.currencies.index')
            ->with('flash', ['type' => 'success', 'message' => 'Currency created.']);
    }

    public function edit(Currency $currency): View
    {
        return view('currencies.form', compact('currency'));
    }

    public function update(CurrencyRequest $request, Currency $currency): RedirectResponse
    {
        $currency->update($request->validated());

        return redirect()->route('admin.currencies.index')
            ->with('flash', ['type' => 'success', 'message' => 'Currency updated.']);
    }

    public function destroy(Currency $currency): RedirectResponse
    {
        if ($currency->motorcycles()->exists()) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Cannot delete — motorcycles are priced in this currency.',
            ]);
        }

        $currency->delete();

        return back()->with('flash', ['type' => 'success', 'message' => 'Currency deleted.']);
    }
}
