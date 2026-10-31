@extends('layouts.app')

@section('title', 'Edit — '.$def['label'])
@section('crumb', 'Site content / '.$def['label'])

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">Site content / {{ __($def['group']) }}</div>
    <h1 class="page-title">{{ __('EDIT') }} <span class="accent">{{ mb_strtoupper($def['label']) }}</span></h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.content.index') }}">← Back</a>
</div>

<form class="card form-card form-shell" method="POST" enctype="multipart/form-data" action="{{ route('admin.content.update', $sectionKey) }}">
  @csrf @method('PUT')

  <div class="form-grid" style="padding:1.4rem">
    @foreach($def['fields'] as $name => [$type, $label])
      @php $value = old($name, $payload[$name] ?? null); @endphp

      @if($type === 'image')
        <div class="field full {{ $errors->has($name) ? 'has-error' : '' }}">
          <label>{{ __($label) }}</label>
          <div class="img-row">
            @if($payload[$name] ?? null)
              <img class="img-preview" src="{{ asset('storage/'.$payload[$name]) }}" alt="">
              <label class="checkbox-row" style="align-items:center">
                <input type="checkbox" name="{{ $name }}_remove" value="1">
                <span>{{ __('Remove current image') }}</span>
              </label>
            @else
              <div class="img-preview img-none">{{ __('No image set — site fallback is used') }}</div>
            @endif
            <input class="input" type="file" name="{{ $name }}" accept="image/*">
          </div>
          @error($name)<span class="err">{{ $message }}</span>@enderror
        </div>
      @elseif($type === 'color')
        <div class="field {{ $errors->has($name) ? 'has-error' : '' }}">
          <label for="{{ $name }}">{{ __($label) }}</label>
          <input type="color" id="{{ $name }}" name="{{ $name }}" value="{{ $value ?? '#888888' }}" class="input color-input">
          @error($name)<span class="err">{{ $message }}</span>@enderror
        </div>
      @elseif($type === 'textarea' || $type === 'lines')
        <div class="field {{ $errors->has($name) ? 'has-error' : '' }}">
          <label for="{{ $name }}">{{ __($label) }} — EN</label>
          <textarea class="textarea" id="{{ $name }}" name="{{ $name }}" rows="{{ $type === 'lines' ? 3 : 4 }}">{{ $value }}</textarea>
          @error($name)<span class="err">{{ $message }}</span>@enderror
        </div>
        <div class="field {{ $errors->has($name.'_ar') ? 'has-error' : '' }}" dir="rtl">
          <label for="{{ $name }}_ar">{{ __($label) }} — AR</label>
          <textarea class="textarea" id="{{ $name }}_ar" name="{{ $name }}_ar" rows="{{ $type === 'lines' ? 3 : 4 }}">{{ old($name.'_ar', $payload[$name.'_ar'] ?? null) }}</textarea>
          @error($name.'_ar')<span class="err">{{ $message }}</span>@enderror
        </div>
      @else
        <div class="field {{ $errors->has($name) ? 'has-error' : '' }}">
          <label for="{{ $name }}">{{ __($label) }} — EN</label>
          <input class="input" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}" maxlength="255">
          @error($name)<span class="err">{{ $message }}</span>@enderror
        </div>
        <div class="field {{ $errors->has($name.'_ar') ? 'has-error' : '' }}" dir="rtl">
          <label for="{{ $name }}_ar">{{ __($label) }} — AR</label>
          <input class="input" id="{{ $name }}_ar" name="{{ $name }}_ar" value="{{ old($name.'_ar', $payload[$name.'_ar'] ?? null) }}" maxlength="255">
          @error($name.'_ar')<span class="err">{{ $message }}</span>@enderror
        </div>
      @endif
    @endforeach
  </div>

  <div class="form-actions">
    <a class="btn btn-ghost" href="{{ route('admin.content.index') }}">Cancel</a>
    <button class="btn btn-primary" type="submit">{{ __('Save section') }}</button>
  </div>
</form>
@endsection
