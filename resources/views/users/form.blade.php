@extends('layouts.app')

@section('title', $user->exists ? 'Edit Staff' : 'New Staff')
@section('crumb', $user->exists ? 'Edit Staff' : 'New Staff')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Access control / {{ $user->exists ? 'Edit' : 'Create' }}</div>
    <h1 class="page-title">{{ $user->exists ? 'EDIT' : 'NEW' }} <span class="accent">ACCOUNT</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.users.index') }}">← Back</a>
</div>

<form class="card form-card" method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
  @csrf
  @if($user->exists) @method('PUT') @endif

  <div class="form-grid">
    <div class="field {{ $errors->has('name') ? 'has-error' : '' }}">
      <label for="name">FULL NAME <span class="req">*</span></label>
      <input class="input" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="100">
      @error('name')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('email') ? 'has-error' : '' }}">
      <label for="email">EMAIL <span class="req">*</span></label>
      <input class="input" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="190" autocomplete="off">
      @error('email')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('role_id') ? 'has-error' : '' }}">
      <label for="role_id">ROLE <span class="req">*</span></label>
      <select class="select" id="role_id" name="role_id" required>
        @foreach($roles as $r)
          <option value="{{ $r->id }}" @selected((string) old('role_id', $user->role_id) === (string) $r->id)>
            {{ $r->name }}{{ $r->is_admin ? ' — full access' : '' }}
          </option>
        @endforeach
      </select>
      <span class="hint" id="role-hint">{{ $roles->firstWhere('id', old('role_id', $user->role_id))?->description ?? __('Pick what this account can reach under Access control → Roles.') }}</span>
      @error('role_id')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('password') ? 'has-error' : '' }}">
      <label for="password">PASSWORD {{ $user->exists ? '' : '*' }}</label>
      <input class="input" id="password" name="password" type="password" {{ $user->exists ? '' : 'required' }} autocomplete="new-password">
      @if($user->exists)<span class="hint">Leave blank to keep the current password.</span>@endif
      <span class="hint">Min 10 chars, mixed case + numbers.</span>
      @error('password')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
      <label for="password_confirmation">CONFIRM PASSWORD</label>
      <input class="input" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
      @error('password_confirmation')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('brands') ? 'has-error' : '' }}">
      <label>ASSIGNED BRANDS <span class="req" id="brands-req">*</span></label>
      <span class="hint" style="margin-bottom:0.5rem">{{ __('Brand-scoped roles only manage these brands. Administrator roles ignore this.') }}</span>
      <div class="check-grid">
        @php $assigned = old('brands', $user->exists ? $user->brands->pluck('id')->all() : []); @endphp
        @foreach($brands as $b)
          <div class="checkbox-row">
            <input type="checkbox" id="brand-{{ $b->id }}" name="brands[]" value="{{ $b->id }}" @checked(in_array($b->id, $assigned))>
            <label for="brand-{{ $b->id }}"><span class="cell-brand"><span class="dot" style="background:{{ $b->accent }}"></span>{{ $b->name }}</span></label>
          </div>
        @endforeach
      </div>
      @error('brands')<span class="err">{{ $message }}</span>@enderror
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="{{ route('admin.users.index') }}">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ $user->exists ? 'Save changes' : 'Create account' }}</button>
  </div>
</form>

<script>
  // Show the chosen role's description under the select.
  const roleMeta = @json($roles->mapWithKeys(fn ($r) => [$r->id => $r->description ?? ($r->is_admin ? 'Full access to every section.' : '')]));
  const roleSel = document.getElementById('role_id');
  const roleHint = document.getElementById('role-hint');
  roleSel?.addEventListener('change', () => { roleHint.textContent = roleMeta[roleSel.value] || ''; });
</script>
@endsection
