@props([
    'title' => 'No documents found',
    'subtitle' => 'It seems we could not find any records at the moment.',
    'icon' => null,
])

<div {{ $attributes->class(['flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-slate-50/50 px-6 py-12 text-center transition-all duration-300 hover:bg-slate-50']) }}>
    <div class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-200">
        @if ($icon)
            {{ $icon }}
        @else
            <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        @endif
    </div>
    <h3 class="text-xl font-bold text-slate-900">{{ $title }}</h3>
    <p class="mt-2 max-w-sm text-sm leading-6 text-slate-500">{{ $subtitle }}</p>
    
    @if ($slot->isNotEmpty())
        <div class="mt-8">
            {{ $slot }}
        </div>
    @endif
</div>
