@extends('layouts.app')

@section('title', 'Motorcycles')
@section('crumb', 'Motorcycles')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Catalogue</div>
    <h1 class="page-title">MOTOR<span class="accent">CYCLES</span></h1>
  </div>
  @can('create', App\Models\Motorcycle::class)
    <a class="btn btn-primary" href="{{ route('admin.motorcycles.create') }}">+ New model</a>
  @endcan
</div>

<div class="card">
  @if($motorcycles->isEmpty())
    <div class="empty">
      <div class="big">No motorcycles</div>
      <p>Add your first model, or ask an admin to assign brands to your account.</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr>
          <th></th><th>Model</th><th>Brand</th><th>Price</th><th>From/mo</th><th>Bookings</th><th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($motorcycles as $m)
        <tr>
          <td style="width:64px">
            @if($m->image)
              <img class="thumb" src="{{ $m->image }}" alt="" loading="lazy">
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
          <td class="num">${{ number_format($m->price) }}</td>
          <td class="num">${{ number_format($m->monthly_from) }}</td>
          <td class="num">{{ $m->test_ride_requests_count }}</td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="{{ route('admin.motorcycles.edit', $m) }}">Edit</a>
              <form method="POST" action="{{ route('admin.motorcycles.destroy', $m) }}" onsubmit="return confirm('Delete {{ $m->name }}? This cannot be undone.')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-bar">
      <span>{{ $motorcycles->total() }} models</span>
      {{ $motorcycles->links() }}
    </div>
  @endif
</div>
@endsection
