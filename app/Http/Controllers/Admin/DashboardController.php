<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TestRideStatus;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Motorcycle;
use App\Models\Offer;
use App\Models\Showroom;
use App\Models\TestRideRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'motorcycles' => Motorcycle::visibleTo($user)->count(),
            'offers' => Offer::visibleTo($user)->where('is_active', true)->count(),
            'pending_rides' => TestRideRequest::visibleTo($user)
                ->where('status', TestRideStatus::Pending)->count(),
            'showrooms' => Showroom::count(),
        ];

        $recentRides = TestRideRequest::visibleTo($user)
            ->with(['motorcycle.brand', 'showroom'])
            ->latest()
            ->limit(6)
            ->get();

        $brands = $user->isAdmin()
            ? Brand::withCount('motorcycles')->get()
            : $user->brands()->withCount('motorcycles')->get();

        return view('dashboard', compact('stats', 'recentRides', 'brands'));
    }
}
