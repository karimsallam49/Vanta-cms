@extends('layouts.app')

@section('title', 'Showrooms')
@section('crumb', 'Showrooms')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Group facilities</div>
    <h1 class="page-title">SHOW<span class="accent">ROOMS</span></h1>
  </div>
  <a class="btn btn-primary" href="{{ route('admin.showrooms.create') }}">+ New location</a>
</div>

<div class="card">
  @if($showrooms->isEmpty())
    <div class="empty">
      <div class="big">No locations</div>
      <p>Add a showroom or service center.</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr><th>Location</th><th>Name</th><th>Phone</th><th>Hours</th><th>Type</th><th></th></tr>
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
          <td><span class="badge {{ $s->is_service_center ? 'amber' : 'sky' }}">{{ $s->is_service_center ? 'SHOWROOM + SERVICE' : 'SHOWROOM' }}</span></td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="{{ route('admin.showrooms.edit', $s) }}">Edit</a>
              <form method="POST" action="{{ route('admin.showrooms.destroy', $s) }}" onsubmit="return confirm('Delete {{ $s->name }}?')">
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
      <span>{{ $showrooms->total() }} locations</span>
      {{ $showrooms->links() }}
    </div>
  @endif
</div>
@endsection
