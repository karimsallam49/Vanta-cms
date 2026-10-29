@extends('layouts.app')

@section('title', 'Test Rides')
@section('crumb', 'Test Rides')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Customer requests</div>
    <h1 class="page-title">TEST <span class="accent">RIDES</span></h1>
  </div>
</div>

<div class="filters" style="margin-bottom:1rem">
  <a class="btn btn-ghost btn-sm {{ !$activeStatus ? 'active-filter' : '' }}" href="{{ route('admin.test-rides.index') }}">All</a>
  @foreach($statuses as $s)
    <a class="btn btn-ghost btn-sm {{ $activeStatus === $s->value ? 'active-filter' : '' }}" href="{{ route('admin.test-rides.index', ['status' => $s->value]) }}">{{ $s->label() }}</a>
  @endforeach
</div>

<div class="card">
  @if($rides->isEmpty())
    <div class="empty">
      <div class="big">No requests</div>
      <p>{{ $activeStatus ? 'Nothing with this status — try another filter.' : 'Test ride bookings will land here.' }}</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr><th>Customer</th><th>Motorcycle</th><th>Showroom</th><th>Date</th><th>Status</th><th></th></tr>
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
          <td><span class="badge {{ $ride->status->tone() }}">{{ $ride->status->label() }}</span></td>
          <td>
            <div class="row-actions">
              <details class="ride-actions">
                <summary class="btn btn-ghost btn-sm" style="list-style:none">Manage</summary>
                <div style="position:relative">
                  <form method="POST" action="{{ route('admin.test-rides.update', $ride) }}" style="position:absolute;right:0;z-index:10;background:var(--carbon);border:1px solid var(--hairline);border-radius:8px;padding:0.8rem;display:flex;flex-direction:column;gap:0.5rem;min-width:220px">
                    @csrf @method('PATCH')
                    <select class="select" name="status">
                      @foreach($statuses as $s)
                        <option value="{{ $s->value }}" @selected($ride->status === $s)>{{ $s->label() }}</option>
                      @endforeach
                    </select>
                    <input class="input" name="internal_note" value="{{ $ride->internal_note }}" placeholder="Internal note (optional)" maxlength="1000">
                    <button class="btn btn-primary btn-sm" type="submit">Update</button>
                  </form>
                </div>
              </details>
            </div>
          </td>
        </tr>
        @if($ride->internal_note)
        <tr>
          <td colspan="6" style="padding-top:0.15rem;padding-bottom:0.9rem">
            <span class="dim" style="font-size:0.76rem">Note: {{ $ride->internal_note }}</span>
          </td>
        </tr>
        @endif
        @endforeach
      </tbody>
    </table>
    <div class="pagination-bar">
      <span>{{ $rides->total() }} requests</span>
      {{ $rides->links() }}
    </div>
  @endif
</div>
@endsection
