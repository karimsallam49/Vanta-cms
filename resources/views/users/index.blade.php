@extends('layouts.app')

@section('title', 'Staff')
@section('crumb', 'Staff')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Access control') }}</div>
    <h1 class="page-title">{{ __('STAFF') }} <span class="accent">{{ __('ACCOUNTS') }}</span></h1>
  </div>
  <a class="btn btn-primary" href="{{ route('admin.users.create') }}">{{ __('+ New account') }}</a>
</div>

@include('partials.filter-bar', [
  'search'  => 'Name or email…',
  'selects' => [
    ['name' => 'role_id', 'label' => 'Any role', 'options' => $roles],
  ],
])

<div class="card">
  <table class="table">
    <thead>
      <tr><th>{{ __('Name') }}</th><th>{{ __('Email') }}</th><th>{{ __('Role') }}</th><th>{{ __('Brand scope') }}</th><th></th></tr>
    </thead>
    <tbody>
      @foreach($users as $u)
      <tr>
        <td class="cell-title">{{ $u->name }}</td>
        <td class="dim">{{ $u->email }}</td>
        <td><span class="badge {{ $u->isAdmin() ? 'solid' : '' }}">{{ $u->role?->name ?? __('No role') }}</span></td>
        <td>
          @if($u->isAdmin())
            <span class="dim">{{ __('All brands') }}</span>
          @elseif($u->brands->isEmpty())
            <span class="badge red">{{ __('NO BRANDS ASSIGNED') }}</span>
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
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.users.edit', $u) }}">{{ __('Edit') }}</a>
            @if(!$u->is(auth()->user()))
              <form method="POST" action="{{ route('admin.users.destroy', $u) }}" onsubmit="return confirm('Delete {{ $u->name }}?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-sm" type="submit">{{ __('Delete') }}</button>
              </form>
            @endif
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="pagination-bar">
    <span>{{ __(':count accounts', ['count' => $users->total()]) }}</span>
    {{ $users->links() }}
  </div>
</div>
@endsection
