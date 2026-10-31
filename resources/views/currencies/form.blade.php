@extends('layouts.app')

@section('title', $currency->exists ? 'Edit Currency' : 'New Currency')
@section('crumb', $currency->exists ? 'Edit Currency' : 'New Currency')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Configuration / {{ $currency->exists ? 'Edit' : 'Create' }}</div>
    <h1 class="page-title">{{ $currency->exists ? 'EDIT' : 'NEW' }} <span class="accent">CURRENCY</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.currencies.index') }}">← Back</a>
</div>

<form class="card form-card" method="POST" action="{{ $currency->exists ? route('admin.currencies.update', $currency) : route('admin.currencies.store') }}">
  @csrf
  @if($currency->exists) @method('PUT') @endif

  <div class="form-grid">
    <div class="field {{ $errors->has('code') ? 'has-error' : '' }}">
      <label for="code">CODE <span class="req">*</span></label>
      <input class="input" id="code" name="code" value="{{ old('code', $currency->code) }}" required maxlength="3" minlength="3" placeholder="USD" style="text-transform:uppercase">
      <span class="hint">ISO-style, 3 letters — stored uppercase.</span>
      @error('code')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('symbol') ? 'has-error' : '' }}">
      <label for="symbol">SYMBOL <span class="req">*</span></label>
      <input class="input" id="symbol" name="symbol" value="{{ old('symbol', $currency->symbol) }}" required maxlength="8" placeholder="$">
      @error('symbol')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('name') ? 'has-error' : '' }}">
      <label for="name">NAME <span class="req">*</span></label>
      <input class="input" id="name" name="name" value="{{ old('name', $currency->name) }}" required maxlength="60" placeholder="UAE Dirham">
      @error('name')<span class="err">{{ $message }}</span>@enderror
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="{{ route('admin.currencies.index') }}">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ $currency->exists ? 'Save changes' : 'Create currency' }}</button>
  </div>
</form>
@endsection
