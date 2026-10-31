@extends('layouts.app')

@section('title', 'Reports')
@section('crumb', 'Reports')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Everything, measured') }}</div>
    <h1 class="page-title">{{ __('SYSTEM') }} <span class="accent">{{ __('REPORTS') }}</span></h1>
  </div>
</div>

{{-- ---------- Whole-system KPIs ---------- --}}
<div class="stats">
  <div class="stat" style="--stat-accent:var(--bone)"><div class="k">{{ __('Brands') }}</div><div class="v">{{ $kpis['brands'] }}</div></div>
  <div class="stat" style="--stat-accent:var(--bone)"><div class="k">{{ __('Models') }}</div><div class="v">{{ $kpis['models'] }} <span class="dim" style="font-size:0.62rem">{{ $kpis['active_models'] }} {{ __('live') }}</span></div></div>
  <div class="stat" style="--stat-accent:var(--ok)"><div class="k">{{ __('Units in stock') }}</div><div class="v">{{ $kpis['stock'] }}</div></div>
  <div class="stat" style="--stat-accent:var(--bone)"><div class="k">{{ __('Showrooms') }}</div><div class="v">{{ $kpis['showrooms'] }} <span class="dim" style="font-size:0.62rem">{{ $kpis['service_centers'] }} {{ __('service') }}</span></div></div>
  <div class="stat" style="--stat-accent:var(--ok)"><div class="k">{{ __('Active offers') }}</div><div class="v">{{ $kpis['active_offers'] }}</div></div>
  <div class="stat" style="--stat-accent:var(--lava)"><div class="k">{{ __('Test rides — pending') }}</div><div class="v">{{ $kpis['pending'] }} <span class="dim" style="font-size:0.62rem">/ {{ $kpis['rides'] }}</span></div></div>
  <div class="stat" style="--stat-accent:var(--smoke)"><div class="k">{{ __('Staff') }}</div><div class="v">{{ $kpis['staff'] }}</div></div>
  <div class="stat" style="--stat-accent:var(--smoke)"><div class="k">{{ __('Log entries') }}</div><div class="v">{{ $kpis['entries'] }}</div></div>
</div>

{{-- ---------- Test rides ---------- --}}
<div class="card">
  <div class="card-head">
    <h3>{{ __('Test ride requests') }} <span class="dim">{{ $from->format('d M') }} → {{ $to->format('d M Y') }}</span></h3>
    <div style="display:flex;gap:0.6rem;align-items:center">
      <form class="filter-bar" method="GET" style="margin:0">
        <input class="input" type="date" name="from" value="{{ $from->toDateString() }}">
        <input class="input" type="date" name="to" value="{{ $to->toDateString() }}">
        <noscript><button class="btn btn-ghost btn-sm" type="submit">{{ __('Apply') }}</button></noscript>
      </form>
      <a class="btn btn-ghost btn-sm" href="{{ route('admin.reports.export', ['report' => 'test-rides', 'from' => $from->toDateString(), 'to' => $to->toDateString()]) }}">{{ __('Export CSV') }}</a>
    </div>
  </div>

  <div class="report-grid">
    <div>
      <div class="report-sub">{{ __('By status') }}</div>
      <div style="display:flex;flex-wrap:wrap;gap:0.5rem">
        @foreach($statuses as $s)
          <span class="badge {{ $s->tone() }}">{{ __($s->label()) }} — {{ (int) ($ridesByStatus[$s->value] ?? 0) }}</span>
        @endforeach
      </div>

      <div class="report-sub" style="margin-top:1.4rem">{{ __('Requests per day') }}</div>
      <div class="bars" dir="ltr">
        @php $peak = max(1, $trend->max('total')); @endphp
        @foreach($trend as $d)
          <div class="bar-col" title="{{ $d['day'] }} — {{ $d['total'] }}">
            <div class="bar" style="height:{{ max(3, round($d['total'] / $peak * 100)) }}%"></div>
          </div>
        @endforeach
      </div>
      <div class="dim" style="font-size:0.66rem;display:flex;justify-content:space-between" dir="ltr">
        <span>{{ $trend->first()['day'] ?? '' }}</span><span>{{ $trend->last()['day'] ?? '' }}</span>
      </div>
    </div>

    <div>
      <div class="report-sub">{{ __('By showroom') }}</div>
      <table class="table">
        <tbody>
          @foreach($ridesByShowroom as $sr)
          <tr>
            <td>{{ $sr->city }} <span class="dim">— {{ $sr->name }}</span></td>
            <td class="num">{{ $sr->test_ride_requests_count }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div>
      <div class="report-sub">{{ __('By brand') }}</div>
      <table class="table">
        <tbody>
          @foreach($ridesByBrand as $b)
          <tr>
            <td><span class="cell-brand"><span class="dot" style="background:{{ $b->accent }}"></span>{{ $b->name }}</span></td>
            <td class="num">{{ $b->test_ride_requests_count }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ---------- Inventory ---------- --}}
<div class="report-grid">
  <div class="card" style="margin:0">
    <div class="card-head">
      <h3>{{ __('Inventory by brand') }}</h3>
      <a class="btn btn-ghost btn-sm" href="{{ route('admin.reports.export', 'report=inventory') }}">{{ __('Export CSV') }}</a>
    </div>
    <table class="table">
      <thead><tr><th>{{ __('Brand') }}</th><th>{{ __('Models') }}</th><th>{{ __('Live') }}</th><th>{{ __('Units') }}</th></tr></thead>
      <tbody>
        @foreach($inventory as $b)
        <tr>
          <td><span class="cell-brand"><span class="dot" style="background:{{ $b->accent }}"></span>{{ $b->name }}</span></td>
          <td class="num">{{ $b->motorcycles_count }}</td>
          <td class="num">{{ $b->active_models }}</td>
          <td class="num">{{ (int) $b->motorcycles_sum_quantity }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="card" style="margin:0">
    <div class="card-head"><h3>{{ __('Low stock') }} <span class="dim">{{ __('≤ 3 units') }}</span></h3></div>
    @if($lowStock->isEmpty())
      <div class="empty"><div class="big">{{ __('All stocked') }}</div><p>{{ __('No model is at 3 units or fewer.') }}</p></div>
    @else
      <table class="table">
        <tbody>
          @foreach($lowStock as $m)
          <tr>
            <td>{{ $m->name }} <span class="dim">— {{ $m->brand?->name }}</span></td>
            <td><span class="badge {{ $m->quantity === 0 ? 'red' : '' }}">{{ $m->quantity === 0 ? __('Out of stock') : $m->quantity.' '.__('left') }}</span></td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>

{{-- ---------- Offers ---------- --}}
<div class="report-grid">
  <div class="card" style="margin:0">
    <div class="card-head">
      <h3>{{ __('Offers by scope') }}</h3>
      <a class="btn btn-ghost btn-sm" href="{{ route('admin.reports.export', 'report=offers') }}">{{ __('Export CSV') }}</a>
    </div>
    <div style="display:flex;flex-wrap:wrap;gap:0.5rem;padding:0.4rem 0">
      @foreach($scopes as $s)
        <span class="badge">{{ __($s->label()) }} — {{ (int) ($offersByScope[$s->value] ?? 0) }}</span>
      @endforeach
    </div>
  </div>

  <div class="card" style="margin:0">
    <div class="card-head"><h3>{{ __('Expiring within 14 days') }}</h3></div>
    @if($expiring->isEmpty())
      <div class="empty"><div class="big">{{ __('Nothing expiring') }}</div><p>{{ __('No active offer ends in the next two weeks.') }}</p></div>
    @else
      <table class="table">
        <tbody>
          @foreach($expiring as $o)
          <tr>
            <td>{{ $o->title }} <span class="dim">— {{ $o->targetName() }}</span></td>
            <td class="num">{{ $o->expires_on->format('d M') }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>
</div>

{{-- ---------- Activity ---------- --}}
<div class="report-grid">
  <div class="card" style="margin:0">
    <div class="card-head">
      <h3>{{ __('Most active — last 7 days') }}</h3>
      <a class="btn btn-ghost btn-sm" href="{{ route('admin.reports.export', 'report=activity') }}">{{ __('Export CSV') }}</a>
    </div>
    @if($topUsers->isEmpty())
      <div class="empty"><div class="big">{{ __('Quiet week') }}</div><p>{{ __('No activity recorded in the last 7 days.') }}</p></div>
    @else
      <table class="table">
        <tbody>
          @foreach($topUsers as $u)
          <tr><td>{{ $u['name'] }}</td><td class="num">{{ $u['total'] }} {{ __('actions') }}</td></tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  <div class="card" style="margin:0">
    <div class="card-head">
      <h3>{{ __('Latest activity') }}</h3>
      <a class="btn btn-ghost btn-sm" href="{{ route('admin.activity.index') }}">{{ __('View all') }}</a>
    </div>
    <table class="table">
      <tbody>
        @forelse($recentActivity as $log)
        <tr>
          <td class="dim" style="white-space:nowrap">{{ $log->created_at->format('d M H:i') }}</td>
          <td class="dim">{{ $log->user?->name ?? __('System') }}</td>
          <td><span class="badge {{ $log->tone() }}">{{ __(Str::headline($log->action)) }}</span></td>
          <td class="dim">{{ $log->description }}</td>
        </tr>
        @empty
        <tr><td class="dim">{{ __('Nothing logged yet.') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<script>
  document.querySelectorAll('.filter-bar input[type="date"]').forEach(d =>
    d.addEventListener('change', () => d.form.submit()));
</script>
@endsection
