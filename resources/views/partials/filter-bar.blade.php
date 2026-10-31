{{--
  Reusable filter bar — GET params drive the query, applied live.
  Usage:
    @include('partials.filter-bar', [
      'search'  => 'Placeholder for the search box (omit to hide)',
      'selects' => [ ['name' => 'brand_id', 'label' => 'All brands', 'options' => [id => label, …]], … ],
    ])
--}}
<form class="filter-bar" method="GET">
  <span class="fb-tag">{{ __('Filter') }}</span>

  @if(isset($search))
    <input class="input" name="q" value="{{ request('q') }}" placeholder="{{ __($search) }}" autocomplete="off">
  @endif

  @foreach($selects ?? [] as $s)
    <select class="select" name="{{ $s['name'] }}">
      <option value="">{{ __($s['label']) }}</option>
      @foreach($s['options'] as $value => $label)
        <option value="{{ $value }}" @selected((string) request($s['name']) === (string) $value)>{{ __($label) }}</option>
      @endforeach
    </select>
  @endforeach

  @foreach($dates ?? [] as $d)
    <input class="input" type="date" name="{{ $d['name'] }}" value="{{ request($d['name']) }}"
      title="{{ __($d['label']) }}" aria-label="{{ __($d['label']) }}" style="width:auto">
  @endforeach

  <div class="fb-actions">
    @if(collect(request()->except('page'))->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty())
      <a class="fb-reset" href="{{ url()->current() }}">{{ __('Reset') }}</a>
    @endif
  </div>
</form>

@once
<script>
  document.querySelectorAll('.filter-bar').forEach(form => {
    const q = form.querySelector('input[name="q"]');
    let timer;

    // Live search — short pause after typing, then apply.
    q?.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(() => form.submit(), 400);
    });

    // Selects and date fields apply instantly.
    form.querySelectorAll('select, input[type="date"]').forEach(sel =>
      sel.addEventListener('change', () => form.submit()));

    // After a filtered reload, keep typing seamless —
    // refocus the search box with the caret at the end.
    if (q && q.value) {
      q.focus();
      q.setSelectionRange(q.value.length, q.value.length);
    }
  });
</script>
@endonce
