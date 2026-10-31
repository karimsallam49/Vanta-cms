@extends('layouts.app')

@section('title', $brand->exists ? 'Edit Brand' : 'New Brand')
@section('crumb', $brand->exists ? 'Edit Brand' : 'New Brand')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Catalogue / {{ $brand->exists ? 'Edit' : 'Create' }}</div>
    <h1 class="page-title">{{ $brand->exists ? 'EDIT' : 'NEW' }} <span class="accent">BRAND</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.brands.index') }}">← Back</a>
</div>

<form class="form-shell" method="POST" action="{{ $brand->exists ? route('admin.brands.update', $brand) : route('admin.brands.store') }}">
  @csrf
  @if($brand->exists) @method('PUT') @endif

  <div class="card form-section">
    <div class="card-head">
      <h3>Brand</h3>
      <span class="card-hint">EN is the fallback when an Arabic field is empty</span>
    </div>

    <div class="lang-tabs" role="tablist">
      <button type="button" class="lang-tab active" data-pane="en">English (EN)</button>
      <button type="button" class="lang-tab" data-pane="ar">العربية (AR)</button>
    </div>

    <div class="section-body">
      <div class="lang-pane" data-pane="en">
        <div class="form-grid">
          <div class="field full {{ $errors->has('name') ? 'has-error' : '' }}">
            <label for="name">BRAND NAME <span class="req">*</span></label>
            <input class="input" id="name" name="name" value="{{ old('name', $brand->name) }}" required maxlength="80" placeholder="NOMAD">
            @error('name')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="field full {{ $errors->has('origin') ? 'has-error' : '' }}">
            <label for="origin">ORIGIN</label>
            <input class="input" id="origin" name="origin" value="{{ old('origin', $brand->origin) }}" maxlength="80" placeholder="Salzburg, Austria">
            @error('origin')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="field full {{ $errors->has('tagline') ? 'has-error' : '' }}">
            <label for="tagline">TAGLINE</label>
            <input class="input" id="tagline" name="tagline" value="{{ old('tagline', $brand->tagline) }}" maxlength="140" placeholder="Engineered for the long way home">
            @error('tagline')<span class="err">{{ $message }}</span>@enderror
          </div>
        </div>
      </div>

      <div class="lang-pane" data-pane="ar" hidden>
        <div class="form-grid" dir="rtl">
          <div class="field full {{ $errors->has('name_ar') ? 'has-error' : '' }}">
            <label for="name_ar">اسم البراند</label>
            <input class="input" id="name_ar" name="name_ar" value="{{ old('name_ar', $brand->name_ar) }}" maxlength="80" placeholder="نوماد">
            @error('name_ar')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="field full {{ $errors->has('origin_ar') ? 'has-error' : '' }}">
            <label for="origin_ar">المنشأ</label>
            <input class="input" id="origin_ar" name="origin_ar" value="{{ old('origin_ar', $brand->origin_ar) }}" maxlength="80" placeholder="سالزبورغ، النمسا">
            @error('origin_ar')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="field full {{ $errors->has('tagline_ar') ? 'has-error' : '' }}">
            <label for="tagline_ar">الشعار</label>
            <input class="input" id="tagline_ar" name="tagline_ar" value="{{ old('tagline_ar', $brand->tagline_ar) }}" maxlength="140" placeholder="مصمم للطريق الطويل">
            @error('tagline_ar')<span class="err">{{ $message }}</span>@enderror
          </div>
        </div>
      </div>

      <div class="form-grid" style="margin-top:1.2rem">
        <div class="field {{ $errors->has('segment') ? 'has-error' : '' }}">
          <label for="segment">SEGMENT <span class="req">*</span></label>
          <select class="select" id="segment" name="segment" required>
            @foreach(['urban', 'adventure', 'performance', 'premium'] as $seg)
              <option value="{{ $seg }}" @selected(old('segment', $brand->segment) === $seg)>{{ ucfirst($seg) }}</option>
            @endforeach
          </select>
          @error('segment')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="field {{ $errors->has('accent') ? 'has-error' : '' }}">
          <label for="accent">ACCENT COLOR <span class="req">*</span></label>
          <div class="color-pick">
            <input type="color" id="accent" name="accent" value="{{ old('accent', $brand->accent ?? '#e10600') }}">
          </div>
          @error('accent')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="sub-head">Preview — how the brand card looks</div>

        <div class="field full">
          <div class="brand-card" id="brand-preview" style="max-width:300px">
            <span class="stripe" id="pv-stripe" style="background:{{ old('accent', $brand->accent ?? '#e10600') }}"></span>
            <h3 id="pv-name">{{ old('name', $brand->name) ?: 'BRAND' }}</h3>
            <div class="seg"><span id="pv-seg">{{ ucfirst(old('segment', $brand->segment ?? 'urban')) }}</span> — <span id="pv-origin">{{ old('origin', $brand->origin) ?: 'Origin' }}</span></div>
            <div class="dim" id="pv-tag" style="font-size:0.76rem;margin-top:0.4rem">{{ old('tagline', $brand->tagline) }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="{{ route('admin.brands.index') }}">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ $brand->exists ? 'Save changes' : 'Create brand' }}</button>
  </div>
</form>

<script>
(function () {
  document.querySelectorAll('.lang-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      const shell = tab.closest('.form-section');
      shell.querySelectorAll('.lang-tab').forEach(t => t.classList.toggle('active', t === tab));
      shell.querySelectorAll('.lang-pane').forEach(p => p.hidden = p.dataset.pane !== tab.dataset.pane);
    });
  });

  const bind = (id, fn) => ['input', 'change'].forEach(ev =>
    document.getElementById(id).addEventListener(ev, (e) => fn(e.target.value)));
  bind('name', v => document.getElementById('pv-name').textContent = v || 'BRAND');
  bind('segment', v => document.getElementById('pv-seg').textContent = v.charAt(0).toUpperCase() + v.slice(1));
  bind('origin', v => document.getElementById('pv-origin').textContent = v || 'Origin');
  bind('tagline', v => document.getElementById('pv-tag').textContent = v);
  bind('accent', v => document.getElementById('pv-stripe').style.background = v);
})();
</script>
@endsection
