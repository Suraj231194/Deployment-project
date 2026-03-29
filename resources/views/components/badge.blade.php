@props([
    'variant' => 'default',
])

@php
    $style = match ($variant) {
        'success' => 'border-primary-200 bg-primary-50 text-primary-600',
        'warning' => 'border-amber-200 bg-secondary-50 text-secondary-700',
        'danger' => 'border-rose-200 bg-rose-50 text-rose-700',
        'info' => 'border-primary-200 bg-primary-50 text-primary-700',
        'inverse' => 'border-white/20 bg-white/10 text-white',
        default => 'border-slate-200 bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold', $style]) }}>
    {{ $slot }}
</span>
