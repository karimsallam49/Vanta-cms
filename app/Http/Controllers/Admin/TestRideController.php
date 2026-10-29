<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TestRideStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestRideUpdateRequest;
use App\Models\TestRideRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestRideController extends Controller
{
    public function index(Request $request): View
    {
        $rides = TestRideRequest::visibleTo($request->user())
            ->with(['motorcycle.brand', 'showroom'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('test-rides.index', [
            'rides' => $rides,
            'statuses' => TestRideStatus::cases(),
            'activeStatus' => $request->query('status'),
        ]);
    }

    public function update(TestRideUpdateRequest $request, TestRideRequest $testRide): RedirectResponse
    {
        $testRide->update($request->validated());

        return back()->with('flash', ['type' => 'success', 'message' => 'Request updated.']);
    }
}
