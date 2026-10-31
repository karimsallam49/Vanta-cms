@extends('layouts.app')

@section('title', "Import {$title}")
@section('crumb', "Import {$title}")

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Catalogue / Bulk import</div>
    <h1 class="page-title">IMPORT <span class="accent">{{ strtoupper($title) }}</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ $listUrl }}">← Back</a>
</div>

<div class="form-split">
  <div class="form-main">
    <div class="card form-section">
      <div class="card-head">
        <h3>Upload sheet</h3>
        <span class="card-hint">You'll review a preview before anything is saved</span>
      </div>
      <div class="section-body">
        <form method="POST" action="{{ $previewUrl }}" enctype="multipart/form-data" data-loader id="import-form">
          @csrf
          <div id="import-dz" role="button" tabindex="0" aria-label="Upload file">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="dz-ic"><path d="M12 16V4m0 0l-4 4m4-4l4 4"/><path d="M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3"/></svg>
            <div>Drop your file here or <span class="dz-link">browse</span></div>
            <div class="hint" style="margin-top:0.3rem">.xlsx / .xls / .csv — up to 10 MB</div>
            <div id="import-name" class="import-name" hidden></div>
          </div>
          <input id="import-file" name="file" type="file" accept=".xlsx,.xls,.csv" hidden required>
          @error('file')<span class="err">{{ $message }}</span>@enderror

          <div class="form-actions" style="border:0;background:none;padding:1rem 0 0">
            <button class="btn btn-primary" type="submit" id="preview-btn" disabled>Preview rows →</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="form-rail">
    <div class="card form-section">
      <div class="card-head">
        <h3>Import settings</h3>
        <span class="card-hint">How to handle edge cases while importing</span>
      </div>
      <div class="section-body">
        @foreach($settings as $f)
          <div class="field" style="margin-bottom:0.9rem">
            <label for="set-{{ $f['name'] }}">{{ $f['label'] }}</label>
            <select class="select" id="set-{{ $f['name'] }}" name="{{ $f['name'] }}" form="import-form">
              @foreach($f['options'] as $v => $label)
                <option value="{{ $v }}">{{ $label }}</option>
              @endforeach
            </select>
            <span class="hint">{{ $f['hint'] }}</span>
          </div>
        @endforeach
      </div>
    </div>

    <div class="card form-section">
      <div class="card-head"><h3>Template</h3></div>
      <div class="section-body">
        <span class="hint" style="display:block;margin-bottom:0.9rem">Download the ready-made sheet — fill it and upload it back.</span>
        <a class="btn btn-ghost" href="{{ $templateUrl }}" style="width:100%">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M12 4v12m0 0l-4-4m4 4l4-4"/><path d="M4 20h16"/></svg>
          Download .xlsx template
        </a>
      </div>
    </div>

    <div class="card form-section">
      <div class="card-head"><h3>Expected columns</h3></div>
      <div class="section-body">
        <div class="import-cols">
          @foreach($columns as [$col, $note])
            <div class="ic-row">
              <code>{{ $col }}</code>
              <span class="dim">{{ $note }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>

<div class="loader-veil" id="loader" hidden>
  <div class="spinner"></div>
  <div class="loader-text">Reading your sheet…</div>
</div>

<script>
(function () {
  const dz = document.getElementById('import-dz');
  const input = document.getElementById('import-file');
  const nameEl = document.getElementById('import-name');
  const btn = document.getElementById('preview-btn');

  function pick(file) {
    if (!file) return;
    const dt = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    nameEl.textContent = file.name;
    nameEl.hidden = false;
    btn.disabled = false;
  }

  dz.addEventListener('click', () => input.click());
  dz.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); } });
  input.addEventListener('change', () => pick(input.files[0]));
  dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('over'); });
  dz.addEventListener('dragleave', () => dz.classList.remove('over'));
  dz.addEventListener('drop', e => { e.preventDefault(); dz.classList.remove('over'); pick(e.dataTransfer.files[0]); });

  document.querySelectorAll('form[data-loader]').forEach(f =>
    f.addEventListener('submit', () => document.getElementById('loader').hidden = false));
})();
</script>
@endsection
