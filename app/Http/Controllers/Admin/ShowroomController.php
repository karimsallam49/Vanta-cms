<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowroomRequest;
use App\Models\Showroom;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ShowroomController extends Controller
{
    // Routes are wrapped in the `admin` middleware — group facilities
    // are managed by administrators only.

    public function index(): View
    {
        $showrooms = Showroom::orderBy('country')->orderBy('city')
            ->paginate(15)
            ->withQueryString();

        return view('showrooms.index', compact('showrooms'));
    }

    public function create(): View
    {
        return view('showrooms.form', ['showroom' => new Showroom()]);
    }

    public function store(ShowroomRequest $request): RedirectResponse
    {
        Showroom::create($request->validated());

        return redirect()->route('admin.showrooms.index')
            ->with('flash', ['type' => 'success', 'message' => 'Showroom created.']);
    }

    public function edit(Showroom $showroom): View
    {
        return view('showrooms.form', compact('showroom'));
    }

    public function update(ShowroomRequest $request, Showroom $showroom): RedirectResponse
    {
        $showroom->update($request->validated());

        return redirect()->route('admin.showrooms.index')
            ->with('flash', ['type' => 'success', 'message' => 'Showroom updated.']);
    }

    public function destroy(Showroom $showroom): RedirectResponse
    {
        if ($showroom->testRideRequests()->exists()) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Cannot delete — test ride requests are linked to this showroom.',
            ]);
        }

        $showroom->delete();

        return back()->with('flash', ['type' => 'success', 'message' => 'Showroom deleted.']);
    }
}
