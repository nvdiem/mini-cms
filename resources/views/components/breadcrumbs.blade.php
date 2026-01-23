@props(['items' => []])

<nav aria-label="Breadcrumb" class="overflow-x-auto whitespace-nowrap mb-6 text-sm">
  <ol class="flex items-center gap-2 text-slate-500">
    @foreach($items as $item)
      <li class="flex items-center gap-2">
        @if(!$loop->first)
          <span class="text-slate-300">/</span>
        @endif
        
        @if(!empty($item['url']))
          <a href="{{ $item['url'] }}" class="hover:text-primary hover:underline transition">
            {{ $item['label'] }}
          </a>
        @else
          <span class="font-medium text-slate-900 truncate max-w-[200px] sm:max-w-xs">
            {{ $item['label'] }}
          </span>
        @endif
      </li>
    @endforeach
  </ol>
</nav>
