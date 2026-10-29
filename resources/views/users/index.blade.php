@extends('layouts.app')

@section('title', 'Staff')
@section('crumb', 'Staff')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Access control</div>
    <h1 class="page-title">STAFF <span class="accent">ACCOUNTS</span></h1>
  </div>
  <a class="btn btn-primary" href="{{ route('admin.users.create') }}">+ New account</a>
</div>

<div class="card">
  <table class="table">
    <thead>
      <tr><th>Name</th><th>Email</th><th>Role</th><th>Brand scope</th><th></th></tr>
    </thead>
    <tbody>
      @foreach($users as $u)
      <tr>
        <td class="cell-title">{{ $u->name }}</td>
        <td class="dim">{{ $u->email }}</td>
        <td><span class="badge {{ $u->isAdmin() ? 'red' : 'lime' }}">{{ $u->role->label() }}</span></td>
        <td>
          @if($u->isAdmin())
            <span class="dim">All brands</span>
          @elseif($u->brands->isEmpty())
            <span class="badge amber">NO BRANDS ASSIGNED</span>
          @else
            <span class="cell-brand" style="gap:0.9rem;flex-wrap:wrap">
              @foreach($u->brands as $b)
                <span class="cell-brand"><span class="dot" style="background:{{ $b->accent }}"></span>{{ $b->name }}</span>
              @endforeach
            </span>
          @endif
        </td>
        <td>
          <div class="row-actions">
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.users.edit', $u) }}">Edit</a>
            @if(!$u->is(auth()->user()))
              <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete {{ $u->name }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm" type="submit">Delete</button>
              </form>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="pagination-bar">
    <span>{{ $users->total() }} accounts</span>
    {{ $users->links() }}
  </div>
</div>
@endsection
