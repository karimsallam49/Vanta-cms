@extends('layouts.app')

@section('title', __('Profile'))
@section('crumb', __('Profile'))

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Account settings') }}</div>
    <h1 class="page-title">{{ __('MY') }} <span class="accent">{{ __('ACCOUNT') }}</span></h1>
  </div>
</div>

<div class="form-split">
  <div class="form-main">
    <form method="POST" action="{{ route('admin.profile.update') }}">
      @csrf @method('PUT')

      <div class="card form-section">
        <div class="card-head">
          <h3>{{ __('Account details') }}</h3>
          <span class="card-hint">{{ __('Update your name, email and password.') }}</span>
        </div>
        <div class="section-body">
          <div class="form-grid">
            <div class="field {{ $errors->has('name') ? 'has-error' : '' }}">
              <label for="name">{{ __('Name') }} <span class="req">*</span></label>
              <input class="input" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="120">
              @error('name')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field {{ $errors->has('email') ? 'has-error' : '' }}">
              <label for="email">{{ __('Email') }} <span class="req">*</span></label>
              <input class="input" id="email" name="email" type="email" dir="ltr" value="{{ old('email', $user->email) }}" required maxlength="190">
              @error('email')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field {{ $errors->has('locale') ? 'has-error' : '' }}">
              <label for="locale">{{ __('Language') }} <span class="req">*</span></label>
              <select class="select" id="locale" name="locale">
                <option value="en" @selected(old('locale', $user->locale) === 'en')>English</option>
                <option value="ar" @selected(old('locale', $user->locale) === 'ar')>العربية</option>
              </select>
              @error('locale')<span class="err">{{ $message }}</span>@enderror
            </div>
          </div>
        </div>
      </div>

      <div class="card form-section">
        <div class="card-head">
          <h3>{{ __('Change password') }}</h3>
          <span class="card-hint">{{ __('Leave blank to keep your current password.') }}</span>
        </div>
        <div class="section-body">
          <div class="form-grid">
            <div class="field {{ $errors->has('current_password') ? 'has-error' : '' }}">
              <label for="current_password">{{ __('Current password') }}</label>
              <input class="input" id="current_password" name="current_password" type="password" dir="ltr" autocomplete="current-password">
              <span class="hint">{{ __('Required only when setting a new password.') }}</span>
              @error('current_password')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field {{ $errors->has('password') ? 'has-error' : '' }}">
              <label for="password">{{ __('New password') }}</label>
              <input class="input" id="password" name="password" type="password" dir="ltr" autocomplete="new-password">
              <span class="hint">{{ __('Minimum 8 characters.') }}</span>
              @error('password')<span class="err">{{ $message }}</span>@enderror
            </div>
            <div class="field">
              <label for="password_confirmation">{{ __('Confirm new password') }}</label>
              <input class="input" id="password_confirmation" name="password_confirmation" type="password" dir="ltr" autocomplete="new-password">
            </div>
          </div>
        </div>
      </div>

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">{{ __('Save changes') }}</button>
      </div>
    </form>
  </div>

  <div class="form-rail">
    <div class="card form-section">
      <div class="card-head"><h3>{{ __('Role') }}</h3></div>
      <div class="section-body">
        <span class="badge {{ $user->isAdmin() ? 'solid' : '' }}">{{ $user->role?->name ?? __('No role') }}</span>
        @unless($user->isAdmin())
          <div style="margin-top:0.9rem">
            @if($user->brands->isEmpty())
              <span class="badge red">{{ __('NO BRANDS ASSIGNED') }}</span>
            @else
              @foreach($user->brands as $b)
                <span class="cell-brand" style="margin-bottom:0.4rem"><span class="dot" style="background:{{ $b->accent }}"></span>{{ $b->name }}</span>
              @endforeach
            @endif
          </div>
        @endunless
      </div>
    </div>
  </div>
</div>
@endsection
