<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OfferScope;
use App\Enums\TestRideStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\Motorcycle;
use App\Models\Offer;
use App\Models\Showroom;
use App\Models\TestRideRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        // Reporting window for the test-rides section — defaults to the
        // last 30 days; the breakdowns above it are system-wide.
        $from = $request->date('from') ?? now()->subDays(29)->startOfDay();
        $to = $request->date('to') ?? now()->endOfDay();
        $to = $to->endOfDay();

        $inRange = fn ($q) => $q->whereBetween('test_ride_requests.created_at', [$from, $to]);

        $kpis = [
            'brands' => Brand::count(),
            'models' => Motorcycle::count(),
            'active_models' => Motorcycle::where('is_active', true)->count(),
            'stock' => (int) Motorcycle::sum('quantity'),
            'showrooms' => Showroom::count(),
            'service_centers' => Showroom::where('is_service_center', true)->count(),
            'active_offers' => Offer::active()->count(),
            'rides' => TestRideRequest::count(),
            'pending' => TestRideRequest::where('status', TestRideStatus::Pending)->count(),
            'staff' => User::count(),
            'currencies' => Currency::count(),
            'entries' => ActivityLog::count(),
        ];

        /* ---------- Test rides inside the window ---------- */

        $ridesByStatus = TestRideRequest::select('status', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('status')->pluck('total', 'status');

        $ridesByShowroom = Showroom::withCount(['testRideRequests' => $inRange])
            ->orderByDesc('test_ride_requests_count')->get();

        $ridesByBrand = Brand::withCount(['testRideRequests' => $inRange])
            ->orderByDesc('test_ride_requests_count')->get();

        // Per-day counts across the window for the bar strip.
        $days = max(1, $from->diffInDays($to) + 1);
        $perDay = TestRideRequest::select(DB::raw('date(created_at) as day'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')->pluck('total', 'day');
        $trend = collect(range(0, min($days, 31) - 1))->map(function ($i) use ($from, $perDay) {
            $day = $from->copy()->addDays($i)->toDateString();
            return ['day' => $day, 'total' => (int) ($perDay[$day] ?? 0)];
        });

        /* ---------- Inventory ---------- */

        $inventory = Brand::withCount('motorcycles')
            ->withCount(['motorcycles as active_models' => fn ($q) => $q->where('is_active', true)])
            ->withSum('motorcycles', 'quantity')
            ->orderBy('name')->get();

        $lowStock = Motorcycle::with('brand')
            ->where('quantity', '<=', 3)
            ->orderBy('quantity')->orderBy('name')
            ->limit(8)->get();

        /* ---------- Offers ---------- */

        $offersByScope = Offer::select('scope', DB::raw('count(*) as total'))
            ->groupBy('scope')->pluck('total', 'scope');

        $expiring = Offer::with('brand')
            ->where('is_active', true)
            ->whereNotNull('expires_on')
            ->whereBetween('expires_on', [today(), today()->addDays(14)])
            ->orderBy('expires_on')->limit(8)->get();

        /* ---------- Activity ---------- */

        $recentActivity = ActivityLog::with('user')->latest('created_at')->limit(8)->get();

        $topUsers = ActivityLog::where('created_at', '>=', now()->subDays(7))
            ->whereNotNull('user_id')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')->orderByDesc('total')->limit(5)
            ->pluck('total', 'user_id');
        $topUsers = User::whereIn('id', $topUsers->keys())->pluck('name', 'id')
            ->map(fn ($name, $id) => ['name' => $name, 'total' => $topUsers[$id] ?? 0])
            ->sortByDesc('total')->values();

        return view('reports.index', compact(
            'from', 'to', 'kpis', 'ridesByStatus', 'ridesByShowroom', 'ridesByBrand',
            'trend', 'inventory', 'lowStock', 'offersByScope', 'expiring',
            'recentActivity', 'topUsers',
        ) + [
            'statuses' => TestRideStatus::cases(),
            'scopes' => OfferScope::cases(),
        ]);
    }

    /**
     * CSV download for one report section.
     * ?report=test-rides|inventory|offers|activity
     */
    public function export(Request $request): StreamedResponse
    {
        $report = $request->string('report')->toString();
        abort_unless(in_array($report, ['test-rides', 'inventory', 'offers', 'activity'], true), 404);

        [$headings, $rows] = match ($report) {
            'test-rides' => $this->testRideRows($request),
            'inventory' => $this->inventoryRows(),
            'offers' => $this->offerRows(),
            'activity' => $this->activityRows(),
        };

        ActivityLog::record('export', null, "Exported {$report} report (".count($rows).' rows)', ['report' => $report, 'rows' => count($rows)]);

        $filename = "{$report}-report-".now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($headings, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headings);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function testRideRows(Request $request): array
    {
        $rows = TestRideRequest::with(['motorcycle.brand', 'showroom'])
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->date('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->date('to')))
            ->latest()->get()
            ->map(fn (TestRideRequest $r) => [
                $r->id, $r->name, $r->phone, $r->email,
                $r->motorcycle?->name, $r->motorcycle?->brand?->name,
                $r->showroom?->city.' — '.$r->showroom?->name,
                $r->preferred_date?->toDateString(), $r->status->value,
                $r->created_at->toDateTimeString(),
            ]);

        return [['ID', 'Customer', 'Phone', 'Email', 'Motorcycle', 'Brand', 'Showroom', 'Preferred date', 'Status', 'Requested at'], $rows];
    }

    private function inventoryRows(): array
    {
        $rows = Motorcycle::with(['brand', 'currency', 'showrooms'])
            ->orderBy('brand_id')->orderBy('name')->get()
            ->map(fn (Motorcycle $m) => [
                $m->brand?->name, $m->name, $m->price, $m->currency?->code,
                $m->quantity, $m->is_active ? 'active' : 'inactive',
                $m->showrooms->pluck('city')->join(' / '),
            ]);

        return [['Brand', 'Model', 'Price', 'Currency', 'Units in stock', 'Status', 'Showrooms'], $rows];
    }

    private function offerRows(): array
    {
        $rows = Offer::with('brand', 'motorcycle')->orderBy('scope')->orderByDesc('id')->get()
            ->map(fn (Offer $o) => [
                $o->title, $o->scope->value, $o->targetName(),
                $o->discount_percent ? $o->discount_percent.'%' : '—',
                $o->expires_on?->toDateString() ?? 'no expiry',
                $o->is_active ? 'active' : 'inactive',
            ]);

        return [['Title', 'Scope', 'Applies to', 'Discount', 'Expires', 'Status'], $rows];
    }

    private function activityRows(): array
    {
        $rows = ActivityLog::with('user')->latest('created_at')->limit(2000)->get()
            ->map(fn (ActivityLog $l) => [
                $l->created_at->toDateTimeString(), $l->user?->name ?? 'system',
                $l->action, $l->description,
                implode(', ', $l->changedFields()), $l->ip_address,
            ]);

        return [['When', 'User', 'Action', 'Subject', 'Changed fields', 'IP'], $rows];
    }
}
