<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in — VANTA MOTO CMS</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Cpath d='M13 13 L32 51 L51 13 L42 13 L32 33.5 L22 13 Z' fill='%23e10600'/%3E%3C/svg%3E">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,100..900&family=JetBrains+Mono:wght@400;500;600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/cms.css') }}">
</head>
<body>
<div class="login-split">

  {{-- ---------- Visual side ---------- --}}
  <div class="login-visual" aria-hidden="true">
    <div class="lv-photo"></div>
    <div class="lv-shade"></div>
    <div class="login-grid-lines"></div>

    <a class="brand-mark lv-logo" href="{{ url('/') }}">
      <span class="bm-icon"><svg width="18" height="18" viewBox="0 0 64 64"><path d="M13 13 L32 51 L51 13 L42 13 L32 33.5 L22 13 Z" fill="#fff"/></svg></span>
      <span class="bm-text">VANTA<span class="accent">MOTO</span><span class="sub">{{ __('CONTROL ROOM') }}</span></span>
    </a>

    <div class="lv-copy">
      <div class="lv-rings">
        <span class="lv-ring r1"></span>
        <span class="lv-ring r2"></span>
        <span class="lv-ring r3"></span>
      </div>
      <p class="lv-tag">{{ __('MIDDLE EAST MOTORCYCLE GROUP') }}</p>
      <h2 class="lv-title">{{ __('BUILT TO') }}<br>{{ __('BE') }} <span>{{ __('RIDDEN') }}</span></h2>
      <p class="lv-sub">{{ __('Sales · Test rides · Offers · Showrooms — one control room.') }}</p>
    </div>
  </div>

  {{-- ---------- Form side ---------- --}}
  <div class="login-panel">
    <form class="login-card" method="POST" action="{{ route('login.store') }}">
      @csrf
      <span class="lp-mark"><svg width="30" height="30" viewBox="0 0 64 64"><path d="M13 13 L32 51 L51 13 L42 13 L32 33.5 L22 13 Z" fill="#e10600"/></svg></span>
      <h1 class="lp-title">{{ __('ADMIN LOGIN') }}</h1>
      <p class="lp-sub">{{ __('Log in to your panel to proceed') }}</p>

      <div class="field {{ $errors->has('email') ? 'has-error' : '' }}">
        <label for="email">{{ __('Email Address') }}</label>
        <input class="input" id="email" name="email" type="email" dir="ltr" value="{{ old('email') }}"
               placeholder="{{ __('Email Address') }}" required autofocus autocomplete="username">
        @error('email')<span class="err">{{ $message }}</span>@enderror
      </div>

      <div class="field {{ $errors->has('password') ? 'has-error' : '' }}">
        <label for="password">{{ __('Password') }}</label>
        <div class="lp-pass">
          <input class="input" id="password" name="password" type="password" dir="ltr"
                 placeholder="{{ __('Password') }}" required autocomplete="current-password">
          <button type="button" class="lp-eye" id="lp-eye" aria-label="{{ __('Show password') }}" tabindex="-1">
            <svg id="lp-eye-on" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-6.5 10-6.5S22 12 22 12s-3.5 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="2.6"/></svg>
            <svg id="lp-eye-off" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M2 12s3.5-6.5 10-6.5c2 0 3.7.6 5.1 1.5M22 12s-3.5 6.5-10 6.5c-2 0-3.7-.6-5.1-1.5"/><path d="M3 3l18 18"/></svg>
          </button>
        </div>
        @error('password')<span class="err">{{ $message }}</span>@enderror
      </div>

      <div class="checkbox-row">
        <input type="checkbox" id="remember" name="remember" value="1">
        <label for="remember">{{ __('Remember Me') }}</label>
      </div>

      <button class="btn lp-btn" type="submit">{{ __('LOGIN') }}</button>
      <p class="lp-foot">{{ __('AUTHORIZED PERSONNEL ONLY') }}</p>
    </form>
  </div>
</div>

<script>
  const eye = document.getElementById('lp-eye');
  eye.addEventListener('click', () => {
    const pass = document.getElementById('password');
    const show = pass.type === 'password';
    pass.type = show ? 'text' : 'password';
    document.getElementById('lp-eye-on').style.display = show ? 'none' : '';
    document.getElementById('lp-eye-off').style.display = show ? '' : 'none';
  });
</script>
</body>
</html>
