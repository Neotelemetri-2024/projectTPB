@if ($paginator->total() > 0)
    @php
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();
        $pages = collect([1, $last]);
        for ($page = max(1, $current - 1); $page <= min($last, $current + 1); $page++) {
            $pages->push($page);
        }
        $pages = $pages->unique()->sort()->values();
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center justify-between gap-3 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-3 py-2 text-sm text-gray-400 bg-white border border-gray-300 rounded-md">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Sebelumnya</a>
            @endif

            <span class="text-sm text-gray-600">{{ $current }} / {{ $last }}</span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">Berikutnya</a>
            @else
                <span class="inline-flex items-center px-3 py-2 text-sm text-gray-400 bg-white border border-gray-300 rounded-md">Berikutnya</span>
            @endif
        </div>

        <div class="hidden sm:flex sm:items-center sm:gap-4">
            <p class="text-sm text-gray-600 whitespace-nowrap">
                Menampilkan <span class="font-medium">{{ $paginator->firstItem() }}</span>–<span class="font-medium">{{ $paginator->lastItem() }}</span>
                dari <span class="font-medium">{{ $paginator->total() }}</span>
            </p>

            <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2">
                @foreach(request()->except(['per_page', 'page']) as $key => $value)
                    @if(is_array($value))
                        @foreach($value as $item)
                            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endif
                @endforeach
                <label for="per-page-{{ $paginator->getPageName() }}" class="text-sm text-gray-600 whitespace-nowrap">Per halaman</label>
                <select id="per-page-{{ $paginator->getPageName() }}" name="per_page" onchange="this.form.submit()"
                    class="border border-gray-300 bg-white text-gray-700 text-sm rounded-md py-1.5 pl-2 pr-7 focus:ring-amber-500 focus:border-amber-500">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" {{ (int) $paginator->perPage() === $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <div class="hidden sm:flex sm:items-center">
            <span class="inline-flex rounded-md shadow-sm">
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center px-2 py-2 text-gray-400 bg-white border border-gray-300 rounded-l-md" aria-disabled="true">&lsaquo;</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2 py-2 text-gray-600 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50" aria-label="Sebelumnya">&lsaquo;</a>
                @endif

                @foreach($pages as $index => $page)
                    @if($index > 0 && $page > $pages[$index - 1] + 1)
                        <span class="inline-flex items-center px-3 py-2 -ml-px text-sm text-gray-500 bg-white border border-gray-300">…</span>
                    @endif

                    @if($page === $current)
                        <span class="inline-flex items-center px-3 py-2 -ml-px text-sm font-medium text-white bg-amber-600 border border-amber-600" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $paginator->url($page) }}" class="inline-flex items-center px-3 py-2 -ml-px text-sm text-gray-700 bg-white border border-gray-300 hover:bg-gray-50">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2 py-2 -ml-px text-gray-600 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50" aria-label="Berikutnya">&rsaquo;</a>
                @else
                    <span class="inline-flex items-center px-2 py-2 -ml-px text-gray-400 bg-white border border-gray-300 rounded-r-md" aria-disabled="true">&rsaquo;</span>
                @endif
            </span>
        </div>
    </nav>
@endif
