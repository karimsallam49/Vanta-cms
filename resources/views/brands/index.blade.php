@extends('layouts.app')

@section('title', 'Brands')
@section('crumb', 'Brands')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Group portfolio</div>
    <h1 class="page-title">BR<span class="accent">ANDS</span></h1>
  </div>
  <a class="btn btn-primary" href="{{ route('admin.brands.create') }}">+ New brand</a>
</div>

<div class="grid-cards">
  @foreach($brands as $brand)
  <div class="brand-card">
    <span class="stripe" style="background:{{ $brand->accent }}"></span>
    <h3>{{ $brand->name }}</h3>
    <div class="seg">{{ $brand->segment }} — {{ $brand->origin }}</div>
    @if($brand->tagline)<div class="dim" style="font-size:0.76rem;margin-top:0.4rem">{{ $brand->tagline }}</div>@endif
    <div class="meta">
      <span><span class="num">{{ $brand->motorcycles_count }}</span> models · <span class="num">{{ $brand->offers_count }}</span> offers</span>
    </div>
    <div class="row-actions" style="margin-top:0.9rem;justify-content:flex-start">
      <a class="btn btn-ghost btn-sm" href="{{ route('admin.brands.edit', $brand) }}">Edit</a>
      <form method="POST" action="{{ route('admin.brands.destroy', $brand) }}" onsubmit="return confirm('Delete {{ $brand->name }}?')">
        @csrf @method('DELETE')
        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
      </form>
    </div>
  </div>
  @endforeach
</div>
@endsection
