@extends('layouts.app')

@section('title', 'Motorcycles')
@section('crumb', 'Motorcycles')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Catalogue') }}</div>
    <h1 class="page-title">{{ __('MOTOR') }}<span class="accent">{{ __('CYCLES') }}</span></h1>
  </div>
  @can('create', App\Models\Motorcycle::class)
    <div style="display:flex;gap:0.6rem">
      <a class="btn btn-ghost" href="{{ route('admin.motorcycles.export') }}">{{ __('Export') }}</a>
      <a class="btn btn-ghost" href="{{ route('admin.motorcycles.import.create') }}">{{ __('Bulk import') }}</a>
      <a class="btn btn-primary" href="{{ route('admin.motorcycles.create') }}">{{ __('+ New model') }}</a>
    </div>
  @endcan
</div>

@if(session('import_failures'))
<div class="card form-section" style="margin-bottom:1rem">
  <div class="card-head"><h3>{{ __('Skipped rows') }}</h3><span class="card-hint">{{ __('These rows failed validation during the last import') }}</span></div>
  <table class="table">
    <thead><tr><th style="width:60px">{{ __('Row') }}</th><th>{{ __('Name') }}</th><th>{{ __('Problems') }}</th></tr></thead>
    <tbody>
      @foreach(session('import_failures') as $f)
      <tr>
        <td class="num">{{ $f['row'] }}</td>
        <td class="cell-title" style="font-weight:500;font-size:0.84rem">{{ $f['name'] }}</td>
        <td class="dim" style="font-size:0.72rem">@foreach($f['errors'] as $e)<div class="err" style="margin:0">{{ $e }}</div>@endforeach</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

@include('partials.filter-bar', [
  'search'  => 'Model or tagline…',
  'selects' => [
    ['name' => 'brand_id', 'label' => 'All brands', 'options' => $brands->pluck('name', 'id')],
    ['name' => 'status',   'label' => 'Any status', 'options' => ['1' => 'Live', '0' => 'Hidden']],
  ],
])

<div class="card">
  @if($motorcycles->isEmpty())
    <div class="empty">
      <div class="big">{{ __('No motorcycles') }}</div>
      <p>{{ __('Add your first model, or ask an admin to assign brands to your account.') }}</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr>
          <th></th><th>{{ __('Model') }}</th><th>{{ __('Brand') }}</th><th>{{ __('Price') }}</th><th>{{ __('From/mo') }}</th><th>{{ __('Stock') }}</th><th>{{ __('Status') }}</th><th>{{ __('Bookings') }}</th><th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($motorcycles as $m)
        <tr>
          <td style="width:64px">
            @if($m->coverUrl())
              <img class="thumb" src="{{ $m->coverUrl() }}" alt="" loading="lazy">
            @else
              <span class="thumb-ph">N/A</span>
            @endif
          </td>
          <td>
            <div class="cell-title">{{ $m->name }}</div>
            <div class="dim">{{ $m->tagline }}</div>
          </td>
          <td>
            <span class="cell-brand">
              <span class="dot" style="background:{{ $m->brand->accent }}"></span>
              {{ $m->brand->name }}
            </span>
          </td>
          <td class="num">{{ $m->currency?->symbol ?? '$' }}{{ number_format($m->price) }}</td>
          <td class="num">{{ $m->currency?->symbol ?? '$' }}{{ number_format($m->monthly_from) }}</td>
          <td class="num">{{ $m->quantity }}</td>
          <td><span class="badge {{ $m->is_active ? 'lime' : 'red' }}">{{ $m->is_active ? __('LIVE') : __('HIDDEN') }}</span></td>
          <td class="num">{{ $m->test_ride_requests_count }}</td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="{{ route('admin.motorcycles.edit', $m) }}">{{ __('Edit') }}</a>
              <form method="POST" action="{{ route('admin.motorcycles.destroy', $m) }}" onsubmit="return confirm('Delete {{ $m->name }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm" type="submit">{{ __('Delete') }}</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-bar">
      <span>{{ __(':count models', ['count' => $motorcycles->total()]) }}</span>
      {{ $motorcycles->links() }}
    </div>
  @endif
</div>
@endsection
