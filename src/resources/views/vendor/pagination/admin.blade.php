@if ($paginator->hasPages())
  <nav class="admin-pagination" role="navigation" aria-label="Pagination Navigation">

    {{-- Prev --}}
    @if ($paginator->onFirstPage())
      <span class="ap-btn is-disabled" aria-disabled="true">‹</span>
    @else
      <a class="ap-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
    @endif

    {{-- Pages --}}
    @foreach ($elements as $element)
      @if (is_string($element))
        <span class="ap-ellipsis">{{ $element }}</span>
      @endif

      @if (is_array($element))
        @foreach ($element as $page => $url)
          @if ($page == $paginator->currentPage())
            <span class="ap-btn is-active" aria-current="page">{{ $page }}</span>
          @else
            <a class="ap-btn" href="{{ $url }}">{{ $page }}</a>
          @endif
        @endforeach
      @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
      <a class="ap-btn" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
    @else
      <span class="ap-btn is-disabled" aria-disabled="true">›</span>
    @endif

  </nav>
@endif
