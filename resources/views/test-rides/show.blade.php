@extends('layouts.app')

@section('title', 'Request #'.$ride->id)
@section('crumb', 'Test Rides')

@section('content')
<div class="page-head">
  <div>
    <div class="page-sub">
      <a href="{{ route('admin.test-rides.index') }}" style="color:inherit">{{ __('Test rides') }}</a>
      / {{ __('Request') }} #{{ $ride->id }}
    </div>
    <h1 class="page-title">{{ mb_strtoupper($ride->name) }}
      <span class="badge {{ $ride->status->tone() }}" style="font-size:0.6rem;vertical-align:middle">{{ __($ride->status->label()) }}</span>
    </h1>
  </div>
  <a class="btn btn-ghost" href="{{ route('admin.test-rides.index') }}">← {{ __('Back') }}</a>
</div>

<div class="form-split">
  <div class="form-main">

    {{-- ---------- Customer ---------- --}}
    <div class="card form-card">
      <div class="card-head"><h3>{{ __('Customer') }}</h3></div>
      <div class="section-body">
        <div class="form-grid">
          <div class="field"><label>{{ __('NAME') }}</label><div class="ride-val">{{ $ride->name }}</div></div>
          <div class="field"><label>{{ __('PHONE') }}</label>
            <div class="ride-val"><a href="tel:{{ $ride->phone }}" dir="ltr">{{ $ride->phone }}</a></div>
          </div>
          <div class="field"><label>{{ __('EMAIL') }}</label>
            <div class="ride-val">
              @if($ride->email)<a href="mailto:{{ $ride->email }}" dir="ltr">{{ $ride->email }}</a>
              @else<span class="dim">—</span>@endif
            </div>
          </div>
          <div class="field"><label>{{ __('SUBMITTED') }}</label>
            <div class="ride-val">{{ $ride->created_at->format('d M Y · H:i') }}</div>
          </div>
        </div>
      </div>
    </div>

    {{-- ---------- Booking ---------- --}}
    <div class="card form-card">
      <div class="card-head"><h3>{{ __('Booking') }}</h3></div>
      <div class="section-body">
        <div class="ride-bike">
          @php $img = $ride->motorcycle->images->sortBy('sort_order')->first(); @endphp
          @if($img)
            <img class="ride-bike-img" src="{{ $img->url }}" alt="{{ $ride->motorcycle->name }}">
          @else
            <div class="ride-bike-img ride-bike-empty">{{ __('NO IMG') }}</div>
          @endif
          <div>
            <div class="cell-brand" style="margin-bottom:0.3rem">
              <span class="dot" style="background:{{ $ride->motorcycle->brand->accent }}"></span>
              <span class="dim">{{ $ride->motorcycle->brand->name }}</span>
            </div>
            <div class="cell-title" style="font-size:1.05rem">{{ $ride->motorcycle->name }}</div>
          </div>
        </div>
        <div class="form-grid" style="margin-top:1.1rem">
          <div class="field"><label>{{ __('PREFERRED DATE') }}</label>
            <div class="ride-val">{{ $ride->preferred_date->format('l, d M Y') }}</div>
          </div>
          <div class="field"><label>{{ __('SHOWROOM') }}</label>
            <div class="ride-val">{{ $ride->showroom->name }}</div>
            <div class="dim" style="font-size:0.76rem">{{ $ride->showroom->city }}, {{ $ride->showroom->country }}</div>
          </div>
          <div class="field full"><label>{{ __('SHOWROOM DETAILS') }}</label>
            <div class="ride-val" style="font-weight:400;font-size:0.85rem">
              {{ $ride->showroom->address }}
              @if($ride->showroom->phone) · <a href="tel:{{ $ride->showroom->phone }}" dir="ltr">{{ $ride->showroom->phone }}</a>@endif
              @if($ride->showroom->hours) · <span class="dim">{{ $ride->showroom->hours }}</span>@endif
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ---------- Timeline ---------- --}}
    <div class="card form-card">
      <div class="card-head"><h3>{{ __('Timeline') }}</h3></div>
      <div class="section-body">
        @if($timeline->isEmpty())
          <div class="dim" style="font-size:0.82rem">{{ __('No activity recorded yet.') }}</div>
        @else
          <div class="ride-timeline">
            @foreach($timeline as $entry)
              <div class="tl-row">
                <span class="tl-dot"></span>
                <div class="tl-body">
                  <div class="tl-head">
                    <span class="badge {{ $entry->tone() }}">{{ __($entry->action) }}</span>
                    <span class="dim" style="font-size:0.72rem">{{ $entry->created_at->format('d M Y · H:i') }}</span>
                  </div>
                  <div style="font-size:0.8rem">
                    <strong>{{ $entry->user?->name ?? __('System') }}</strong> — {{ $entry->description }}
                    @if($entry->properties['changes']['status'] ?? null)
                      <span class="dim">
                        ({{ __(\App\Enums\TestRideStatus::from($entry->properties['changes']['status'][0])->label()) }}
                        → {{ __(\App\Enums\TestRideStatus::from($entry->properties['changes']['status'][1])->label()) }})
                      </span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- ---------- Rail ---------- --}}
  <div class="form-rail">
    @can('update', $ride)
    <div class="card form-section">
      <div class="card-head"><h3>{{ __('Update status') }}</h3></div>
      <div class="section-body">
        <form method="POST" action="{{ route('admin.test-rides.update', $ride) }}">
          @csrf @method('PATCH')
          <div class="field">
            <label for="status">{{ __('STATUS') }}</label>
            <select class="select" id="status" name="status">
              @foreach($statuses as $s)
                <option value="{{ $s->value }}" @selected(old('status', $ride->status->value) === $s->value)>{{ __($s->label()) }}</option>
              @endforeach
            </select>
            @error('status')<span class="err">{{ $message }}</span>@enderror
          </div>
          <div class="field" style="margin-top:0.8rem">
            <label for="internal_note">{{ __('INTERNAL NOTE') }}</label>
            <textarea class="input" id="internal_note" name="internal_note" rows="3" maxlength="1000"
              placeholder="{{ __('Only visible to staff') }}">{{ old('internal_note', $ride->internal_note) }}</textarea>
            @error('internal_note')<span class="err">{{ $message }}</span>@enderror
          </div>
          <button class="btn btn-primary" type="submit" style="width:100%;margin-top:0.9rem">{{ __('Save & notify customer') }}</button>
          <div class="hint" style="margin-top:0.6rem;text-align:center">{{ __('Changing the status emails the customer automatically.') }}</div>
        </form>
      </div>
    </div>
    @else
      <div class="card form-section">
        <div class="card-head"><h3>{{ __('Status') }}</h3></div>
        <div class="section-body"><span class="badge {{ $ride->status->tone() }}">{{ __($ride->status->label()) }}</span></div>
      </div>
    @endcan

    <div class="card form-section">
      <div class="card-head"><h3>{{ __('Request') }}</h3></div>
      <div class="section-body" style="display:flex;flex-direction:column;gap:0.55rem;font-size:0.8rem">
        <div style="display:flex;justify-content:space-between"><span class="dim">{{ __('ID') }}</span><strong>#{{ $ride->id }}</strong></div>
        <div style="display:flex;justify-content:space-between"><span class="dim">{{ __('Submitted') }}</span><span>{{ $ride->created_at->format('d M Y') }}</span></div>
        <div style="display:flex;justify-content:space-between"><span class="dim">{{ __('Last update') }}</span><span>{{ $ride->updated_at->format('d M Y · H:i') }}</span></div>
      </div>
    </div>
  </div>
</div>
@endsection
