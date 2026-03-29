@props([
    'paginator' => null,
])

@if ($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())
    <div {{ $attributes->class(['flex items-center justify-center pt-2']) }}>
        {{ $paginator->links() }}
    </div>
@endif
