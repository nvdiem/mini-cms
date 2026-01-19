@if ($paginator->hasPages())
<nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-2">
    @if ($paginator->onFirstPage())
        <span class="px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 text-xs text-slate-400 cursor-not-allowed">Previous</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Previous</a>
    @endif

    <div class="flex items-center gap-1">
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 text-xs text-slate-500">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-1.5 rounded-md bg-primary/10 text-primary text-xs font-semibold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>

    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">Next</a>
    @else
        <span class="px-3 py-1.5 rounded-md border border-slate-200 dark:border-slate-700 text-xs text-slate-400 cursor-not-allowed">Next</span>
    @endif
</nav>
@endif
