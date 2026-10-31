@extends('layouts.app')

@section('title', 'Dashboard')
@section('crumb', 'Dashboard')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Control Room') }}</div>
    <h1 class="page-title">{{ __('GROUP') }} <span class="accent">{{ __('OVERVIEW') }}</span></h1>
  </div>
</div>

<div class="stats">
  <div class="stat" style="--stat-accent:var(--bone)">
    <div class="k">{{ __('Motorcycles') }}</div>
    <div class="v">{{ $stats['motorcycles'] }}</div>
  </div>
  <div class="stat" style="--stat-accent:var(--lava)">
    <div class="k">{{ __('Pending Test Rides') }}</div>
    <div class="v">{{ $stats['pending_rides'] }}</div>
  </div>
  <div class="stat" style="--stat-accent:var(--ok)">
    <div class="k">{{ __('Active Offers') }}</div>
    <div class="v">{{ $stats['offers'] }}</div>
  </div>
  <div class="stat" style="--stat-accent:var(--smoke)">
    <div class="k">{{ __('Showrooms') }}</div>
    <div class="v">{{ $stats['showrooms'] }}</div>
  </div>
</div>

<div class="card">
  <div class="card-head">
    <h3>{{ __('Latest test ride requests') }}</h3>
    <a class="btn btn-ghost btn-sm" href="{{ route('admin.test-rides.index') }}">{{ __('View all') }}</a>
  </div>
  @if($recentRides->isEmpty())
    <div class="empty">
      <div class="big">{{ __('No requests yet') }}</div>
      <p>{{ __('Test ride bookings from the public site will land here.') }}</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr>
          <th>{{ __('Customer') }}</th><th>{{ __('Motorcycle') }}</th><th>{{ __('Showroom') }}</th><th>{{ __('Date') }}</th><th>{{ __('Status') }}</th>
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
          <td><span class="badge {{ $ride->status->tone() }}">{{ __($ride->status->label()) }}</span></td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>

<div class="page-head" style="margin-top:1.6rem;margin-bottom:0.9rem">
  <div class="page-sub" style="margin:0">{{ __('Your scope') }} — {{ $brands->count() }} {{ __('brands') }}</div>
</div>
<div class="grid-cards">
  @foreach($brands as $brand)
  <div class="brand-card">
    <span class="stripe" style="background:{{ $brand->accent }}"></span>
    <h3>{{ $brand->name }}</h3>
    <div class="seg">{{ $brand->segment }} — {{ $brand->origin }}</div>
    <div class="meta"><span><span class="num">{{ $brand->motorcycles_count }}</span> {{ __('models in range') }}</span></div>
  </div>
  @endforeach
</div>
@endsection
