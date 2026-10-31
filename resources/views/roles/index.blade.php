@extends('layouts.app')

@section('title', 'Roles')
@section('crumb', 'Roles')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Access control') }}</div>
    <h1 class="page-title">{{ __('ROLES &') }} <span class="accent">{{ __('PERMISSIONS') }}</span></h1>
  </div>
  <a class="btn btn-primary" href="{{ route('admin.roles.create') }}">{{ __('+ New role') }}</a>
</div>

@include('partials.filter-bar', ['search' => 'Role name…'])

<div class="grid-cards">
  @forelse($roles as $role)
  <div class="card role-card">
    <div class="role-head">
      <div>
        <h3 class="role-name">{{ $role->name }}</h3>
        <div class="dim">{{ $role->description ?? $role->slug }}</div>
      </div>
      @if($role->is_admin)
        <span class="badge solid">{{ __('ADMIN') }}</span>
      @endif
    </div>

    @if($role->is_admin)
      <p class="dim" style="font-size:0.78rem;margin:0.4rem 0">{{ __('Full access — bypasses every permission check.') }}</p>
    @else
      <div class="perm-chips">
        @foreach($role->permissionSummary() as $section => $count)
          <span class="chip">{{ __(Str::headline($section)) }} · {{ $count }}</span>
        @endforeach
        @if(empty($role->permissionSummary()))
          <span class="chip dim">{{ __('No permissions granted') }}</span>
        @endif
      </div>
    @endif

    <div class="role-foot">
      <span class="dim">{{ __(':count account(s)', ['count' => $role->users_count]) }}</span>
      <div class="row-actions">
        <a class="btn btn-ghost btn-sm" href="{{ route('admin.roles.edit', $role) }}">{{ __('Edit') }}</a>
        @if(!$role->is_admin)
          <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete {{ $role->name }}?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm" type="submit">{{ __('Delete') }}</button>
          </form>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="card"><div class="empty"><div class="big">{{ __('No roles') }}</div><p>{{ __('Create one to control what accounts can see and do.') }}</p></div></div>
  @endforelse
</div>

<div class="pagination-bar">
  <span>{{ __(':count roles', ['count' => $roles->total()]) }}</span>
  {{ $roles->links() }}
</div>
@endsection
