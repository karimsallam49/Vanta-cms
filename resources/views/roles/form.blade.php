@extends('layouts.app')

@section('title', $role->exists ? 'Edit Role' : 'New Role')
@section('crumb', $role->exists ? 'Edit Role' : 'New Role')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Access control / {{ $role->exists ? __('Edit') : __('Create') }}</div>
    <h1 class="page-title">{{ $role->exists ? __('EDIT') : __('NEW') }} <span class="accent">{{ __('ROLE') }}</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.roles.index') }}">← {{ __('Back') }}</a>
</div>

<form method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}" id="role-form">
  @csrf
  @if($role->exists) @method('PUT') @endif

  <div class="form-split">
    <div class="form-main">

      {{-- ---------- Identity ---------- --}}
      <div class="card form-card">
        <div class="card-head"><h3>{{ __('Identity') }}</h3></div>
        <div class="form-grid" style="padding:1.4rem">
          <div class="field {{ $errors->has('name') ? 'has-error' : '' }}">
            <label for="name">{{ __('ROLE NAME') }} <span class="req">*</span></label>
            <input class="input" id="name" name="name" value="{{ old('name', $role->name) }}" required maxlength="60" placeholder="{{ __('e.g. Content Editor') }}">
            @error('name')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="field {{ $errors->has('slug') ? 'has-error' : '' }}">
            <label for="slug">{{ __('SLUG') }}</label>
            <input class="input" id="slug" name="slug" value="{{ old('slug', $role->slug) }}" maxlength="60" placeholder="{{ __('auto from name') }}" dir="ltr">
            @error('slug')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="field full {{ $errors->has('description') ? 'has-error' : '' }}">
            <label for="description">{{ __('DESCRIPTION') }}</label>
            <input class="input" id="description" name="description" value="{{ old('description', $role->description) }}" maxlength="255" placeholder="{{ __('What this role is for — shown on the roles list') }}">
            @error('description')<span class="err">{{ $message }}</span>@enderror
          </div>
        </div>
      </div>

      {{-- ---------- Permission matrix ---------- --}}
      <div class="card form-card" id="perm-matrix">
        <div class="card-head">
          <h3>{{ __('Permissions') }}</h3>
          <div style="display:flex;gap:0.5rem">
            <button type="button" class="btn btn-ghost btn-sm" data-perm-all="on">{{ __('Grant all') }}</button>
            <button type="button" class="btn btn-ghost btn-sm" data-perm-all="off">{{ __('Clear all') }}</button>
          </div>
        </div>

        @error('permissions')<div style="padding:0 1.4rem"><span class="err">{{ $message }}</span></div>@enderror

        @php $granted = old('permissions', $role->permissions ?? []); @endphp
        <div class="perm-grid">
          @foreach($sections as $key => $section)
          <div class="perm-card">
            <div class="perm-card-head">
              <div>
                <div class="perm-title">{{ __($section['label']) }}</div>
                <div class="perm-hint">{{ __($section['hint']) }}</div>
              </div>
              <label class="perm-all">
                <input type="checkbox" data-section-all="{{ $key }}">
                <span>{{ __('All') }}</span>
              </label>
            </div>
            <div class="perm-actions">
              @foreach($section['actions'] as $action => $label)
                @php $perm = "{$key}.{$action}"; @endphp
                <label class="perm-check">
                  <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                         data-section="{{ $key }}" @checked(in_array($perm, $granted))>
                  <span>{{ __($label) }}</span>
                </label>
              @endforeach
            </div>
          </div>
          @endforeach
        </div>
      </div>

      <div class="form-actions">
        <a class="btn btn-ghost" href="{{ route('admin.roles.index') }}">{{ __('Cancel') }}</a>
        <button class="btn btn-primary" type="submit">{{ $role->exists ? __('Save changes') : __('Create role') }}</button>
      </div>
    </div>

    {{-- ---------- Rail ---------- --}}
    <div class="form-rail">
      <div class="card form-section">
        <div class="card-head"><h3>{{ __('Access level') }}</h3></div>
        <div class="section-body">
          <label class="checkbox-row" style="align-items:flex-start">
            <input type="checkbox" name="is_admin" value="1" id="is_admin" @checked(old('is_admin', $role->is_admin))>
            <span>
              <strong>{{ __('Full administrator') }}</strong><br>
              <span class="dim" style="font-size:0.74rem">{{ __('Skips every check — sees and edits all sections. Uncheck it and pick permissions instead.') }}</span>
            </span>
          </label>
        </div>
      </div>

      <div class="card form-section" id="perm-count-card">
        <div class="card-head"><h3>{{ __('Granted') }}</h3></div>
        <div class="section-body">
          <div class="perm-count"><span id="perm-count">0</span> <span class="dim">{{ __('permissions') }}</span></div>
          <div class="dim" style="font-size:0.74rem">{{ __('Users with this role only see the sections ticked on the left.') }}</div>
        </div>
      </div>
    </div>
  </div>
</form>

<script>
(function () {
  const adminBox = document.getElementById('is_admin');
  const matrix = document.getElementById('perm-matrix');
  const countEl = document.getElementById('perm-count');
  const boxes = () => [...document.querySelectorAll('#perm-matrix input[data-section]')];

  const refreshCount = () => {
    countEl.textContent = boxes().filter(b => b.checked).length;
    // Section "All" toggles reflect their children.
    document.querySelectorAll('[data-section-all]').forEach(t => {
      const kids = document.querySelectorAll(`input[data-section="${t.dataset.sectionAll}"]`);
      t.checked = kids.length > 0 && [...kids].every(k => k.checked);
    });
  };

  // Full-admin greys out the matrix — the flag means everything anyway.
  const refreshAdmin = () => {
    const on = adminBox.checked;
    matrix.style.opacity = on ? 0.45 : 1;
    matrix.querySelectorAll('input, button').forEach(el => el.disabled = on);
  };

  document.querySelectorAll('[data-section-all]').forEach(t =>
    t.addEventListener('change', () => {
      document.querySelectorAll(`input[data-section="${t.dataset.sectionAll}"]`)
        .forEach(k => k.checked = t.checked);
      refreshCount();
    }));

  boxes().forEach(b => b.addEventListener('change', refreshCount));
  document.querySelector('[data-perm-all="on"]').addEventListener('click', () => { boxes().forEach(b => b.checked = true); refreshCount(); });
  document.querySelector('[data-perm-all="off"]').addEventListener('click', () => { boxes().forEach(b => b.checked = false); refreshCount(); });
  adminBox.addEventListener('change', refreshAdmin);

  refreshCount();
  refreshAdmin();
})();
</script>
@endsection
