@extends('layouts.app')

@section('title', 'Terms & Conditions')

@section('content')
{{-- Terms & Conditions – matches reference image with accordion sections --}}
<div class="min-h-screen bg-slate-50 pb-24">
    {{-- Hero header --}}
    <section class="border-b border-slate-200 bg-white py-12 md:py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-950 md:text-4xl">Terms &amp; Conditions</h1>
            <p class="mt-3 flex items-center gap-2 text-sm text-primary-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Last updated: October 2023
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Intro box --}}
        <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <p class="text-base leading-8 text-slate-700">Welcome to Biogenix. We provide cutting-edge biotechnological solutions and research data. By accessing our platform, you agree to comply with and be bound by the following terms and conditions of use, which together with our privacy policy govern Biogenix's relationship with you in relation to this website.</p>
        </div>

        {{-- Accordion sections --}}
        <div class="space-y-4" x-data="{ openSection: 1 }">
            @php
                $sections = [
                    [
                        'num' => 1,
                        'title' => 'Acceptance of Terms',
                        'content' => '<p class="leading-relaxed text-slate-600">By accessing and using this website, you accept and agree to be bound by the terms and provision of this agreement. In addition, when using these particular services, you shall be subject to any posted guidelines or rules applicable to such services.</p><p class="mt-4 leading-relaxed text-slate-600">Any participation in this service will constitute acceptance of this agreement. If you do not agree to abide by the above, please do not use this service.</p>'
                    ],
                    [
                        'num' => 2,
                        'title' => 'Use of Website',
                        'content' => '<p class="leading-relaxed text-slate-600">You agree to use this website only for lawful purposes and in a way that does not infringe the rights of, restrict, or inhibit anyone else\'s use and enjoyment of the website. Prohibited behavior includes harassing or causing distress to any other user, transmitting obscene or offensive content, or disrupting the normal flow of dialogue within the website.</p>'
                    ],
                    [
                        'num' => 3,
                        'title' => 'Intellectual Property',
                        'content' => '<p class="leading-relaxed text-slate-600">All content included on this site, such as text, graphics, logos, button icons, images, audio clips, digital downloads, data compilations, and software, is the property of Biogenix or its content suppliers and protected by international copyright laws. The compilation of all content on this site is the exclusive property of Biogenix.</p>'
                    ],
                    [
                        'num' => 4,
                        'title' => 'Limitation of Liability',
                        'content' => '<p class="leading-relaxed text-slate-600">Biogenix shall not be liable for any indirect, incidental, special, consequential or punitive damages, including without limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting from your access to or use of or inability to access or use the services.</p>'
                    ],
                    [
                        'num' => 5,
                        'title' => 'Governing Law (Lucknow Jurisdiction)',
                        'content' => '<p class="leading-relaxed text-slate-600">These terms shall be governed and construed in accordance with the laws of India, without regard to its conflict of law provisions. Any disputes arising from these terms shall be subject to the exclusive jurisdiction of the courts located in Lucknow, Uttar Pradesh, India.</p>'
                    ],
                ];
            @endphp

            @foreach ($sections as $section)
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300">
                    <button
                        type="button"
                        class="flex w-full items-center gap-4 px-6 py-5 text-left transition hover:bg-slate-50"
                        onclick="toggleTermsSection({{ $section['num'] }})"
                        aria-expanded="false"
                        id="terms-btn-{{ $section['num'] }}"
                    >
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-700">{{ $section['num'] }}</span>
                        <span class="flex-1 text-base font-semibold text-slate-900">{{ $section['title'] }}</span>
                        <svg class="terms-chevron h-5 w-5 shrink-0 text-slate-400 transition-transform duration-300" id="terms-chevron-{{ $section['num'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="terms-panel hidden" id="terms-panel-{{ $section['num'] }}">
                        <div class="border-t border-slate-100 px-6 pb-6 pt-4 pl-[4.5rem]">
                            {!! $section['content'] !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Footer bar --}}
    <div class="border-t border-slate-200 bg-white py-6">
        <div class="mx-auto flex max-w-4xl flex-wrap items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <div class="relative h-9 w-9 rounded-xl bg-primary-600">
                    <span class="absolute left-2 top-2 h-2 w-2 rounded-sm bg-white"></span>
                    <span class="absolute bottom-2 right-2 h-2 w-2 rounded-sm bg-white"></span>
                </div>
                <span class="text-sm font-semibold text-slate-900">Biogenix Labs</span>
            </div>
            <p class="text-sm text-slate-500">&copy; 2023 Biogenix International. All rights reserved.</p>
            <nav class="flex flex-wrap gap-6">
                <a href="{{ route('privacy') }}" class="text-sm font-medium text-slate-600 no-underline hover:text-primary-600">Privacy Policy</a>
                <a href="{{ route('privacy') }}" class="text-sm font-medium text-slate-600 no-underline hover:text-primary-600">Cookie Policy</a>
                <a href="{{ route('contact') }}" class="text-sm font-medium text-slate-600 no-underline hover:text-primary-600">Compliance</a>
            </nav>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Open section 1 by default
    document.addEventListener('DOMContentLoaded', function() {
        toggleTermsSection(1);
    });

    function toggleTermsSection(num) {
        var panel = document.getElementById('terms-panel-' + num);
        var chevron = document.getElementById('terms-chevron-' + num);
        var btn = document.getElementById('terms-btn-' + num);
        if (!panel) return;

        var isOpen = !panel.classList.contains('hidden');

        // Close all panels
        document.querySelectorAll('.terms-panel').forEach(function(p) { p.classList.add('hidden'); });
        document.querySelectorAll('.terms-chevron').forEach(function(c) { c.classList.remove('rotate-180'); });
        document.querySelectorAll('[id^="terms-btn-"]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });

        // Toggle if was closed
        if (!isOpen) {
            panel.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            btn.setAttribute('aria-expanded', 'true');
        }
    }
</script>
@endpush
@endsection
