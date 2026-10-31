<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TestRideStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\TestRideUpdateRequest;
use App\Mail\TestRideStatusUpdated;
use App\Models\ActivityLog;
use App\Models\Motorcycle;
use App\Models\Showroom;
use App\Models\TestRideRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class TestRideController extends Controller
{
    public function index(Request $request): View
    {
        $rides = TestRideRequest::visibleTo($request->user())
            ->with(['motorcycle.brand', 'showroom'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('showroom_id'), fn ($q) => $q->where('showroom_id', $request->integer('showroom_id')))
            ->when($request->filled('motorcycle_id'), fn ($q) => $q->where('motorcycle_id', $request->integer('motorcycle_id')))
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('preferred_date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('preferred_date', '<=', $request->date('date_to')))
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($qq) => $qq
                ->where('name', 'like', "%{$request->q}%")
                ->orWhere('phone', 'like', "%{$request->q}%")
                ->orWhere('email', 'like', "%{$request->q}%")))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $motorcycles = Motorcycle::visibleTo($request->user())
            ->with('brand')
            ->orderBy('name')
            ->get();

        return view('test-rides.index', [
            'rides' => $rides,
            'statuses' => TestRideStatus::cases(),
            'showrooms' => Showroom::orderBy('country')->orderBy('city')->get(),
            'motorcycles' => $motorcycles,
            'activeStatus' => $request->query('status'),
        ]);
    }

    public function show(TestRideRequest $testRide): View
    {
        $this->authorize('view', $testRide);

        $testRide->load(['motorcycle.brand', 'motorcycle.images', 'showroom']);

        $timeline = ActivityLog::where('subject_type', $testRide->getMorphClass())
            ->where('subject_id', $testRide->getKey())
            ->with('user')
            ->latest('created_at')
            ->limit(20)
            ->get();

        return view('test-rides.show', [
            'ride' => $testRide,
            'statuses' => TestRideStatus::cases(),
            'timeline' => $timeline,
        ]);
    }

    /**
     * Live count for the sidebar badge — polled by the layout script
     * so staff see new requests land without refreshing.
     */
    public function pendingCount(Request $request): JsonResponse
    {
        return response()->json([
            'count' => TestRideRequest::visibleTo($request->user())
                ->where('status', TestRideStatus::Pending)
                ->count(),
        ]);
    }

    public function update(TestRideUpdateRequest $request, TestRideRequest $testRide): RedirectResponse
    {
        $oldStatus = $testRide->status;
        $testRide->update($request->validated());

        $notified = false;
        if ($testRide->status !== $oldStatus && $testRide->email) {
            try {
                Mail::to($testRide->email)->send(new TestRideStatusUpdated($testRide));
                $notified = true;
            } catch (Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('admin.test-rides.show', $testRide)
            ->with('flash', ['type' => 'success', 'message' => $notified
                ? __('Request updated — customer notified by email.')
                : __('Request updated.')]);
    }
}
