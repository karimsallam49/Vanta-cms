@extends('layouts.app')

@section('title', 'Currencies')
@section('crumb', 'Currencies')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Group configuration') }}</div>
    <h1 class="page-title">{{ __('CURREN') }}<span class="accent">{{ __('CIES') }}</span></h1>
  </div>
  <a class="btn btn-primary" href="{{ route('admin.currencies.create') }}">{{ __('+ New currency') }}</a>
</div>

@include('partials.filter-bar', ['search' => 'Code or name…'])

<div class="card">
  @if($currencies->isEmpty())
    <div class="empty">
      <div class="big">{{ __('No currencies') }}</div>
      <p>{{ __('Add a currency — models need one to be priced.') }}</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr><th>{{ __('Code') }}</th><th>{{ __('Name') }}</th><th>{{ __('Symbol') }}</th><th>{{ __('Priced models') }}</th><th></th></tr>
      </thead>
      <tbody>
        @foreach($currencies as $c)
        <tr>
          <td><span class="badge">{{ $c->code }}</span></td>
          <td><div class="cell-title" style="font-weight:500;font-size:0.84rem">{{ $c->name }}</div></td>
          <td class="num">{{ $c->symbol }}</td>
          <td class="num">{{ $c->motorcycles_count }}</td>
          <td>
            <div class="row-actions">
              <a class="btn btn-ghost btn-sm" href="{{ route('admin.currencies.edit', $c) }}">{{ __('Edit') }}</a>
              <form method="POST" action="{{ route('admin.currencies.destroy', $c) }}" onsubmit="return confirm('Delete {{ $c->code }}?')">
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
      <span>{{ __(':count currencies', ['count' => $currencies->total()]) }}</span>
      {{ $currencies->links() }}
    </div>
  @endif
</div>
@endsection
