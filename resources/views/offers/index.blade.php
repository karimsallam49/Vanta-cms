@extends('layouts.app')

@section('title', 'Offers')
@section('crumb', 'Offers')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Promotions & finance') }}</div>
    <h1 class="page-title">{{ __('OFF') }}<span class="accent">{{ __('ERS') }}</span></h1>
  </div>
  @can('create', App\Models\Offer::class)
    <a class="btn btn-primary" href="{{ route('admin.offers.create') }}">{{ __('+ New offer') }}</a>
  @endcan
</div>

@include('partials.filter-bar', [
  'search'  => 'Offer title…',
  'selects' => [
    ['name' => 'scope',    'label' => 'Any scope', 'options' => collect($scopes)->mapWithKeys(fn ($s) => [$s->value => $s->label()])],
    ['name' => 'brand_id', 'label' => 'All brands', 'options' => $brands->pluck('name', 'id')],
    ['name' => 'status',   'label' => 'Any status', 'options' => [
      'active' => 'Active', 'expired' => 'Expired', 'inactive' => 'Inactive',
    ]],
  ],
])

<div class="card">
  @if($offers->isEmpty())
    <div class="empty">
      <div class="big">{{ __('No offers') }}</div>
      <p>{{ __('Create a promotion or a finance deal for your brands.') }}</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr><th>{{ __('Offer') }}</th><th>{{ __('Applies to') }}</th><th>{{ __('Discount') }}</th><th>{{ __('Expires') }}</th><th>{{ __('Status') }}</th><th></th></tr>
      </thead>
      <tbody>
        @foreach($offers as $o)
        <tr>
          <td style="max-width:380px">
            <div class="cell-title">{{ $o->title }}</div>
            <div class="dim" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:340px">{{ $o->description }}</div>
          </td>
          <td>
            <span class="badge {{ $o->isGroupWide() ? '' : 'lime' }}">{{ __($o->scope->label()) }}</span>
            <div class="dim" style="margin-top:0.3rem">{{ $o->targetName() }}</div>
          </td>
          <td class="num">{{ $o->discount_percent ? $o->discount_percent.'%' : '—' }}</td>
          <td class="num">{{ $o->expires_on?->format('d M Y') ?? '—' }}</td>
          <td><span class="badge {{ $o->is_active ? 'lime' : 'red' }}">{{ $o->is_active ? __('ACTIVE') : __('INACTIVE') }}</span></td>
          <td>
            <div class="row-actions">
              @can('update', $o)
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.offers.edit', $o) }}">{{ __('Edit') }}</a>
              @endcan
              @can('delete', $o)
                <form method="POST" action="{{ route('admin.offers.destroy', $o) }}" onsubmit="return confirm('Delete this offer?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-sm" type="submit">{{ __('Delete') }}</button>
                </form>
              @endcan
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-bar">
      <span>{{ __(':count offers', ['count' => $offers->total()]) }}</span>
      {{ $offers->links() }}
    </div>
  @endif
</div>
@endsection
