@extends('layouts.app')

@section('title', $offer->exists ? 'Edit Offer' : 'New Offer')
@section('crumb', $offer->exists ? 'Edit Offer' : 'New Offer')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Promotions / {{ $offer->exists ? 'Edit' : 'Create' }}</div>
    <h1 class="page-title">{{ $offer->exists ? 'EDIT' : 'NEW' }} <span class="accent">OFFER</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.offers.index') }}">← Back</a>
</div>

<form class="card form-card" method="POST" action="{{ $offer->exists ? route('admin.offers.update', $offer) : route('admin.offers.store') }}">
  @csrf
  @if($offer->exists) @method('PUT') @endif

  <div class="form-grid">
    <div class="field full {{ $errors->has('title') ? 'has-error' : '' }}">
      <label for="title">TITLE <span class="req">*</span></label>
      <input class="input" id="title" name="title" value="{{ old('title', $offer->title) }}" required maxlength="140" placeholder="KAIZEN RK-1 — Zero down payment">
      @error('title')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('description') ? 'has-error' : '' }}">
      <label for="description">DESCRIPTION <span class="req">*</span></label>
      <textarea class="textarea" id="description" name="description" required maxlength="2000">{{ old('description', $offer->description) }}</textarea>
      @error('description')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('brand_id') ? 'has-error' : '' }}">
      <label for="brand_id">SCOPE</label>
      <select class="select" id="brand_id" name="brand_id" {{ auth()->user()->isAdmin() ? '' : 'required' }}>
        @if(auth()->user()->isAdmin())
          <option value="">— Group-wide (admin only) —</option>
        @endif
        @foreach($brands as $b)
          <option value="{{ $b->id }}" @selected((string) old('brand_id', $offer->brand_id) === (string) $b->id)>{{ $b->name }}</option>
        @endforeach
      </select>
      <span class="hint">Brand-specific offers appear under that brand's segment on the site.</span>
      @error('brand_id')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('expires_on') ? 'has-error' : '' }}">
      <label for="expires_on">EXPIRES ON</label>
      <input class="input" id="expires_on" name="expires_on" type="date" value="{{ old('expires_on', $offer->expires_on?->format('Y-m-d')) }}">
      @error('expires_on')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('badge') ? 'has-error' : '' }}">
      <label for="badge">BADGE LABEL</label>
      <input class="input" id="badge" name="badge" value="{{ old('badge', $offer->badge) }}" maxlength="40" placeholder="LIMITED TIME">
      @error('badge')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field">
      <label>STATUS</label>
      <div class="checkbox-row">
        <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $offer->is_active ?? true))>
        <label for="is_active">Offer is active and visible</label>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="{{ route('admin.offers.index') }}">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ $offer->exists ? 'Save changes' : 'Create offer' }}</button>
  </div>
</form>
@endsection
