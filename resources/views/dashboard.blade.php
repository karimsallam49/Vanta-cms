@extends('layouts.app')

@section('title', 'Dashboard')
@section('crumb', 'Dashboard')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Control Room</div>
    <h1 class="page-title">GROUP <span class="accent">OVERVIEW</span></h1>
  </div>
</div>

<div class="stats">
  <div class="stat" style="--stat-accent:var(--lava)">
    <div class="k">Motorcycles</div>
    <div class="v">{{ $stats['motorcycles'] }}</div>
  </div>
  <div class="stat" style="--stat-accent:var(--amber)">
    <div class="k">Pending Test Rides</div>
    <div class="v">{{ $stats['pending_rides'] }}</div>
  </div>
  <div class="stat" style="--stat-accent:var(--lime)">
    <div class="k">Active Offers</div>
    <div class="v">{{ $stats['offers'] }}</div>
  </div>
  <div class="stat" style="--stat-accent:var(--gold)">
    <div class="k">Showrooms</div>
    <div class="v">{{ $stats['showrooms'] }}</div>
  </div>
</div>

<div class="card">
  <div class="card-head">
    <h3>Latest test ride requests</h3>
    <a class="btn btn-ghost btn-sm" href="{{ route('admin.test-rides.index') }}">View all</a>
  </div>
  @if($recentRides->isEmpty())
    <div class="empty">
      <div class="big">No requests yet</div>
      <p>Test ride bookings from the public site will land here.</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr>
          <th>Customer</th><th>Motorcycle</th><th>Showroom</th><th>Date</th><th>Status</th>
        </tr>
      </thead>
      <tbody>
        @foreach($recentRides as $ride)
        <tr>
          <td>
            <div class="cell-title">{{ $ride->name }}</div>
            <div class="dim">{{ $ride->phone }}</div>
          </td>
          <td>
            <span class="cell-brand">
              <span class="dot" style="background:{{ $ride->motorcycle->brand->accent }}"></span>
              {{ $ride->motorcycle->name }}
            </span>
          </td>
          <td class="dim">{{ $ride->showroom->city }}</td>
          <td class="num">{{ $ride->preferred_date->format('d M Y') }}</td>
          <td><span class="badge {{ $ride->status->tone() }}">{{ $ride->status->label() }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>

<div class="page-head" style="margin-top:1.6rem;margin-bottom:0.9rem">
  <div class="page-sub" style="margin:0">Your scope — {{ $brands->count() }} brand{{ $brands->count() === 1 ? '' : 's' }}</div>
</div>
<div class="grid-cards">
  @foreach($brands as $brand)
  <div class="brand-card">
    <span class="stripe" style="background:{{ $brand->accent }}"></span>
    <h3>{{ $brand->name }}</h3>
    <div class="seg">{{ $brand->segment }} — {{ $brand->origin }}</div>
    <div class="meta"><span><span class="num">{{ $brand->motorcycles_count }}</span> models in range</span></div>
  </div>
  @endforeach
</div>
@endsection
