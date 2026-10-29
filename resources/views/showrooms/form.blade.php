@extends('layouts.app')

@section('title', $showroom->exists ? 'Edit Showroom' : 'New Showroom')
@section('crumb', $showroom->exists ? 'Edit Showroom' : 'New Showroom')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Facilities / {{ $showroom->exists ? 'Edit' : 'Create' }}</div>
    <h1 class="page-title">{{ $showroom->exists ? 'EDIT' : 'NEW' }} <span class="accent">LOCATION</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.showrooms.index') }}">← Back</a>
</div>

<form class="card form-card" method="POST" action="{{ $showroom->exists ? route('admin.showrooms.update', $showroom) : route('admin.showrooms.store') }}">
  @csrf
  @if($showroom->exists) @method('PUT') @endif

  <div class="form-grid">
    <div class="field {{ $errors->has('city') ? 'has-error' : '' }}">
      <label for="city">CITY <span class="req">*</span></label>
      <input class="input" id="city" name="city" value="{{ old('city', $showroom->city) }}" required maxlength="80">
      @error('city')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('country') ? 'has-error' : '' }}">
      <label for="country">COUNTRY <span class="req">*</span></label>
      <input class="input" id="country" name="country" value="{{ old('country', $showroom->country) }}" required maxlength="80">
      @error('country')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('name') ? 'has-error' : '' }}">
      <label for="name">LOCATION NAME <span class="req">*</span></label>
      <input class="input" id="name" name="name" value="{{ old('name', $showroom->name) }}" required maxlength="140" placeholder="VANTA MOTO — SHEIKH ZAYED FLAGSHIP">
      @error('name')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('address') ? 'has-error' : '' }}">
      <label for="address">ADDRESS <span class="req">*</span></label>
      <input class="input" id="address" name="address" value="{{ old('address', $showroom->address) }}" required maxlength="255">
      @error('address')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('phone') ? 'has-error' : '' }}">
      <label for="phone">PHONE</label>
      <input class="input" id="phone" name="phone" value="{{ old('phone', $showroom->phone) }}" maxlength="40" placeholder="+971 4 555 0100">
      @error('phone')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('hours') ? 'has-error' : '' }}">
      <label for="hours">OPENING HOURS</label>
      <input class="input" id="hours" name="hours" value="{{ old('hours', $showroom->hours) }}" maxlength="120" placeholder="SAT–THU 9:00–21:00 · FRI 14:00–21:00">
      @error('hours')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field">
      <label>FACILITY TYPE</label>
      <div class="checkbox-row">
        <input type="checkbox" id="is_service_center" name="is_service_center" value="1" @checked(old('is_service_center', $showroom->is_service_center))>
        <label for="is_service_center">Includes service center</label>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="{{ route('admin.showrooms.index') }}">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ $showroom->exists ? 'Save changes' : 'Create location' }}</button>
  </div>
</form>
@endsection
