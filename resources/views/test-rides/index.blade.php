@extends('layouts.app')

@section('title', 'Test Rides')
@section('crumb', 'Test Rides')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Customer requests') }}</div>
    <h1 class="page-title">{{ __('TEST') }} <span class="accent">{{ __('RIDES') }}</span></h1>
  </div>
</div>

@include('partials.filter-bar', [
  'search'  => 'Customer name, phone or email…',
  'selects' => [
    ['name' => 'status',        'label' => 'Any status',    'options' => collect($statuses)->mapWithKeys(fn ($s) => [$s->value => __($s->label())])],
    ['name' => 'showroom_id',   'label' => 'All regions',   'options' => $showrooms->mapWithKeys(fn ($s) => [$s->id => "{$s->city} — {$s->name}"])],
    ['name' => 'motorcycle_id', 'label' => 'Any model',     'options' => $motorcycles->mapWithKeys(fn ($m) => [$m->id => "{$m->brand->name} {$m->name}"])],
  ],
  'dates'   => [
    ['name' => 'date_from', 'label' => 'From date'],
    ['name' => 'date_to',   'label' => 'To date'],
  ],
])

<div class="card">
  @if($rides->isEmpty())
    <div class="empty">
      <div class="big">{{ __('No requests') }}</div>
      <p>{{ $activeStatus ? __('Nothing with this status — try another filter.') : __('Test ride bookings will land here.') }}</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr>
          <th>{{ __('Customer') }}</th>
          <th>{{ __('Motorcycle') }}</th>
          <th>{{ __('Showroom') }}</th>
          <th>{{ __('Preferred date') }}</th>
          <th>{{ __('Received') }}</th>
          <th>{{ __('Status') }}</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($rides as $ride)
        <tr>
          <td>
            <div class="cell-title">{{ $ride->name }}</div>
            <div class="dim">{{ $ride->phone }}{{ $ride->email ? ' · '.$ride->email : '' }}</div>
          </td>
          <td>
            <span class="cell-brand">
              <span class="dot" style="background:{{ $ride->motorcycle->brand->accent }}"></span>
              {{ $ride->motorcycle->name }}
            </span>
          </td>
          <td class="dim">{{ $ride->showroom->city }} — {{ $ride->showroom->name }}</td>
          <td class="num">{{ $ride->preferred_date->format('d M Y') }}</td>
          <td class="dim num">{{ $ride->created_at->format('d M') }}</td>
          <td><span class="badge {{ $ride->status->tone() }}">{{ __($ride->status->label()) }}</span></td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="{{ route('admin.test-rides.show', $ride) }}">{{ __('Open') }}</a>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-bar">
      <span>{{ __(':count requests', ['count' => $rides->total()]) }}</span>
      {{ $rides->links() }}
    </div>
  @endif
</div>
@endsection
