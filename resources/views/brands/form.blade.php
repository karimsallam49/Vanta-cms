@extends('layouts.app')

@section('title', $brand->exists ? 'Edit Brand' : 'New Brand')
@section('crumb', $brand->exists ? 'Edit Brand' : 'New Brand')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Portfolio / {{ $brand->exists ? 'Edit' : 'Create' }}</div>
    <h1 class="page-title">{{ $brand->exists ? 'EDIT' : 'NEW' }} <span class="accent">BRAND</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.brands.index') }}">← Back</a>
</div>

<form class="card form-card" method="POST" action="{{ $brand->exists ? route('admin.brands.update', $brand) : route('admin.brands.store') }}">
  @csrf
  @if($brand->exists) @method('PUT') @endif

  <div class="form-grid">
    <div class="field {{ $errors->has('name') ? 'has-error' : '' }}">
      <label for="name">BRAND NAME <span class="req">*</span></label>
      <input class="input" id="name" name="name" value="{{ old('name', $brand->name) }}" required maxlength="80" placeholder="NOMAD">
      @error('name')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('segment') ? 'has-error' : '' }}">
      <label for="segment">SEGMENT <span class="req">*</span></label>
      <select class="select" id="segment" name="segment" required>
        @foreach(['urban', 'adventure', 'performance', 'premium'] as $seg)
          <option value="{{ $seg }}" @selected(old('segment', $brand->segment) === $seg)>{{ ucfirst($seg) }}</option>
        @endforeach
      </select>
      @error('segment')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('origin') ? 'has-error' : '' }}">
      <label for="origin">ORIGIN</label>
      <input class="input" id="origin" name="origin" value="{{ old('origin', $brand->origin) }}" maxlength="80" placeholder="Salzburg, Austria">
      @error('origin')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('accent') ? 'has-error' : '' }}">
      <label for="accent">ACCENT COLOR <span class="req">*</span></label>
      <div class="color-pick">
        <input type="color" id="accent" name="accent" value="{{ old('accent', $brand->accent ?? '#e10600') }}">
        <span class="hint">Used for dots and badges across the CMS.</span>
      </div>
      @error('accent')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('tagline') ? 'has-error' : '' }}">
      <label for="tagline">TAGLINE</label>
      <input class="input" id="tagline" name="tagline" value="{{ old('tagline', $brand->tagline) }}" maxlength="140" placeholder="Engineered for the long way home">
      @error('tagline')<span class="err">{{ $message }}</span>@enderror
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="{{ route('admin.brands.index') }}">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ $brand->exists ? 'Save changes' : 'Create brand' }}</button>
  </div>
</form>
@endsection
