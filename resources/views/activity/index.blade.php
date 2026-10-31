@extends('layouts.app')

@section('title', 'Activity log')
@section('crumb', 'Activity log')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Every action, on record') }}</div>
    <h1 class="page-title">{{ __('ACTIVITY') }} <span class="accent">{{ __('LOG') }}</span></h1>
  </div>
</div>

<form class="filter-bar" method="GET">
  <span class="fb-tag">{{ __('Filter') }}</span>

  <input class="input" name="q" value="{{ request('q') }}" placeholder="{{ __('Search descriptions…') }}" autocomplete="off">

  <select class="select" name="user_id">
    <option value="">{{ __('Any user') }}</option>
    @foreach($users as $id => $name)
      <option value="{{ $id }}" @selected((string) request('user_id') === (string) $id)>{{ $name }}</option>
    @endforeach
  </select>

  <select class="select" name="action">
    <option value="">{{ __('Any action') }}</option>
    @foreach($actions as $a)
      <option value="{{ $a }}" @selected(request('action') === $a)>{{ __(Str::headline($a)) }}</option>
    @endforeach
  </select>

  <select class="select" name="type">
    <option value="">{{ __('Any record type') }}</option>
    @foreach($types as $key => $label)
      <option value="{{ $key }}" @selected(request('type') === $key)>{{ __($label) }}</option>
    @endforeach
  </select>

  <input class="input" type="date" name="from" value="{{ request('from') }}" title="{{ __('From date') }}">
  <input class="input" type="date" name="to" value="{{ request('to') }}" title="{{ __('To date') }}">

  <div class="fb-actions">
    @if(collect(request()->except('page'))->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty())
      <a class="fb-reset" href="{{ url()->current() }}">{{ __('Reset') }}</a>
    @endif
  </div>
</form>

<div class="card">
  @if($logs->isEmpty())
    <div class="empty">
      <div class="big">{{ __('Nothing logged yet') }}</div>
      <p>{{ __('Actions across the CMS — creates, edits, deletes, sign-ins — will appear here.') }}</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr><th>{{ __('When') }}</th><th>{{ __('User') }}</th><th>{{ __('Action') }}</th><th>{{ __('Record') }}</th><th>{{ __('Changed fields') }}</th><th>{{ __('IP') }}</th></tr>
      </thead>
      <tbody>
        @foreach($logs as $log)
        <tr>
          <td class="num" style="white-space:nowrap">{{ $log->created_at->format('d M — H:i') }}</td>
          <td>
            <div class="cell-title">{{ $log->user?->name ?? __('System') }}</div>
            @if($log->user)<div class="dim">{{ $log->user->email }}</div>@endif
          </td>
          <td><span class="badge {{ $log->tone() }}">{{ __(Str::headline($log->action)) }}</span></td>
          <td class="dim">{{ $log->description }}</td>
          <td class="dim" style="font-size:0.76rem">
            {{ implode(', ', array_slice($log->changedFields(), 0, 4)) }}
            @if(count($log->changedFields()) > 4) +{{ count($log->changedFields()) - 4 }}@endif
          </td>
          <td class="dim" style="font-size:0.76rem">{{ $log->ip_address ?? '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-bar">
      <span>{{ __(':count entries', ['count' => $logs->total()]) }}</span>
      {{ $logs->links() }}
    </div>
  @endif
</div>
<script>
  // Date pickers apply immediately, like the selects.
  document.querySelectorAll('.filter-bar input[type="date"]').forEach(d =>
    d.addEventListener('change', () => d.form.submit()));
</script>
@endsection
