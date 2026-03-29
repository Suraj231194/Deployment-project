@props([
    'type' => 'info',
])

@php
    $style = match ($type) {
        'success' => 'border border-primary-200 bg-primary-50 text-primary-600',
        'error' => 'border border-rose-200 bg-rose-50 text-rose-700',
        'warning' => 'border border-amber-200 bg-secondary-50 text-amber-800',
        default => 'border border-primary-100 bg-primary-50 text-primary-700',
    };
@endphp

<div role="alert" {{ $attributes->class(["rounded-2xl px-4 py-3 text-sm", $style]) }}>
    {{ $slot }}
</div>
