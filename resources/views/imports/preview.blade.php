@extends('layouts.app')

@section('title', "Import preview — {$title}")
@section('crumb', "Import {$title}")

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Bulk import / Preview</div>
    <h1 class="page-title">REVIEW <span class="accent">ROWS</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ $listUrl }}">← Back</a>
</div>

<div class="card form-section">
  <div class="card-head">
    <h3>{{ $total }} row(s) found</h3>
    <span class="card-hint">
      <span class="badge lime">{{ $new }} new</span>
      <span class="badge" style="margin-inline:0.4rem">{{ $updates }} updates</span>
      @if($toSkip)<span class="badge" style="margin-inline-end:0.4rem">{{ $toSkip }} to skip</span>@endif
      <span class="badge red">{{ $errors }} with errors</span>
    </span>
  </div>
  <table class="table">
    <thead>
      <tr><th style="width:60px">Row</th><th>Name</th><th>Status</th><th>Notes</th></tr>
    </thead>
    <tbody>
      @foreach($rows as $r)
      <tr>
        <td class="num">{{ $r['num'] }}</td>
        <td><div class="cell-title" style="font-weight:500;font-size:0.84rem">{{ $r['name'] }}</div></td>
        <td>
          <span class="badge {{ $r['status'] === 'error' ? 'red' : ($r['status'] === 'update' ? '' : 'lime') }}">
            {{ strtoupper($r['status']) }}
          </span>
        </td>
        <td class="dim" style="font-size:0.72rem">
          @foreach($r['errors'] as $e)<div class="err" style="margin:0">{{ $e }}</div>@endforeach
          @foreach($r['warnings'] as $w)<div>{{ $w }}</div>@endforeach
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @if($total > $shown)
    <div class="pagination-bar"><span>Showing first {{ $shown }} of {{ $total }} rows — all rows are validated on import.</span></div>
  @endif
</div>

<div class="form-actions" style="border:0;background:none;padding:0.4rem 0">
  <a class="btn btn-ghost" href="{{ $listUrl }}">Cancel</a>
  <button class="btn btn-primary" type="button" id="commit-btn" @disabled($errors === $total)>
    Import {{ $new + $updates }} valid row(s)
  </button>
</div>

<div class="loader-veil" id="loader" hidden>
  <div id="prog-box">
    <div class="spinner"></div>
    <div class="loader-text" id="prog-label">Preparing import…</div>
    <div class="prog"><div class="prog-fill" id="prog-bar"></div></div>
  </div>
  <div id="done-box" hidden>
    <div class="loader-text" id="done-summary"></div>
    <div id="done-failures"></div>
    <a class="btn btn-primary" id="done-link" href="{{ $listUrl }}">Back to list</a>
  </div>
</div>

<script>
(function () {
  const btn = document.getElementById('commit-btn');
  const veil = document.getElementById('loader');
  const label = document.getElementById('prog-label');
  const bar = document.getElementById('prog-bar');
  const box = document.getElementById('prog-box');
  const doneBox = document.getElementById('done-box');
  const summary = document.getElementById('done-summary');
  const failBox = document.getElementById('done-failures');
  const token = document.querySelector('meta[name="csrf-token"]').content;

  const url = @json($commitUrl);
  const path = @json($filePath);
  const settings = @json($importSettings ?? []);
  let offset = 0, created = 0, updated = 0, skipped = 0;
  const failures = [];

  function step() {
    return fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ path, offset, ...settings }),
    }).then(r => {
      if (!r.ok) throw new Error('Import failed — HTTP ' + r.status);
      return r.json();
    }).then(d => {
      created += d.created;
      updated += d.updated;
      skipped += d.skipped || 0;
      failures.push(...d.failures);
      offset = d.processed;
      const left = d.total - offset;
      label.textContent = `Importing… ${offset} of ${d.total} rows` + (left ? ` — ${left} left` : '');
      bar.style.width = (d.total ? Math.round(offset / d.total * 100) : 100) + '%';
      return d.done ? finish() : step();
    });
  }

  function finish() {
    box.hidden = true;
    summary.textContent =
      `Done — ${created} created, ${updated} updated` +
      (skipped ? `, ${skipped} skipped` : '') +
      (failures.length ? `, ${failures.length} failed` : '');
    if (failures.length) {
      failBox.innerHTML = failures.slice(0, 12).map(f =>
        `<div class="done-fail">Row ${f.row} — ${f.name}: ${f.errors.join('; ')}</div>`
      ).join('') + (failures.length > 12 ? `<div class="done-fail">…and ${failures.length - 12} more</div>` : '');
    }
    doneBox.hidden = false;
  }

  btn?.addEventListener('click', () => {
    veil.hidden = false;
    step().catch(err => {
      label.textContent = err.message;
      bar.style.background = 'var(--lava)';
    });
  });
})();
</script>
@endsection
