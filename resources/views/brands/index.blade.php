@extends('layouts.app')

@section('title', 'Brands')
@section('crumb', 'Brands')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Group portfolio') }}</div>
    <h1 class="page-title">{{ __('BR') }}<span class="accent">{{ __('ANDS') }}</span></h1>
  </div>
  <div style="display:flex;gap:0.6rem">
    <a class="btn btn-ghost" href="{{ route('admin.brands.export') }}">{{ __('Export') }}</a>
    <a class="btn btn-ghost" href="{{ route('admin.brands.import.create') }}">{{ __('Bulk import') }}</a>
    <a class="btn btn-primary" href="{{ route('admin.brands.create') }}">{{ __('+ New brand') }}</a>
  </div>
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
  'search'  => 'Brand name or origin…',
  'selects' => [
    ['name' => 'segment', 'label' => 'All segments', 'options' => [
      'urban' => 'Urban', 'adventure' => 'Adventure', 'performance' => 'Performance', 'premium' => 'Premium',
    ]],
  ],
])

<div class="grid-cards">
  @foreach($brands as $brand)
  <div class="brand-card">
    <span class="stripe" style="background:{{ $brand->accent }}"></span>
    <h3>{{ $brand->name }}</h3>
    <div class="seg">{{ $brand->segment }} — {{ $brand->origin }}</div>
    @if($brand->tagline)<div class="dim" style="font-size:0.76rem;margin-top:0.4rem">{{ $brand->tagline }}</div>@endif
    <div class="meta">
      <span><span class="num">{{ $brand->motorcycles_count }}</span> {{ __('models') }} · <span class="num">{{ $brand->offers_count }}</span> {{ __('offers') }} · <span class="num">{{ $brand->users_count }}</span> {{ __('staff') }}</span>
      @if($brand->pending_rides_count > 0)
        <span class="badge red" style="margin-top:0.4rem">{{ $brand->pending_rides_count }} {{ __('pending rides') }}</span>
      @endif
    </div>
    <div class="row-actions" style="margin-top:0.9rem;justify-content:flex-start">
      <a class="btn btn-ghost btn-sm" href="{{ route('admin.brands.edit', $brand) }}">{{ __('Edit') }}</a>
      <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Delete {{ $brand->name }}?')">
        @csrf @method('DELETE')
        <button class="btn btn-danger btn-sm" type="submit">{{ __('Delete') }}</button>
      </form>
    </div>
  </div>
  @endforeach
</div>
@endsection
