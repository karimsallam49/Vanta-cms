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

<div class="card">
  @if($roles->isEmpty())
    <div class="empty">
      <div class="big">{{ __('No roles') }}</div>
      <p>{{ __('Create one to control what accounts can see and do.') }}</p>
    </div>
  @else
  <table class="table">
    <thead>
      <tr>
        <th>{{ __('Role') }}</th>
        <th>{{ __('Type') }}</th>
        <th>{{ __('Permissions') }}</th>
        <th>{{ __('Accounts') }}</th>
        <th style="text-align:end">{{ __('Actions') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach($roles as $role)
      <tr>
        <td>
          <div class="cell-title">{{ $role->name }}</div>
          <div class="dim">{{ $role->description ?? $role->slug }}</div>
        </td>
        <td>
          @if($role->is_admin)
            <span class="badge solid">{{ __('Admin') }}</span>
          @else
            <span class="badge">{{ __('Custom') }}</span>
          @endif
        </td>
        <td>
          @if($role->is_admin)
            <span class="dim">{{ __('Full access — bypasses every check') }}</span>
          @else
            <div class="perm-chips">
              @foreach($role->permissionSummary() as $section => $count)
                <span class="chip">{{ __(Str::headline($section)) }} · {{ $count }}</span>
              @endforeach
              @if(empty($role->permissionSummary()))
                <span class="dim">{{ __('None granted') }}</span>
              @endif
            </div>
          @endif
        </td>
        <td class="num">{{ $role->users_count }}</td>
        <td>
          <div class="row-actions">
            <a class="btn btn-ghost btn-sm" href="{{ route('admin.roles.edit', $role) }}">{{ __('Edit') }}</a>
            @if(!$role->is_admin)
              <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('Delete {{ $role->name }}?')">
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
  @endif

  <div class="pagination-bar">
    <span>{{ __(':count roles', ['count' => $roles->total()]) }}</span>
    {{ $roles->links() }}
  </div>
</div>
@endsection
