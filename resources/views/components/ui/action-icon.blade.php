@props([
    'type' => 'view', // 'view' (primary), 'edit' (secondary), 'document/add' (tertiary), 'delete' (danger)
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center shrink-0 h-7 w-7 rounded-full shadow-sm transition-all hover:-translate-y-px hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 text-white cursor-pointer';

    $typeClasses = match ($type) {
        'view', 'primary' => 'bg-primary-700 hover:bg-primary-800 focus-visible:ring-primary-700',
        'edit', 'secondary' => 'bg-secondary-500 hover:bg-secondary-600 focus-visible:ring-secondary-500',
        'document', 'add', 'tertiary' => 'bg-tertiary-600 hover:bg-tertiary-700 focus-visible:ring-tertiary-600',
        'delete', 'danger' => 'bg-rose-500 hover:bg-rose-600 focus-visible:ring-rose-500',
        default => 'bg-slate-500 hover:bg-slate-600 focus-visible:ring-slate-500',
    };

    $classes = "{$baseClasses} {$typeClasses}";
    $mergedAttributes = $attributes->merge(['class' => $classes]);
    
    // Add default title tooltips based on type if not explicitly set
    if (!$mergedAttributes->has('title')) {
        $title = match ($type) {
            'view' => 'View Details',
            'edit' => 'Edit',
            'document' => 'Download/View Document',
            'delete' => 'Delete',
            default => '',
        };
        if ($title) {
            $mergedAttributes = $mergedAttributes->merge(['title' => $title]);
        }
    }
@endphp

@if($href)
    <a href="{{ $href }}" {{ $mergedAttributes }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->has('type') && !in_array($type, ['view','edit','delete','document']) ? '' : 'type="button"' }} {{ $mergedAttributes }}>
        {{ $slot }}
    </button>
@endif
