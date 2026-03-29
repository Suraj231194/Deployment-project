@php
    $segments = request()->segments();
    $paths = [];
    $count = count($segments);
@endphp

<nav aria-label="Breadcrumb" class="mb-5 flex items-center text-sm text-slate-500">
    <ol class="flex list-none flex-wrap items-center gap-y-2 p-0">
        <li class="flex items-center">
            <a href="{{ url('/') }}" class="flex items-center transition-colors hover:text-primary-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </a>
            <svg class="mx-2 h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </li>

        @foreach($segments as $i => $segment)
            @php
                $paths[] = $segment;
                $url = url(implode('/', $paths));
                $label = ucwords(str_replace(['-', '_'], ' ', $segment));
                $isLast = ($i + 1) === $count;
            @endphp

            <li class="flex items-center">
                @if($isLast)
                    <span class="text-slate-900 font-semibold" aria-current="page">{{ $label }}</span>
                @else
                    <a href="{{ $url }}" class="transition-colors hover:text-primary-600">{{ $label }}</a>
                    <svg class="mx-2 h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
