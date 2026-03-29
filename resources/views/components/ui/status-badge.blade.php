@props([
    'type' => 'status',
    'value' => null,
    'label' => null,
    'dot' => false,
    'uppercase' => false,
])

@php
    use Illuminate\Support\Str;

    $resolvedLabel = (string) ($label ?? Str::of((string) $value)->replace('_', ' ')->headline());
    $normalizedValue = Str::of((string) ($value ?? $resolvedLabel))
        ->lower()
        ->replace(['-', ' '], '_')
        ->value();

    $styles = match ($type) {
        'priority' => [
            'urgent' => 'border-rose-200 bg-rose-50 text-rose-700',
            'high' => 'border-amber-200 bg-secondary-50 text-secondary-700',
            'medium' => 'border-primary-200 bg-primary-50 text-primary-700',
            'low' => 'border-slate-200 bg-slate-100 text-slate-700',
        ],
        'product' => [
            'in_stock' => 'border-primary-200 bg-primary-50 text-primary-600',
            'verified' => 'border-primary-200 bg-primary-50 text-primary-600',
            'clinical_ready' => 'border-primary-200 bg-primary-50 text-primary-700',
            'new_arrival' => 'border-primary-200 bg-primary-50 text-primary-700',
            'best_seller' => 'border-slate-900 bg-slate-900 text-white',
            'institutional_fav' => 'border-slate-200 bg-slate-100 text-slate-700',
            'legacy_support' => 'border-slate-200 bg-white/90 text-slate-700',
            'limited_availability' => 'border-amber-200 bg-secondary-50 text-secondary-700',
        ],
        'cart' => [
            'dispatch_24_48h' => 'border-primary-200 bg-primary-50 text-primary-700',
            'ships_in_24_48_hours' => 'border-primary-200 bg-primary-50 text-primary-700',
            'procurement_ready' => 'border-primary-200 bg-primary-50 text-primary-600',
            'fastest' => 'border-primary-200 bg-primary-50 text-primary-700',
            'validated_packaging' => 'border-slate-200 bg-white text-slate-700',
            'priority_support_available' => 'border-slate-200 bg-white text-slate-700',
            'secure_enterprise_checkout' => 'border-slate-200 bg-white text-slate-700',
            'gst_ready_invoices' => 'border-slate-200 bg-white text-slate-700',
            'cold_chain_dispatch_support' => 'border-slate-200 bg-white text-slate-700',
            'bank_grade_encrypted_transaction' => 'border-slate-200 bg-white text-slate-700',
            'validated_lab_documentation' => 'border-slate-200 bg-white text-slate-700',
        ],
        default => [
            'draft' => 'border-amber-200 bg-secondary-50 text-secondary-700',
            'submitted' => 'border-primary-200 bg-primary-50 text-primary-700',
            'cancelled' => 'border-rose-200 bg-rose-50 text-rose-700',
            'open' => 'border-slate-200 bg-slate-100 text-slate-700',
            'in_progress' => 'border-primary-200 bg-primary-50 text-primary-700',
            'awaiting_response' => 'border-amber-200 bg-secondary-50 text-secondary-700',
            'closed' => 'border-primary-200 bg-primary-50 text-primary-600',
        ],
    };

    $style = $styles[$normalizedValue] ?? 'border-slate-200 bg-slate-100 text-slate-700';
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold', $style]) }}>
    @if ($dot)
        <span class="h-2 w-2 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    @endif
    <span class="{{ $uppercase ? 'uppercase tracking-wide' : '' }}">{{ $resolvedLabel }}</span>
</span>
