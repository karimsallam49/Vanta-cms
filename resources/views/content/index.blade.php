@extends('layouts.app')

@section('title', 'Site content')
@section('crumb', 'Site content')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">{{ __('Public website') }}</div>
    <h1 class="page-title">{{ __('SITE') }} <span class="accent">{{ __('CONTENT') }}</span></h1>
  </div>
</div>

<p class="dim" style="margin:-0.4rem 0 1.4rem;max-width:640px">
  {{ __('Text and imagery for sections that are not catalogue-driven — hero, intros, headings, worlds and service copy. Catalogue items (brands, models, offers, showrooms) are managed from their own sections.') }}
</p>

@foreach($groups as $group => $sections)
  <div class="sub-head" style="border-top-width:{{ $loop->first ? '0' : '1px' }};margin-top:{{ $loop->first ? '0' : '1.4rem' }}">{{ __($group) }}</div>
  <div class="content-grid">
    @foreach($sections as $s)
      <a class="content-card" href="{{ route('admin.content.edit', $s['key']) }}">
        <div class="cc-top">
          <span class="cell-title">{{ $s['label'] }}</span>
          <span class="badge {{ $s['filled'] ? 'lime' : '' }}">{{ $s['filled'] }}/{{ count($s['fields']) }}</span>
        </div>
        @if($s['thumb'])
          <img class="cc-thumb" src="{{ asset('storage/'.$s['thumb']) }}" alt="">
        @else
          <div class="cc-thumb cc-empty">{{ __('No image') }}</div>
        @endif
        <div class="cc-foot">
          <span class="dim">{{ $s['updated_at'] ? __('Updated').' '.$s['updated_at']->diffForHumans() : __('Defaults — not edited yet') }}</span>
          <span class="btn btn-ghost btn-sm">{{ __('Edit') }}</span>
        </div>
      </a>
    @endforeach
  </div>
@endforeach
@endsection
