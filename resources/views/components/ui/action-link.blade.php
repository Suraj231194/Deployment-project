@props([
    'href' => null,
    'variant' => 'primary',
])

@php
    $style = match ($variant) {
        'secondary' => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 focus-visible:ring-primary-500/30',
        'contrast' => 'border border-slate-900 bg-slate-900 text-white hover:bg-primary-700 focus-visible:ring-slate-900/20',
        'inverse' => 'border border-white/30 bg-white/10 text-white hover:bg-white/20 focus-visible:ring-white/30',
        'dark' => 'border border-slate-950 bg-slate-950 text-white hover:bg-primary-700 focus-visible:ring-slate-950/20',
        default => 'border border-primary-600 bg-primary-600 text-white hover:bg-primary-700 focus-visible:ring-primary-500/30',
    };
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->class([
            'inline-flex h-11 items-center justify-center gap-2 rounded-xl px-5 text-sm font-semibold no-underline shadow-sm transition focus-visible:outline-none focus-visible:ring-2',
            $style,
        ]) }}
    >
        {{ $slot }}
    </a>
@else
    <span
        {{ $attributes->class([
            'inline-flex h-11 items-center justify-center gap-2 rounded-xl px-5 text-sm font-semibold shadow-sm transition',
            $style,
        ]) }}
    >
        {{ $slot }}
    </span>
@endif
