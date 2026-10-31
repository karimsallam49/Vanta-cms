<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
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
  $pendingRides = \App\Models\TestRideRequest::visibleTo(auth()->user())
      ->where('status', \App\Enums\TestRideStatus::Pending)
      ->count();
  $nav = [
    'Sales & Marketing' => [
      ['route' => 'admin.offers.index', 'match' => 'admin.offers.*', 'label' => 'Offers', 'icon' => '<path d="M4 12V6a2 2 0 012-2h6l8 8-8 8-8-8z"/><circle cx="9" cy="9" r="1.4"/>', 'perm' => 'offers.view'],
      ['route' => 'admin.test-rides.index', 'match' => 'admin.test-rides.*', 'label' => 'Test Rides', 'icon' => '<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/>', 'count' => $pendingRides, 'perm' => 'test-rides.view',
        'children' => array_merge(
          [['label' => 'All requests', 'href' => route('admin.test-rides.index'),
            'on' => request()->routeIs('admin.test-rides.index') && ! request('status'), 'count' => null]],
          collect(\App\Enums\TestRideStatus::cases())->map(fn ($s) => [
            'label' => $s->label(),
            'href' => route('admin.test-rides.index', ['status' => $s->value]),
            'on' => request()->routeIs('admin.test-rides.index') && request('status') === $s->value,
            'count' => $s === \App\Enums\TestRideStatus::Pending ? $pendingRides : null,
          ])->all()
        )],
    ],
    'Locations' => [
      ['route' => 'admin.showrooms.index', 'match' => 'admin.showrooms.*', 'label' => 'Showrooms', 'icon' => '<path d="M4 20V8l8-4 8 4v12"/><path d="M9 20v-6h6v6"/>', 'perm' => 'showrooms.view'],
    ],
    'Website' => [
      ['route' => 'admin.content.index', 'match' => 'admin.content.*', 'label' => 'Site content', 'icon' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M8 4v5"/>', 'perm' => 'site-content.view'],
    ],
    'Insights' => [
      ['route' => 'admin.reports.index', 'match' => 'admin.reports.*', 'label' => 'Reports', 'icon' => '<path d="M4 20V10M10 20V4M16 20v-7M2 20h20"/>', 'perm' => 'reports.view'],
      ['route' => 'admin.activity.index', 'match' => 'admin.activity.*', 'label' => 'Activity log', 'icon' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/>', 'perm' => 'activity.view'],
    ],
    'Administration' => [
      ['route' => 'admin.currencies.index', 'match' => 'admin.currencies.*', 'label' => 'Currencies', 'icon' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10M15.2 9.3c-.6-1-1.9-1.6-3.2-1.6-1.9 0-3 .9-3 2.2 0 2.9 6.2 1.4 6.2 4.4 0 1.3-1.4 2.4-3.4 2.4-1.5 0-2.7-.7-3.2-1.7"/>', 'perm' => 'currencies.view'],
      ['route' => 'admin.users.index', 'match' => 'admin.users.*', 'label' => 'Staff', 'icon' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6.5 8-6.5s8 2.5 8 6.5"/>', 'perm' => 'staff.view'],
      ['route' => 'admin.roles.index', 'match' => 'admin.roles.*', 'label' => 'Roles', 'icon' => '<rect x="4" y="11" width="16" height="9" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/><circle cx="12" cy="15.5" r="1.4"/>', 'perm' => 'roles.view'],
    ],
  ];
  $initials = collect(explode(' ', trim(auth()->user()->name)))
      ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
      ->take(2)->implode('');
@endphp
<div class="shell">
  <div class="scrim" id="scrim"></div>

  <aside class="sidebar" id="sidebar">
    <a class="brand-mark" href="{{ route('admin.dashboard') }}">
      <span class="bm-icon"><svg width="18" height="18" viewBox="0 0 64 64" aria-hidden="true"><path d="M13 13 L32 51 L51 13 L42 13 L32 33.5 L22 13 Z" fill="#fff"/></svg></span>
      <span class="bm-text">VANTA<span class="accent">MOTO</span><span class="sub">{{ __('CONTROL ROOM') }}</span></span>
    </a>

    <nav class="side-nav" aria-label="CMS">
      <div class="side-label" style="padding-top:0">{{ __('Overview') }}</div>
      <a class="side-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="5" rx="1"/><rect x="13" y="10" width="8" height="11" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/></svg>
        {{ __('Dashboard') }}
      </a>

      <div class="side-label">{{ __('Catalogue') }}</div>

      @if(auth()->user()->hasPermission('brands.view'))
      <details class="nav-group" @if(request()->routeIs('admin.brands.*')) open @endif>
        <summary>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l3 6 6 .7-4.5 4.3 1.1 6-5.6-3-5.6 3 1.1-6L3 9.7 9 9z"/></svg>
          {{ __('Brands') }}
          <span class="nav-caret"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg></span>
        </summary>
        <div class="nav-sub">
          <a class="sub-link {{ request()->routeIs('admin.brands.index') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">{{ __('All brands') }}</a>
          @if(auth()->user()->hasPermission('brands.create'))
            <a class="sub-link {{ request()->routeIs('admin.brands.create') ? 'active' : '' }}" href="{{ route('admin.brands.create') }}">{{ __('Create') }}</a>
          @endif
          @if(auth()->user()->hasPermission('brands.import'))
            <a class="sub-link {{ request()->routeIs('admin.brands.import.*') ? 'active' : '' }}" href="{{ route('admin.brands.import.create') }}">{{ __('Bulk import') }}</a>
          @endif
          @if(auth()->user()->hasPermission('brands.export'))
            <a class="sub-link" href="{{ route('admin.brands.export') }}">{{ __('Export') }}</a>
          @endif
        </div>
      </details>
      @endif

      @if(auth()->user()->hasPermission('motorcycles.view'))
      <details class="nav-group" @if(request()->routeIs('admin.motorcycles.*')) open @endif>
        <summary>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="7" cy="15" r="3.5"/><circle cx="19" cy="15" r="3.5"/><path d="M7 15h5l3-6h4"/></svg>
          {{ __('Motorcycles') }}
          <span class="nav-caret"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg></span>
        </summary>
        <div class="nav-sub">
          <a class="sub-link {{ request()->routeIs('admin.motorcycles.index') ? 'active' : '' }}" href="{{ route('admin.motorcycles.index') }}">{{ __('All models') }}</a>
          @if(auth()->user()->hasPermission('motorcycles.create'))
            <a class="sub-link {{ request()->routeIs('admin.motorcycles.create') ? 'active' : '' }}" href="{{ route('admin.motorcycles.create') }}">{{ __('Create') }}</a>
          @endif
          @if(auth()->user()->hasPermission('motorcycles.import'))
            <a class="sub-link {{ request()->routeIs('admin.motorcycles.import.*') ? 'active' : '' }}" href="{{ route('admin.motorcycles.import.create') }}">{{ __('Bulk import') }}</a>
          @endif
          @if(auth()->user()->hasPermission('motorcycles.export'))
            <a class="sub-link" href="{{ route('admin.motorcycles.export') }}">{{ __('Export') }}</a>
          @endif
        </div>
      </details>
      @endif

      @foreach($nav as $group => $links)
        @php $links = array_filter($links, fn ($l) => empty($l['perm']) || auth()->user()->hasPermission($l['perm'])); @endphp
        @if(count($links))
          <div class="side-label">{{ __($group) }}</div>
          @foreach($links as $link)
            @if(!empty($link['children']))
              <details class="nav-group" @if(request()->routeIs($link['match'])) open @endif>
                <summary>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $link['icon'] !!}</svg>
                  {{ __($link['label']) }}
                  @if(isset($link['count']))
                    <span class="nav-badge rides-pending-badge" @if(!$link['count']) style="display:none" @endif>{{ $link['count'] }}</span>
                  @endif
                  <span class="nav-caret"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg></span>
                </summary>
                <div class="nav-sub">
                  @foreach($link['children'] as $child)
                    <a class="sub-link {{ $child['on'] ? 'active' : '' }}" href="{{ $child['href'] }}">
                      {{ __($child['label']) }}
                      @if($child['count'] !== null)
                        <span class="nav-badge rides-pending-badge" style="margin-inline-start:auto;{{ $child['count'] ? '' : 'display:none' }}">{{ $child['count'] }}</span>
                      @endif
                    </a>
                  @endforeach
                </div>
              </details>
            @else
              <a class="side-link {{ request()->routeIs($link['match']) ? 'active' : '' }}" href="{{ route($link['route']) }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">{!! $link['icon'] !!}</svg>
                {{ __($link['label']) }}
                @if(!empty($link['count']))
                  <span class="nav-badge">{{ $link['count'] }}</span>
                @endif
              </a>
            @endif
          @endforeach
        @endif
      @endforeach
    </nav>
  </aside>

  <div class="main">
    <div class="topbar">
      <div style="display:flex;align-items:center;gap:0.8rem">
        <button class="burger" id="burger" aria-label="Menu"><span></span><span></span><span></span></button>
        <div class="crumb">VANTA MOTO <span style="opacity:.4">/</span> <strong>@yield('crumb', __('Dashboard'))</strong></div>
      </div>

      <div class="tb-actions">
        <details class="tb-menu" id="lang-menu">
          <summary>
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.7 2.6 4 5.7 4 9s-1.3 6.4-4 9c-2.7-2.6-4-5.7-4-9s1.3-6.4 4-9z"/></svg>
            <span>{{ app()->getLocale() === 'ar' ? 'العربية' : 'EN' }}</span>
            <svg class="tb-caret" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
          </summary>
          <div class="tb-drop">
            <a class="tb-item {{ app()->getLocale() === 'en' ? 'on' : '' }}" href="{{ route('admin.locale', 'en') }}">English</a>
            <a class="tb-item {{ app()->getLocale() === 'ar' ? 'on' : '' }}" href="{{ route('admin.locale', 'ar') }}">العربية</a>
          </div>
        </details>

        <details class="tb-menu tb-user" id="user-menu">
          <summary>
            <span class="tb-avatar">{{ $initials }}</span>
            <span class="tb-who">
              <span class="tb-name">{{ auth()->user()->name }}</span>
              <span class="tb-role">{{ auth()->user()->role?->name ?? __('No role') }}</span>
            </span>
            <svg class="tb-caret" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M6 9l6 6 6-6"/></svg>
          </summary>
          <div class="tb-drop">
            <a class="tb-item {{ request()->routeIs('admin.profile.*') ? 'on' : '' }}" href="{{ route('admin.profile.edit') }}">{{ __('Profile') }}</a>
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <button class="tb-item tb-item-danger" type="submit">{{ __('Sign out') }}</button>
            </form>
          </div>
        </details>
      </div>
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

  // Topbar dropdowns — one open at a time, close on outside click.
  document.addEventListener('click', e => {
    document.querySelectorAll('.tb-menu[open]').forEach(m => {
      if (!m.contains(e.target)) m.removeAttribute('open');
    });
  });

  @if(auth()->user()->hasPermission('test-rides.view'))
  // Live pending-requests badge — poll so new bookings show without a refresh.
  (function () {
    const badges = document.querySelectorAll('.rides-pending-badge');
    if (!badges.length) return;
    const poll = () => fetch(@json(route('admin.test-rides.pending-count')), { headers: { Accept: 'application/json' } })
      .then(r => r.ok ? r.json() : null)
      .then(d => {
        if (!d) return;
        badges.forEach(b => {
          b.textContent = d.count;
          b.style.display = d.count > 0 ? '' : 'none';
        });
      })
      .catch(() => {});
    setInterval(poll, 30000);
  })();
  @endif
</script>
</body>
</html>
