@if ($paginator->hasPages())
<nav class="pg" role="navigation" aria-label="Pagination">
    @if ($paginator->onFirstPage())
        <span class="pg-btn disabled" aria-disabled="true">‹</span>
    @else
        <a class="pg-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous">‹</a>
    @endif

    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="pg-btn disabled">{{ $element }}</span>
        @endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span class="pg-btn current" aria-current="page">{{ $page }}</span>
                @else
                    <a class="pg-btn" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
        @endif
    @endforeach

    @if ($paginator->hasMorePages())
        <a class="pg-btn" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next">›</a>
    @else
        <span class="pg-btn disabled" aria-disabled="true">›</span>
    @endif
</nav>
@endif
