@extends('layouts.app')

@section('title', $motorcycle->exists ? 'Edit Motorcycle' : 'New Motorcycle')
@section('crumb', $motorcycle->exists ? 'Edit Motorcycle' : 'New Motorcycle')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Catalogue / {{ $motorcycle->exists ? 'Edit' : 'Create' }}</div>
    <h1 class="page-title">{{ $motorcycle->exists ? 'EDIT' : 'NEW' }} <span class="accent">MODEL</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.motorcycles.index') }}">← Back</a>
</div>

<form class="card form-card" method="POST" action="{{ $motorcycle->exists ? route('admin.motorcycles.update', $motorcycle) : route('admin.motorcycles.store') }}">
  @csrf
  @if($motorcycle->exists) @method('PUT') @endif

  <div class="form-grid">
    <div class="field {{ $errors->has('brand_id') ? 'has-error' : '' }}">
      <label for="brand_id">BRAND <span class="req">*</span></label>
      <select class="select" id="brand_id" name="brand_id" required>
        <option value="">— Select brand —</option>
        @foreach($brands as $b)
          <option value="{{ $b->id }}" @selected((string) old('brand_id', $motorcycle->brand_id) === (string) $b->id)>{{ $b->name }} — {{ ucfirst($b->segment) }}</option>
        @endforeach
      </select>
      @if($brands->isEmpty())<span class="hint">No brands assigned to your account — ask an administrator.</span>@endif
      @error('brand_id')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('name') ? 'has-error' : '' }}">
      <label for="name">MODEL NAME <span class="req">*</span></label>
      <input class="input" id="name" name="name" value="{{ old('name', $motorcycle->name) }}" required maxlength="120" placeholder="NOMAD ATLAS 900">
      @error('name')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('price') ? 'has-error' : '' }}">
      <label for="price">PRICE (USD) <span class="req">*</span></label>
      <input class="input" id="price" name="price" type="number" min="0" value="{{ old('price', $motorcycle->price) }}" required>
      @error('price')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('monthly_from') ? 'has-error' : '' }}">
      <label for="monthly_from">MONTHLY FROM (USD) <span class="req">*</span></label>
      <input class="input" id="monthly_from" name="monthly_from" type="number" min="0" value="{{ old('monthly_from', $motorcycle->monthly_from) }}" required>
      @error('monthly_from')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('engine_cc') ? 'has-error' : '' }}">
      <label for="engine_cc">ENGINE (CC)</label>
      <input class="input" id="engine_cc" name="engine_cc" type="number" min="0" value="{{ old('engine_cc', $motorcycle->engine_cc) }}">
      @error('engine_cc')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('power_hp') ? 'has-error' : '' }}">
      <label for="power_hp">POWER (HP)</label>
      <input class="input" id="power_hp" name="power_hp" type="number" min="0" value="{{ old('power_hp', $motorcycle->power_hp) }}">
      @error('power_hp')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('weight_kg') ? 'has-error' : '' }}">
      <label for="weight_kg">WEIGHT (KG)</label>
      <input class="input" id="weight_kg" name="weight_kg" type="number" min="0" value="{{ old('weight_kg', $motorcycle->weight_kg) }}">
      @error('weight_kg')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('top_speed_kmh') ? 'has-error' : '' }}">
      <label for="top_speed_kmh">TOP SPEED (KM/H)</label>
      <input class="input" id="top_speed_kmh" name="top_speed_kmh" type="number" min="0" value="{{ old('top_speed_kmh', $motorcycle->top_speed_kmh) }}">
      @error('top_speed_kmh')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('tagline') ? 'has-error' : '' }}">
      <label for="tagline">TAGLINE</label>
      <input class="input" id="tagline" name="tagline" value="{{ old('tagline', $motorcycle->tagline) }}" maxlength="160" placeholder="Street electric, tuned for the city grid">
      @error('tagline')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('image') ? 'has-error' : '' }}">
      <label for="image">IMAGE URL</label>
      <input class="input" id="image" name="image" type="url" value="{{ old('image', $motorcycle->image) }}" placeholder="https://…">
      @error('image')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full">
      <label>PAINT OPTIONS</label>
      <div id="color-rows">
        @forelse(old('colors', $motorcycle->colors ?? []) as $c)
          <div class="color-row">
            <input class="input" name="colors[{{ $loop->index }}][name]" value="{{ $c['name'] ?? '' }}" placeholder="Color name">
            <input type="color" name="colors[{{ $loop->index }}][hex]" value="{{ $c['hex'] ?? '#000000' }}">
          </div>
        @empty
          <div class="color-row">
            <input class="input" name="colors[0][name]" placeholder="Color name">
            <input type="color" name="colors[0][hex]" value="#0b0b0d">
          </div>
        @endforelse
      </div>
      <button type="button" class="btn btn-ghost btn-sm" id="add-color" style="margin-top:0.3rem">+ Add color</button>
      @error('colors.*.hex')<span class="err">{{ $message }}</span>@enderror
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="{{ route('admin.motorcycles.index') }}">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ $motorcycle->exists ? 'Save changes' : 'Create model' }}</button>
  </div>
</form>

<script>
  let colorIdx = {{ count(old('colors', $motorcycle->colors ?? [1])) }};
  document.getElementById('add-color').addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'color-row';
    row.innerHTML = `<input class="input" name="colors[${colorIdx}][name]" placeholder="Color name">
      <input type="color" name="colors[${colorIdx}][hex]" value="#0b0b0d">`;
    document.getElementById('color-rows').appendChild(row);
    colorIdx++;
  });
</script>
@endsection
