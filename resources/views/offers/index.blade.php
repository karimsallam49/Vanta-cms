@extends('layouts.app')

@section('title', 'Offers')
@section('crumb', 'Offers')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Promotions & finance</div>
    <h1 class="page-title">OFF<span class="accent">ERS</span></h1>
  </div>
  @can('create', App\Models\Offer::class)
    <a class="btn btn-primary" href="{{ route('admin.offers.create') }}">+ New offer</a>
  @endcan
</div>

<div class="card">
  @if($offers->isEmpty())
    <div class="empty">
      <div class="big">No offers</div>
      <p>Create a promotion or a finance deal for your brands.</p>
    </div>
  @else
    <table class="table">
      <thead>
        <tr><th>Offer</th><th>Scope</th><th>Expires</th><th>Status</th><th></th></tr>
      </thead>
      <tbody>
        @foreach($offers as $o)
        <tr>
          <td style="max-width:380px">
            <div class="cell-title">{{ $o->title }}</div>
            <div class="dim" style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:340px">{{ $o->description }}</div>
          </td>
          <td>
            @if($o->isGroupWide())
              <span class="badge gold">GROUP-WIDE</span>
            @else
              <span class="cell-brand"><span class="dot" style="background:{{ $o->brand->accent }}"></span>{{ $o->brand->name }}</span>
            @endif
          </td>
          <td class="num">{{ $o->expires_on?->format('d M Y') ?? '—' }}</td>
          <td><span class="badge {{ $o->is_active ? 'lime' : 'red' }}">{{ $o->is_active ? 'ACTIVE' : 'INACTIVE' }}</span></td>
          <td>
            <div class="row-actions">
              @can('update', $o)
                <a class="btn btn-ghost btn-sm" href="{{ route('admin.offers.edit', $o) }}">Edit</a>
              @endcan
              @can('delete', $o)
                <form method="POST" action="{{ route('admin.offers.destroy', $o) }}" onsubmit="return confirm('Delete this offer?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                </form>
              @endcan
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="pagination-bar">
      <span>{{ $offers->total() }} offers</span>
      {{ $offers->links() }}
    </div>
  @endif
</div>
@endsection
