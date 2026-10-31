@extends('layouts.app')

@section('title', $showroom->exists ? 'Edit Showroom' : 'New Showroom')
@section('crumb', $showroom->exists ? 'Edit Showroom' : 'New Showroom')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Facilities / {{ $showroom->exists ? 'Edit' : 'Create' }}</div>
    <h1 class="page-title">{{ $showroom->exists ? 'EDIT' : 'NEW' }} <span class="accent">LOCATION</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.showrooms.index') }}">← Back</a>
</div>

<form class="card form-card" method="POST" action="{{ $showroom->exists ? route('admin.showrooms.update', $showroom) : route('admin.showrooms.store') }}">
  @csrf
  @if($showroom->exists) @method('PUT') @endif

  <div class="form-grid">
    <div class="field {{ $errors->has('city') ? 'has-error' : '' }}">
      <label for="city">CITY <span class="req">*</span></label>
      <input class="input" id="city" name="city" value="{{ old('city', $showroom->city) }}" required maxlength="80">
      @error('city')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('country') ? 'has-error' : '' }}">
      <label for="country">COUNTRY <span class="req">*</span></label>
      <input class="input" id="country" name="country" value="{{ old('country', $showroom->country) }}" required maxlength="80">
      @error('country')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('name') ? 'has-error' : '' }}">
      <label for="name">LOCATION NAME <span class="req">*</span></label>
      <input class="input" id="name" name="name" value="{{ old('name', $showroom->name) }}" required maxlength="140" placeholder="VANTA MOTO — SHEIKH ZAYED FLAGSHIP">
      @error('name')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('address') ? 'has-error' : '' }}">
      <label for="address">ADDRESS <span class="req">*</span></label>
      <input class="input" id="address" name="address" value="{{ old('address', $showroom->address) }}" required maxlength="255">
      @error('address')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('phone') ? 'has-error' : '' }}">
      <label for="phone">PHONE</label>
      <input class="input" id="phone" name="phone" value="{{ old('phone', $showroom->phone) }}" maxlength="40" placeholder="+971 4 555 0100">
      @error('phone')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field {{ $errors->has('hours') ? 'has-error' : '' }}">
      <label for="hours">OPENING HOURS</label>
      <input class="input" id="hours" name="hours" value="{{ old('hours', $showroom->hours) }}" maxlength="120" placeholder="SAT–THU 9:00–21:00 · FRI 14:00–21:00">
      @error('hours')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field full {{ $errors->has('open_days') ? 'has-error' : '' }}">
      <label>{{ __('TEST-RIDE DAYS') }}</label>
      @php
        $days = [1 => 'MON', 2 => 'TUE', 3 => 'WED', 4 => 'THU', 5 => 'FRI', 6 => 'SAT', 0 => 'SUN'];
        $picked = old('open_days', $showroom->open_days ?? [0, 1, 2, 3, 4, 6]);
      @endphp
      <div class="check-grid" style="margin-top:0.4rem">
        @foreach($days as $num => $label)
          <div class="checkbox-row">
            <input type="checkbox" id="od-{{ $num }}" name="open_days[]" value="{{ $num }}" @checked(in_array($num, $picked))>
            <label for="od-{{ $num }}">{{ __($label) }}</label>
          </div>
        @endforeach
      </div>
      <span class="hint">{{ __('Days this showroom accepts test rides — the website blocks closed days.') }}</span>
      @error('open_days')<span class="err">{{ $message }}</span>@enderror
    </div>

    <div class="field">
      <label>FACILITY TYPE</label>
      <div class="checkbox-row">
        <input type="checkbox" id="is_service_center" name="is_service_center" value="1" @checked(old('is_service_center', $showroom->is_service_center))>
        <label for="is_service_center">Includes service center</label>
      </div>
    </div>

    <div class="field full">
      <label>MAP LOCATION</label>
      <span class="hint">Type to search, click the map or drag the pin — the address fills itself.</span>
      <div class="map-bar">
        <input class="input" id="map-search" type="text" placeholder="Search city or address… (e.g. Sheikh Zayed Rd Dubai)" autocomplete="off">
      </div>
      <div id="map-pick"></div>
      <div class="form-grid" style="margin-top:0.8rem">
        <div class="field {{ $errors->has('latitude') ? 'has-error' : '' }}">
          <label for="latitude">LATITUDE</label>
          <input class="input" id="latitude" name="latitude" dir="ltr" value="{{ old('latitude', $showroom->latitude) }}" placeholder="25.1184">
          @error('latitude')<span class="err">{{ $message }}</span>@enderror
        </div>
        <div class="field {{ $errors->has('longitude') ? 'has-error' : '' }}">
          <label for="longitude">LONGITUDE</label>
          <input class="input" id="longitude" name="longitude" dir="ltr" value="{{ old('longitude', $showroom->longitude) }}" placeholder="55.2275">
          @error('longitude')<span class="err">{{ $message }}</span>@enderror
        </div>
      </div>
    </div>
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="{{ route('admin.showrooms.index') }}">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ $showroom->exists ? 'Save changes' : 'Create location' }}</button>
  </div>
</form>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
  const latIn = document.getElementById('latitude');
  const lngIn = document.getElementById('longitude');
  const addrIn = document.getElementById('address');
  const start = (latIn.value && lngIn.value)
    ? [parseFloat(latIn.value), parseFloat(lngIn.value)]
    : [25.2, 55.3]; // Gulf default

  // Fix default marker icon when assets come from CDN
  delete L.Icon.Default.prototype._getIconUrl;
  L.Icon.Default.mergeOptions({
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
  });

  const map = L.map('map-pick').setView(start, latIn.value ? 14 : 8);

  // CartoDB dark tiles — free, no API key. Matches the CMS theme.
  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap &copy; CARTO',
    subdomains: 'abcd', maxZoom: 19,
  }).addTo(map);

  let marker = null;

  function ensureMarker(ll) {
    if (!marker) {
      marker = L.marker(ll, { draggable: true }).addTo(map);
      marker.on('dragend', () => {
        const p = marker.getLatLng();
        updateLocation(p.lat, p.lng);
      });
    }
    marker.setLatLng(ll);
  }

  // Reverse geocode — fill the address field from the dropped pin
  async function reverseGeocode(lat, lng) {
    try {
      const r = await fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng);
      const d = await r.json();
      if (d.display_name) addrIn.value = d.display_name;
    } catch (e) { /* keep the typed address */ }
  }

  function updateLocation(lat, lng) {
    latIn.value = lat.toFixed(7);
    lngIn.value = lng.toFixed(7);
    ensureMarker([lat, lng]);
    map.panTo([lat, lng]);
    reverseGeocode(lat, lng);
  }

  if (latIn.value && lngIn.value) ensureMarker(start);

  map.on('click', e => updateLocation(e.latlng.lat, e.latlng.lng));

  // Manual coordinate entry still works
  [latIn, lngIn].forEach(el => el.addEventListener('change', () => {
    const la = parseFloat(latIn.value), lo = parseFloat(lngIn.value);
    if (!isNaN(la) && !isNaN(lo)) {
      updateLocation(la, lo);
      map.setView([la, lo], Math.max(map.getZoom(), 13));
    }
  }));

  // Live search with debounce — free OSM geocoder (Nominatim), no key needed.
  const q = document.getElementById('map-search');
  let timer;
  q.addEventListener('input', () => {
    clearTimeout(timer);
    const query = q.value.trim();
    if (query.length < 3) return;
    timer = setTimeout(async () => {
      try {
        const r = await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query));
        const d = await r.json();
        if (!d.length) { q.classList.add('has-error'); return; }
        q.classList.remove('has-error');
        const la = parseFloat(d[0].lat), lo = parseFloat(d[0].lon);
        updateLocation(la, lo);
        map.setView([la, lo], 15);
      } catch (e) { /* network — ignore */ }
    }, 500);
  });
  q.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });
})();
</script>
@endsection
