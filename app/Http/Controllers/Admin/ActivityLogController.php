<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\Motorcycle;
use App\Models\MotorcycleImage;
use App\Models\Offer;
use App\Models\Showroom;
use App\Models\SiteSection;
use App\Models\TestRideRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    /**
     * Entity types that can appear as a log subject — the filter maps the
     * short key to the stored morph class.
     */
    private const SUBJECT_TYPES = [
        'brand' => Brand::class,
        'motorcycle' => Motorcycle::class,
        'motorcycle_image' => MotorcycleImage::class,
        'showroom' => Showroom::class,
        'offer' => Offer::class,
        'currency' => Currency::class,
        'test_ride' => TestRideRequest::class,
        'site_section' => SiteSection::class,
        'user' => User::class,
    ];

    public function index(Request $request): View
    {
        $logs = ActivityLog::with('user')
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->integer('user_id')))
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->string('action')->toString()))
            ->when($request->filled('type') && isset(self::SUBJECT_TYPES[$request->type]),
                fn ($q) => $q->where('subject_type', self::SUBJECT_TYPES[$request->type]))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->when($request->filled('q'), fn ($q) => $q->where('description', 'like', '%'.$request->string('q').'%'))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('activity.index', [
            'logs' => $logs,
            'users' => User::orderBy('name')->pluck('name', 'id'),
            'actions' => ['created', 'updated', 'deleted', 'login', 'logout', 'import', 'export'],
            'types' => collect(self::SUBJECT_TYPES)->mapWithKeys(fn ($class, $key) => [$key => Str::headline($key)]),
        ]);
    }
}
