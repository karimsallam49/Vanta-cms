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

<form class="form-shell form-split" method="POST" enctype="multipart/form-data" action="{{ $offer->exists ? route('admin.offers.update', $offer) : route('admin.offers.store') }}">
  @csrf
  @if($offer->exists) @method('PUT') @endif

  {{-- ---------- Main ---------- --}}
  <div class="form-main">
    <div class="card form-section">
      <div class="card-head"><h3>Offer details</h3></div>
      <div class="section-body">
        <div class="field {{ $errors->has('title') ? 'has-error' : '' }}">
          <label for="title">TITLE <span class="req">*</span></label>
          <input class="input" id="title" name="title" value="{{ old('title', $offer->title) }}" required maxlength="140" placeholder="KAIZEN RK-1 — zero down payment">
          @error('title')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="field {{ $errors->has('description') ? 'has-error' : '' }}">
          <label for="description">DESCRIPTION <span class="req">*</span></label>
          <textarea class="textarea" id="description" name="description" required maxlength="2000" rows="5">{{ old('description', $offer->description) }}</textarea>
          @error('description')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="form-grid">
          <div class="field {{ $errors->has('badge') ? 'has-error' : '' }}">
            <label for="badge">BADGE LABEL</label>
            <input class="input" id="badge" name="badge" value="{{ old('badge', $offer->badge) }}" maxlength="40" placeholder="LIMITED TIME">
            <span class="hint">Small tag on the offer card — e.g. SEASON, FINANCE.</span>
            @error('badge')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="field {{ $errors->has('discount_percent') ? 'has-error' : '' }}">
            <label for="discount_percent">DISCOUNT %</label>
            <input class="input" id="discount_percent" name="discount_percent" type="number" min="1" max="100" dir="ltr" value="{{ old('discount_percent', $offer->discount_percent) }}" placeholder="20">
            <span class="hint">Leave empty for non-discount promos (free pack, financing…).</span>
            @error('discount_percent')<span class="err">{{ $message }}</span>@enderror
          </div>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <a class="btn btn-ghost" href="{{ route('admin.offers.index') }}">Cancel</a>
      <button class="btn btn-primary" type="submit">{{ $offer->exists ? 'Save changes' : 'Create offer' }}</button>
    </div>
  </div>

  {{-- ---------- Right rail ---------- --}}
  <div class="form-rail">
    <div class="card form-section">
      <div class="card-head"><h3>Applies to</h3></div>
      <div class="section-body">
        <div class="field {{ $errors->has('scope') ? 'has-error' : '' }}">
          <label for="scope">SCOPE <span class="req">*</span></label>
          <select class="select" id="scope" name="scope" required>
            @foreach($scopes as $s)
              @continue(!$s->value || (in_array($s->value, ['group', 'test_ride']) && !auth()->user()->isAdmin()))
              <option value="{{ $s->value }}" @selected(old('scope', $offer->scope?->value ?? 'brand') === $s->value)>
                {{ $s->label() }}
              </option>
            @endforeach
          </select>
          @error('scope')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="field {{ $errors->has('brand_id') ? 'has-error' : '' }}" id="target-brand" hidden>
          <label for="brand_id">BRAND <span class="req">*</span></label>
          <select class="select" id="brand_id" name="brand_id">
            <option value="">— Choose a brand —</option>
            @foreach($brands as $b)
              <option value="{{ $b->id }}" @selected((string) old('brand_id', $offer->brand_id) === (string) $b->id)>{{ $b->name }}</option>
            @endforeach
          </select>
          @error('brand_id')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="field {{ $errors->has('motorcycle_id') ? 'has-error' : '' }}" id="target-motorcycle" hidden>
          <label for="motorcycle_id">MODEL <span class="req">*</span></label>
          <select class="select" id="motorcycle_id" name="motorcycle_id">
            <option value="">— Choose a model —</option>
            @foreach($motorcycles as $m)
              <option value="{{ $m->id }}" @selected((string) old('motorcycle_id', $offer->motorcycle_id) === (string) $m->id)>{{ $m->brand->name }} — {{ $m->name }}</option>
            @endforeach
          </select>
          @error('motorcycle_id')<span class="err">{{ $message }}</span>@enderror
        </div>

        <span class="hint" id="scope-hint"></span>
      </div>
    </div>

    <div class="card form-section">
      <div class="card-head"><h3>Card image</h3></div>
      <div class="section-body">
        @if($offer->imageUrl())
          <div style="margin-bottom:0.8rem">
            <img src="{{ $offer->imageUrl() }}" alt="" style="width:100%;border-radius:8px;border:1px solid var(--hairline);display:block">
          </div>
          <div class="checkbox-row" style="margin-bottom:0.8rem">
            <input type="checkbox" id="remove_image" name="remove_image" value="1" @checked(old('remove_image'))>
            <label for="remove_image">{{ __('Remove current image') }}</label>
          </div>
        @endif
        <div class="field {{ $errors->has('image') ? 'has-error' : '' }}">
          <label for="image">{{ $offer->imageUrl() ? __('REPLACE') : __('UPLOAD') }}</label>
          <input class="input" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
          <span class="hint">{{ __('JPG, PNG or WebP — up to 4 MB. Shown as the offer card background on the website.') }}</span>
          @error('image')<span class="err">{{ $message }}</span>@enderror
        </div>
      </div>
    </div>

    <div class="card form-section">
      <div class="card-head"><h3>Schedule</h3></div>
      <div class="section-body">
        <div class="field {{ $errors->has('expires_on') ? 'has-error' : '' }}">
          <label for="expires_on">EXPIRES ON</label>
          <input class="input" id="expires_on" name="expires_on" type="date" value="{{ old('expires_on', $offer->expires_on?->format('Y-m-d')) }}">
          <span class="hint">Leave empty for an open-ended offer.</span>
          @error('expires_on')<span class="err">{{ $message }}</span>@enderror
        </div>
      </div>
    </div>

    <div class="card form-section">
      <div class="card-head"><h3>Visibility</h3></div>
      <div class="section-body">
        <div class="checkbox-row">
          <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $offer->is_active ?? true))>
          <label for="is_active">Offer is live on the website</label>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
(function () {
  const scope = document.getElementById('scope');
  const brand = document.getElementById('target-brand');
  const bike = document.getElementById('target-motorcycle');
  const hint = document.getElementById('scope-hint');
  const hints = {
    group: 'Shown to every visitor, across all brands.',
    brand: 'Attached to one brand — appears on its offers and cards.',
    motorcycle: 'Attached to a single model — product-level promotion.',
    test_ride: 'Promotes test rides group-wide (e.g. free gear per ride).',
  };
  function sync() {
    brand.hidden = scope.value !== 'brand';
    bike.hidden = scope.value !== 'motorcycle';
    document.getElementById('brand_id').required = scope.value === 'brand';
    document.getElementById('motorcycle_id').required = scope.value === 'motorcycle';
    hint.textContent = hints[scope.value] || '';
  }
  scope.addEventListener('change', sync);
  sync();
})();
</script>
@endsection
