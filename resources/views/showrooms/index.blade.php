@extends('layouts.app')

@section('title', 'Showrooms')
@section('crumb', 'Showrooms')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Group facilities') }}</div>
    <h1 class="page-title">{{ __('SHOW') }}<span class="accent">{{ __('ROOMS') }}</span></h1>
  </div>
  <a class="btn btn-primary" href="{{ route('admin.showrooms.create') }}">{{ __('+ New location') }}</a>
</div>

@include('partials.filter-bar', [
  'search'  => 'City, country or name…',
  'selects' => [
    ['name' => 'type', 'label' => 'Any type', 'options' => [
      '1' => 'Showroom + service', '0' => 'Showroom only',
    ]],
  ],
])

<div class="card">
  @if($showrooms->isEmpty())
    <div class="empty">
      <div class="big">{{ __('No locations') }}</div>
      <p>{{ __('Add a showroom or service center.') }}</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr><th>{{ __('Location') }}</th><th>{{ __('Name') }}</th><th>{{ __('Phone') }}</th><th>{{ __('Hours') }}</th><th>{{ __('Type') }}</th><th></th></tr>
      </thead>
      <tbody>
        @foreach($showrooms as $s)
        <tr>
          <td>
            <div class="cell-title">{{ $s->city }}</div>
            <div class="dim">{{ $s->country }}</div>
          </td>
          <td>
            <div class="cell-title" style="font-weight:500;font-size:0.84rem">{{ $s->name }}</div>
            <div class="dim">{{ $s->address }}</div>
          </td>
          <td class="num">{{ $s->phone ?? '—' }}</td>
          <td class="dim">{{ $s->hours ?? '—' }}</td>
          <td><span class="badge {{ $s->is_service_center ? 'lime' : '' }}">{{ $s->is_service_center ? __('SHOWROOM + SERVICE') : __('SHOWROOM') }}</span></td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="{{ route('admin.showrooms.edit', $s) }}">{{ __('Edit') }}</a>
              <form method="POST" action="{{ route('admin.showrooms.destroy', $s) }}" onsubmit="return confirm('Delete {{ $s->name }}?')">
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
      <span>{{ __(':count locations', ['count' => $showrooms->total()]) }}</span>
      {{ $showrooms->links() }}
    </div>
  @endif
</div>
@endsection
