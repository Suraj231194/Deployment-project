@extends('layouts.app')

@section('title', 'Refund & Cancellation Policy')

@section('content')
{{-- Refund & Cancellation Policy – matching reference image with tabs, icons, and process steps --}}
<div class="min-h-screen bg-slate-50 pb-24">
    {{-- Hero header --}}
    <section class="border-b border-slate-200 bg-white py-12 md:py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <nav class="mb-4 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="text-slate-500 no-underline hover:text-primary-600">Home</a>
                <span class="mx-2">&rsaquo;</span>
                <span class="text-slate-700">Legal</span>
            </nav>
            <h1 class="text-3xl font-bold tracking-tight text-slate-950 md:text-4xl">Refund &amp; Cancellation Policy</h1>
            <p class="mt-3 max-w-xl text-base leading-7 text-slate-600">Standard guidelines for reagents, laboratory equipment, and cold-chain logistics to ensure product integrity and research continuity.</p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Tab navigation --}}
        <div class="mb-10 border-b border-slate-200">
            <nav class="flex gap-6" id="refund-tabs">
                <button type="button" class="refund-tab active border-b-2 border-primary-600 pb-3 text-sm font-semibold text-primary-700 transition" data-tab="reagents" onclick="switchRefundTab('reagents')">Reagents &amp; Biologicals</button>
                <button type="button" class="refund-tab border-b-2 border-transparent pb-3 text-sm font-semibold text-slate-500 transition hover:text-slate-700" data-tab="equipment" onclick="switchRefundTab('equipment')">Lab Equipment</button>
                <button type="button" class="refund-tab border-b-2 border-transparent pb-3 text-sm font-semibold text-slate-500 transition hover:text-slate-700" data-tab="general" onclick="switchRefundTab('general')">General Policy</button>
            </nav>
        </div>

        {{-- Tab content: Reagents & Biologicals --}}
        <div class="refund-tab-content" id="tab-reagents">
            {{-- Section 1: Cancellation Policy --}}
            <div class="mb-10">
                <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-600">1</span>
                    Cancellation Policy
                </h2>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    {{-- Reagents card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-3 flex items-center gap-2">
                            <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <h3 class="text-base font-semibold text-primary-700">Reagents &amp; Perishables</h3>
                        </div>
                        <p class="text-sm leading-7 text-slate-600">Cancellations for temperature-sensitive reagents must be made within <strong>4 hours</strong> of order placement. Due to the high-precision nature of biological formulations, once packaging for refrigerated/frozen shipping commences, cancellations are not permitted.</p>
                    </div>

                    {{-- Lab Equipment card --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="mb-3 flex items-center gap-2">
                            <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                            <h3 class="text-base font-semibold text-primary-700">Laboratory Equipment</h3>
                        </div>
                        <p class="text-sm leading-7 text-slate-600">Standard hardware orders can be cancelled within <strong>24 hours</strong> without penalty. Custom-configured instrumentation or specialized robotic systems may incur a 15% restocking fee if cancelled after manufacturing has initiated.</p>
                    </div>
                </div>
            </div>

            {{-- Section 2: Refund Process --}}
            <div class="mb-10">
                <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-600">2</span>
                    Refund Process
                </h2>

                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="mb-5 text-sm leading-7 text-slate-600">Biogenix ensures a rigorous validation process for all refund requests to maintain the integrity of our biotech supply chain.</p>

                    <div class="space-y-5">
                        <div class="flex gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50">
                                <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900">Integrity Validation</h4>
                                <p class="mt-1 text-sm leading-7 text-slate-600">Refunds are only processed for reagents that remain in their original, unopened manufacturer packaging with intact tamper-evident seals.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-secondary-50">
                                <svg class="h-5 w-5 text-secondary-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900">Reimbursement Timeline</h4>
                                <p class="mt-1 text-sm leading-7 text-slate-600">Approved refunds will be credited back to the original funding source (Grant P-Card, Institutional Wire, etc.) within 7-10 business days.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-50">
                                <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900">Cold Chain Exclusion</h4>
                                <p class="mt-1 text-sm leading-7 text-slate-600">Products requiring -20°C or -80°C storage are non-returnable once they leave our facility to ensure zero risk of thermal degradation for other researchers.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Damaged Goods & Logistics --}}
            <div class="mb-10">
                <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-600">3</span>
                    Damaged Goods &amp; Logistics
                </h2>

                <div class="mt-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                        <h3 class="text-base font-semibold text-slate-900">Immediate Action Required</h3>
                        <p class="mt-2 text-sm leading-7 text-slate-600">If laboratory equipment arrives damaged or reagents show signs of temperature excursion (thawed dry ice or triggered heat indicators):</p>

                        <div class="mt-5 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-primary-50">
                                    <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                                </div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-primary-600">Step 1</p>
                                <p class="mt-1 text-sm text-slate-700">Document packaging &amp; indicators</p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-primary-50">
                                    <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-5 5v-5zM4.868 20.132A9 9 0 1120.132 4.868M5 12h14"/></svg>
                                </div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-primary-600">Step 2</p>
                                <p class="mt-1 text-sm text-slate-700">Notify within 24 hours of receipt</p>
                            </div>

                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-primary-50">
                                    <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-primary-600">Step 3</p>
                                <p class="mt-1 text-sm text-slate-700">Contact logistics@biogenix.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab content: Lab Equipment --}}
        <div class="refund-tab-content hidden" id="tab-equipment">
            <div class="mb-10">
                <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-600">1</span>
                    Equipment Cancellation
                </h2>
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm leading-7 text-slate-600">Standard laboratory equipment orders can be cancelled within <strong>24 hours</strong> of placement without any restocking fee. Custom-configured or built-to-order instrumentation may incur cancellation charges of up to 15% after the manufacturing process has initiated.</p>
                </div>
            </div>
            <div class="mb-10">
                <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-600">2</span>
                    Equipment Returns
                </h2>
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm leading-7 text-slate-600">Equipment must be returned in its original packaging within <strong>14 days</strong> of receipt. Items must be unused and in the same condition as received. Biogenix will arrange pickup for approved returns. Refunds will be processed within 10-14 business days.</p>
                </div>
            </div>
        </div>

        {{-- Tab content: General Policy --}}
        <div class="refund-tab-content hidden" id="tab-general">
            <div class="mb-10">
                <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-600">1</span>
                    General Refund Guidelines
                </h2>
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm leading-7 text-slate-600">All refund requests must be submitted through the Biogenix support portal or by emailing <a href="mailto:support@biogenix.com" class="font-semibold text-primary-700 underline underline-offset-4">support@biogenix.com</a>. Include your order number, reason for refund, and any supporting documentation (photographs, temperature logs, etc.).</p>
                </div>
            </div>
            <div class="mb-10">
                <h2 class="flex items-center gap-3 text-xl font-bold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-600">2</span>
                    Non-Refundable Items
                </h2>
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm leading-7 text-slate-600">Certain products are non-refundable, including custom-synthesized reagents, opened consumables, software licenses, and calibration services. These items are clearly marked during the ordering process.</p>
                </div>
            </div>
        </div>

        {{-- CTA Banner --}}
        <div class="mt-12 rounded-2xl bg-slate-900 px-8 py-8 text-center text-white">
            <h2 class="text-xl font-bold text-white">Need specialized assistance?</h2>
            <p class="mt-2 text-sm text-slate-300">Our biotech logistics specialists are available to discuss complex institutional procurement needs.</p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('contact') }}" class="inline-flex h-11 items-center gap-2 rounded-xl bg-primary-600 px-6 text-sm font-semibold text-white no-underline shadow-sm transition hover:bg-primary-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Contact Support
                </a>
                <a href="{{ route('faq') }}" class="inline-flex h-11 items-center gap-2 rounded-xl border border-slate-500 bg-transparent px-6 text-sm font-semibold text-white no-underline transition hover:bg-white/10">View FAQ</a>
            </div>
        </div>
    </section>

    {{-- Footer bar --}}
    <div class="mt-8 border-t border-slate-200 bg-white py-6">
        <div class="mx-auto flex max-w-4xl flex-wrap items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <p class="text-sm text-slate-500">&copy; 2024 Biogenix Biotech Solutions. All rights reserved.</p>
            <nav class="flex flex-wrap gap-6">
                <a href="{{ route('terms') }}" class="text-sm font-medium text-slate-600 no-underline hover:text-primary-600">Terms of Service</a>
                <a href="{{ route('privacy') }}" class="text-sm font-medium text-slate-600 no-underline hover:text-primary-600">Privacy Policy</a>
                <a href="{{ route('contact') }}" class="text-sm font-medium text-slate-600 no-underline hover:text-primary-600">Compliance</a>
            </nav>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchRefundTab(tab) {
    // Update tab buttons
    document.querySelectorAll('.refund-tab').forEach(function(btn) {
        var isActive = btn.getAttribute('data-tab') === tab;
        btn.classList.toggle('active', isActive);
        btn.classList.toggle('border-primary-600', isActive);
        btn.classList.toggle('text-primary-700', isActive);
        btn.classList.toggle('border-transparent', !isActive);
        btn.classList.toggle('text-slate-500', !isActive);
    });

    // Update tab content
    document.querySelectorAll('.refund-tab-content').forEach(function(content) {
        content.classList.add('hidden');
    });
    var activeContent = document.getElementById('tab-' + tab);
    if (activeContent) {
        activeContent.classList.remove('hidden');
    }
}
</script>
@endpush
@endsection
