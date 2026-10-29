<!DOCTYPE html>
<html lang="en">
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
<div class="login-wrap">
  <div class="login-grid-lines" aria-hidden="true"></div>

  <form class="login-card" method="POST" action="{{ route('login.store') }}">
    @csrf
    <div class="brand-mark">
      <svg width="26" height="26" viewBox="0 0 64 64" aria-hidden="true"><path d="M13 13 L32 51 L51 13 L42 13 L32 33.5 L22 13 Z" fill="#e10600"/></svg>
      <span>VANTA<span class="accent">MOTO</span><span class="sub">CONTROL ROOM</span></span>
    </div>
    <p class="login-tag">AUTHORIZED PERSONNEL ONLY</p>

    <div class="field {{ $errors->has('email') ? 'has-error' : '' }}">
      <label for="email">EMAIL <span class="req">*</span></label>
      <input class="input" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
      @error('email')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('password') ? 'has-error' : '' }}">
      <label for="password">PASSWORD <span class="req">*</span></label>
      <input class="input" id="password" name="password" type="password" required autocomplete="current-password">
      @error('password')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="checkbox-row">
      <input type="checkbox" id="remember" name="remember" value="1">
      <label for="remember">Keep me signed in</label>
    </div>

    <button class="btn btn-primary" type="submit" style="width:100%;margin-top:0.6rem">SIGN IN</button>
  </form>
</div>
</body>
</html>
