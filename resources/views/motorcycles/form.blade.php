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

<form class="form-shell form-split" method="POST" enctype="multipart/form-data"
      action="{{ $motorcycle->exists ? route('admin.motorcycles.update', $motorcycle) : route('admin.motorcycles.store') }}">
  @csrf
  @if($motorcycle->exists) @method('PUT') @endif

  <div class="form-main">

    {{-- ---------- Model — bilingual content ---------- --}}
    <div class="card form-section">
      <div class="card-head">
        <h3>Model</h3>
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
              <label for="name">MODEL NAME <span class="req">*</span></label>
              <input class="input" id="name" name="name" value="{{ old('name', $motorcycle->name) }}" required maxlength="120" placeholder="NOMAD ATLAS 900">
              @error('name')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field full {{ $errors->has('tagline') ? 'has-error' : '' }}">
              <label for="tagline">TAGLINE</label>
              <input class="input" id="tagline" name="tagline" value="{{ old('tagline', $motorcycle->tagline) }}" maxlength="160" placeholder="Continent-crushing flagship">
              @error('tagline')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field full {{ $errors->has('description') ? 'has-error' : '' }}">
              <label for="description">DESCRIPTION</label>
              <textarea class="input" id="description" name="description" rows="3" maxlength="2000" placeholder="A short paragraph shown on the model's page…">{{ old('description', $motorcycle->description) }}</textarea>
              @error('description')<span class="err">{{ $message }}</span>@enderror
            </div>
          </div>
        </div>

        <div class="lang-pane" data-pane="ar" hidden>
          <div class="form-grid" dir="rtl">
            <div class="field full {{ $errors->has('name_ar') ? 'has-error' : '' }}">
              <label for="name_ar">اسم الموديل</label>
              <input class="input" id="name_ar" name="name_ar" value="{{ old('name_ar', $motorcycle->name_ar) }}" maxlength="120" placeholder="نوماد أطلس 900">
              @error('name_ar')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field full {{ $errors->has('tagline_ar') ? 'has-error' : '' }}">
              <label for="tagline_ar">الشعار</label>
              <input class="input" id="tagline_ar" name="tagline_ar" value="{{ old('tagline_ar', $motorcycle->tagline_ar) }}" maxlength="160" placeholder="وحش القارات">
              @error('tagline_ar')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field full {{ $errors->has('description_ar') ? 'has-error' : '' }}">
              <label for="description_ar">الوصف</label>
              <textarea class="input" id="description_ar" name="description_ar" rows="3" maxlength="2000" placeholder="فقرة قصيرة تظهر في صفحة الموديل…">{{ old('description_ar', $motorcycle->description_ar) }}</textarea>
              @error('description_ar')<span class="err">{{ $message }}</span>@enderror
            </div>
          </div>
        </div>

        <div class="form-grid" style="margin-top:1.2rem">
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
        </div>
      </div>
    </div>

    {{-- ---------- Pricing & specs ---------- --}}
    <div class="card form-section">
      <div class="card-head">
        <h3>Pricing &amp; specs</h3>
        <span class="card-hint">Commercials and the optional spec table</span>
      </div>
      <div class="form-grid">
        <div class="field {{ $errors->has('price') ? 'has-error' : '' }}">
          <label for="price">PRICE <span class="req">*</span></label>
          <input class="input" id="price" name="price" type="number" min="0" value="{{ old('price', $motorcycle->price) }}" required>
          @error('price')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="field {{ $errors->has('currency_id') ? 'has-error' : '' }}">
          <label for="currency_id">CURRENCY <span class="req">*</span></label>
          <select class="select" id="currency_id" name="currency_id" required>
            <option value="">— Select currency —</option>
            @foreach($currencies as $c)
              <option value="{{ $c->id }}" @selected((string) old('currency_id', $motorcycle->currency_id) === (string) $c->id)>{{ $c->code }} — {{ $c->name }} ({{ $c->symbol }})</option>
            @endforeach
          </select>
          @if($currencies->isEmpty())<span class="hint">No currencies yet — an admin can add them under Group → Currencies.</span>@endif
          @error('currency_id')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="field {{ $errors->has('monthly_from') ? 'has-error' : '' }}">
          <label for="monthly_from">MONTHLY FROM <span class="req">*</span></label>
          <input class="input" id="monthly_from" name="monthly_from" type="number" min="0" value="{{ old('monthly_from', $motorcycle->monthly_from) }}" required>
          @error('monthly_from')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="sub-head">Performance — optional spec table</div>

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
      </div>
    </div>

    <div class="form-actions">
      <a class="btn btn-ghost" href="{{ route('admin.motorcycles.index') }}">Cancel</a>
      <button class="btn btn-primary" type="submit">{{ $motorcycle->exists ? 'Save changes' : 'Create model' }}</button>
    </div>
  </div>

  {{-- ---------- Right rail ---------- --}}
  <div class="form-rail">
    <div class="card form-section">
      <div class="card-head">
        <h3>Gallery &amp; paint</h3>
      </div>
      <div class="section-body">
        <div id="dropzone" role="button" tabindex="0" aria-label="Upload images">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="dz-ic"><path d="M12 16V4m0 0l-4 4m4-4l4 4"/><path d="M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3"/></svg>
          <div>Drop images here or <span class="dz-link">browse files</span></div>
          <div class="hint" style="margin-top:0.3rem">JPG / PNG / WebP / AVIF — max 12</div>
        </div>
        <input id="file-picker" type="file" accept="image/*" multiple hidden>
        @if($errors->has('new_images.*.file'))<span class="err">{{ $errors->first('new_images.*.file') }}</span>@endif

        <div id="gallery-grid">
          @foreach($motorcycle->images as $img)
            <div class="g-tile" draggable="true">
              <img src="{{ $img->url }}" alt="{{ $img->color_name ?? $motorcycle->name }}" draggable="false">
              <div class="g-meta">
                <input type="color" name="images[{{ $img->id }}][color_hex]" value="{{ $img->color_hex ?? '#888888' }}" title="Paint color">
                <input class="input g-name" name="images[{{ $img->id }}][color_name]" value="{{ $img->color_name }}" placeholder="Paint name" maxlength="60">
              </div>
              <input type="hidden" class="sort-input" name="images[{{ $img->id }}][sort]" value="{{ $loop->index }}">
              <label class="g-del"><input type="checkbox" name="images[{{ $img->id }}][delete]" value="1"> Remove</label>
              <span class="g-cover">Cover</span>
            </div>
          @endforeach
        </div>
        <span class="hint" style="display:block;margin-top:0.5rem">Drag tiles to reorder — first is the cover. Name a color to make it a paint swatch on the site.</span>

        <div class="field {{ $errors->has('image') ? 'has-error' : '' }}" style="margin-top:1.1rem">
          <label for="image">FALLBACK COVER URL</label>
          <input class="input" id="image" name="image" type="url" value="{{ old('image', $motorcycle->image) }}" placeholder="https://…" aria-describedby="image-hint">
          <span class="hint" id="image-hint">Used only when the gallery is empty — external link.</span>
          @error('image')<span class="err">{{ $message }}</span>@enderror
        </div>
      </div>
    </div>

    <div class="card form-section">
      <div class="card-head"><h3>Visibility</h3></div>
      <div class="section-body">
        <div class="checkbox-row">
          <input type="checkbox" id="is_active" name="is_active" value="1" @checked(old('is_active', $motorcycle->exists ? $motorcycle->is_active : true))>
          <label for="is_active">Listed on the website</label>
        </div>
        <span class="hint" style="margin-top:0.6rem;display:block">Turning this off hides the model from the public site and test-ride booking.</span>
      </div>
    </div>

    <div class="card form-section">
      <div class="card-head"><h3>Availability</h3></div>
      <div class="section-body">
        <div class="field {{ $errors->has('quantity') ? 'has-error' : '' }}">
          <label for="quantity">QUANTITY IN STOCK <span class="req">*</span></label>
          <input class="input" id="quantity" name="quantity" type="number" min="0" value="{{ old('quantity', $motorcycle->exists ? $motorcycle->quantity : 0) }}" required>
          @error('quantity')<span class="err">{{ $message }}</span>@enderror
        </div>

        <div class="field {{ $errors->has('showrooms') ? 'has-error' : '' }}" style="margin-top:1rem">
          <label>STOCKED IN SHOWROOMS</label>
          <div class="check-list">
            @foreach($showrooms as $s)
              <div class="checkbox-row">
                <input type="checkbox" id="sr-{{ $s->id }}" name="showrooms[]" value="{{ $s->id }}"
                  @checked(in_array($s->id, old('showrooms', $motorcycle->exists ? $motorcycle->showrooms->pluck('id')->all() : [])))>
                <label for="sr-{{ $s->id }}">{{ $s->city }}, {{ $s->country }}</label>
              </div>
            @endforeach
          </div>
          @error('showrooms')<span class="err">{{ $message }}</span>@enderror
        </div>
      </div>
    </div>
  </div>
</form>

<script>
(function () {
  /* ---------- Language tabs ---------- */
  document.querySelectorAll('.lang-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      const shell = tab.closest('.form-section');
      shell.querySelectorAll('.lang-tab').forEach(t => t.classList.toggle('active', t === tab));
      shell.querySelectorAll('.lang-pane').forEach(p => p.hidden = p.dataset.pane !== tab.dataset.pane);
    });
  });

  /* ---------- Gallery: dropzone + drag-sort ---------- */
  const grid = document.getElementById('gallery-grid');
  const dz = document.getElementById('dropzone');
  const picker = document.getElementById('file-picker');
  let newIdx = 0;
  let dragged = null;

  function refreshOrder() {
    grid.querySelectorAll('.g-tile').forEach((t, i) => {
      const s = t.querySelector('.sort-input');
      if (s) s.value = i;
      t.classList.toggle('is-cover', i === 0);
    });
  }

  function addFiles(files) {
    [...files].filter(f => f.type.startsWith('image/')).forEach(file => {
      const i = newIdx++;
      const tile = document.createElement('div');
      tile.className = 'g-tile g-new';
      tile.draggable = true;
      tile.innerHTML = `
        <img alt="" draggable="false">
        <div class="g-meta">
          <input type="color" name="new_images[${i}][color_hex]" value="#888888" title="Paint color">
          <input class="input g-name" name="new_images[${i}][color_name]" placeholder="Paint name" maxlength="60">
        </div>
        <input type="hidden" class="sort-input" name="new_images[${i}][sort]">
        <button type="button" class="g-remove" title="Remove">×</button>
        <span class="g-tag-new">New</span>
        <span class="g-cover">Cover</span>`;
      const fileInput = document.createElement('input');
      fileInput.type = 'file';
      fileInput.name = `new_images[${i}][file]`;
      fileInput.hidden = true;
      const dt = new DataTransfer();
      dt.items.add(file);
      fileInput.files = dt.files;
      tile.appendChild(fileInput);
      tile.querySelector('img').src = URL.createObjectURL(file);
      tile.querySelector('.g-remove').addEventListener('click', () => { tile.remove(); refreshOrder(); });
      grid.appendChild(tile);
    });
    refreshOrder();
  }

  dz.addEventListener('click', () => picker.click());
  dz.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); picker.click(); } });
  picker.addEventListener('change', () => { addFiles(picker.files); picker.value = ''; });
  dz.addEventListener('dragover', (e) => { e.preventDefault(); dz.classList.add('over'); });
  dz.addEventListener('dragleave', () => dz.classList.remove('over'));
  dz.addEventListener('drop', (e) => { e.preventDefault(); dz.classList.remove('over'); addFiles(e.dataTransfer.files); });

  grid.addEventListener('dragstart', (e) => {
    dragged = e.target.closest('.g-tile');
    if (dragged) dragged.classList.add('dragging');
  });
  grid.addEventListener('dragover', (e) => {
    e.preventDefault();
    if (!dragged) return;
    const siblings = [...grid.querySelectorAll('.g-tile:not(.dragging)')];
    const next = siblings.find(t => {
      const r = t.getBoundingClientRect();
      return e.clientY < r.top + r.height / 2 || (e.clientY < r.bottom && e.clientX < r.left + r.width / 2);
    });
    if (next) grid.insertBefore(dragged, next);
    else grid.appendChild(dragged);
  });
  grid.addEventListener('dragend', () => {
    if (dragged) dragged.classList.remove('dragging');
    dragged = null;
    refreshOrder();
  });

  // mark existing tiles for deletion
  grid.addEventListener('change', (e) => {
    if (e.target.matches('.g-del input')) {
      e.target.closest('.g-tile').classList.toggle('marked-del', e.target.checked);
    }
  });

  refreshOrder();
})();
</script>
@endsection
