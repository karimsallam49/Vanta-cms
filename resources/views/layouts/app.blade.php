<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard') — VANTA MOTO CMS</title>
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Cpath d='M13 13 L32 51 L51 13 L42 13 L32 33.5 L22 13 Z' fill='%23e10600'/%3E%3C/svg%3E">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Archivo:wdth,wght@62..125,100..900&family=JetBrains+Mono:wght@400;500;600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/cms.css') }}">
</head>
<body>
@php
  $nav = [
    'CONTENT' => [
      ['route' => 'admin.motorcycles.index', 'match' => 'admin.motorcycles.*', 'label' => 'Motorcycles', 'icon' => '<circle cx="7" cy="15" r="3.5"/><circle cx="19" cy="15" r="3.5"/><path d="M7 15h5l3-6h4"/>'],
      ['route' => 'admin.offers.index', 'match' => 'admin.offers.*', 'label' => 'Offers', 'icon' => '<path d="M4 12V6a2 2 0 012-2h6l8 8-8 8-8-8z"/><circle cx="9" cy="9" r="1.4"/>'],
      ['route' => 'admin.test-rides.index', 'match' => 'admin.test-rides.*', 'label' => 'Test Rides', 'icon' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/>'],
    ],
    'GROUP' => [
      ['route' => 'admin.showrooms.index', 'match' => 'admin.showrooms.*', 'label' => 'Showrooms', 'icon' => '<path d="M4 20V8l8-4 8 4v12"/><path d="M9 20v-6h6v6"/>', 'admin' => true],
      ['route' => 'admin.brands.index', 'match' => 'admin.brands.*', 'label' => 'Brands', 'icon' => '<path d="M12 3l3 6 6 .7-4.5 4.3 1.1 6-5.6-3-5.6 3 1.1-6L3 9.7 9 9z"/>', 'admin' => true],
      ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => 'Staff', 'icon' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>', 'admin' => true],
    ],
  ];
@endphp
<div class="shell">
  <div class="scrim" id="scrim"></div>

  <aside class="sidebar" id="sidebar">
    <a class="brand-mark" href="{{ route('admin.dashboard') }}">
      <svg width="22" height="22" viewBox="0 0 64 64" aria-hidden="true"><path d="M13 13 L32 51 L51 13 L42 13 L32 33.5 L22 13 Z" fill="#e10600"/></svg>
      <span>VANTA<span class="accent">MOTO</span><span class="sub">CONTROL ROOM</span></span>
    </a>

    <nav class="side-nav" aria-label="CMS">
      <div class="side-label" style="padding-top:0">Overview</div>
      <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="5" rx="1"/><rect x="13" y="10" width="8" height="11" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/></svg>
        Dashboard
      </a>

      @foreach($nav as $group => $links)
        @php $links = array_filter($links, fn ($l) => empty($l['admin']) || auth()->user()->isAdmin()); @endphp
        @if(count($links))
          <div class="side-label">{{ $group }}</div>
          @foreach($links as $link)
            <a class="side-link {{ request()->routeIs($link['match']) ? 'active' : '' }}" href="{{ route($link['route']) }}">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $link['icon'] !!}</svg>
              {{ $link['label'] }}
            </a>
          @endforeach
        @endif
      @endforeach
    </nav>

    <div class="side-user">
      <div>
        <div class="who">{{ auth()->user()->name }}</div>
        <span class="badge {{ auth()->user()->isAdmin() ? 'red' : 'lime' }}" style="margin-top:0.3rem">
          {{ auth()->user()->role->label() }}
        </span>
      </div>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="btn btn-ghost btn-sm" type="submit" style="width:100%">Sign out</button>
      </form>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div style="display:flex;align-items:center;gap:0.8rem">
        <button class="burger" id="burger" aria-label="Menu"><span></span><span></span><span></span></button>
        <div class="crumb">VANTA MOTO <span style="opacity:.4">/</span> <strong>@yield('crumb', 'Dashboard')</strong></div>
      </div>
      <div class="crumb" style="opacity:.6">FEEL THE RIDE</div>
    </div>

    <main class="content">
      @include('partials.flash')
      @yield('content')
    </main>
  </div>
</div>

<script>
  const sidebar = document.getElementById('sidebar');
  const scrim = document.getElementById('scrim');
  document.getElementById('burger')?.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    scrim.classList.toggle('on');
  });
  scrim.addEventListener('click', () => {
    sidebar.classList.remove('open');
    scrim.classList.remove('on');
  });
</script>
</body>
</html>
