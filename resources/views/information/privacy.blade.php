@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
{{-- Privacy Policy – accordion style matching Terms & Conditions design --}}
<div class="min-h-screen bg-slate-50 pb-24">
    {{-- Hero header --}}
    <section class="border-b border-slate-200 bg-white py-12 md:py-16">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-950 md:text-4xl">Privacy Policy</h1>
            <p class="mt-3 flex items-center gap-2 text-sm text-primary-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Last updated: October 2023
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Intro box --}}
        <div class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm md:p-8">
            <p class="text-base leading-8 text-slate-700">Welcome to Biogenix. This privacy policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our biotechnological solutions and research platform. Please read this privacy policy carefully.</p>
        </div>

        {{-- Accordion sections --}}
        <div class="space-y-4">
            @php
                $privacySections = [
                    [
                        'num' => 1,
                        'title' => 'Information We Collect',
                        'content' => '<p class="leading-relaxed text-slate-600">We collect information that you provide directly to us, including personal details during registration, quotation requests, support interactions, and account operations. This includes your name, email address, phone number, organization details, and any other information you choose to provide.</p><p class="mt-4 leading-relaxed text-slate-600">We also automatically collect certain information when you access our services, including your IP address, browser type, operating system, and usage patterns.</p>'
                    ],
                    [
                        'num' => 2,
                        'title' => 'How We Use Your Information',
                        'content' => '<p class="leading-relaxed text-slate-600">We use the information we collect to provide, maintain, and improve our services, process transactions, send transactional messages, and provide customer support. We may also use the information to send you technical notices, updates, security alerts, and product announcements.</p>'
                    ],
                    [
                        'num' => 3,
                        'title' => 'Data Sharing & Disclosure',
                        'content' => '<p class="leading-relaxed text-slate-600">We do not sell your personal data. We may share your information with trusted service providers who assist us in operating our platform, conducting business, or servicing you. These third parties are contractually obligated to keep your information confidential and use it only for the purposes for which we disclose it to them.</p>'
                    ],
                    [
                        'num' => 4,
                        'title' => 'Data Security',
                        'content' => '<p class="leading-relaxed text-slate-600">We implement appropriate technical and organizational security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. This includes encryption, secure authentication protocols, and regular security audits of our systems.</p>'
                    ],
                    [
                        'num' => 5,
                        'title' => 'Your Rights & Choices',
                        'content' => '<p class="leading-relaxed text-slate-600">You have the right to access, correct, or delete your personal information. You can also opt out of receiving marketing communications from us at any time. To exercise any of these rights, please contact our Data Protection Officer at <a href="mailto:support@biogenix.com" class="font-semibold text-primary-700 underline decoration-primary-200 underline-offset-4 hover:decoration-primary-600">support@biogenix.com</a>.</p>'
                    ],
                    [
                        'num' => 6,
                        'title' => 'Cookies & Tracking',
                        'content' => '<p class="leading-relaxed text-slate-600">We use cookies and similar tracking technologies to track the activity on our service and hold certain information. Cookies are files with a small amount of data which may include an anonymous unique identifier. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.</p>'
                    ],
                ];
            @endphp

            @foreach ($privacySections as $section)
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300">
                    <button
                        type="button"
                        class="flex w-full items-center gap-4 px-6 py-5 text-left transition hover:bg-slate-50"
                        onclick="togglePrivacySection({{ $section['num'] }})"
                        aria-expanded="false"
                        id="privacy-btn-{{ $section['num'] }}"
                    >
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-sm font-bold text-primary-700">{{ $section['num'] }}</span>
                        <span class="flex-1 text-base font-semibold text-slate-900">{{ $section['title'] }}</span>
                        <svg class="privacy-chevron h-5 w-5 shrink-0 text-slate-400 transition-transform duration-300" id="privacy-chevron-{{ $section['num'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div class="privacy-panel hidden" id="privacy-panel-{{ $section['num'] }}">
                        <div class="border-t border-slate-100 px-6 pb-6 pt-4 pl-[4.5rem]">
                            {!! $section['content'] !!}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Contact box --}}
        <div class="mt-10 rounded-2xl border border-primary-100 bg-primary-50/60 p-6">
            <h2 class="mb-2 flex items-center text-xl font-semibold text-slate-900">
                <svg class="mr-2 h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                Contact for Privacy Requests
            </h2>
            <p class="text-slate-600">For any privacy-related queries, data deletion requests, or concerns, please contact our Data Protection Officer at <a href="mailto:support@biogenix.com" class="font-semibold text-primary-700 underline decoration-primary-100 underline-offset-4 transition hover:decoration-primary-600">support@biogenix.com</a>.</p>
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
                <a href="{{ route('terms') }}" class="text-sm font-medium text-slate-600 no-underline hover:text-primary-600">Terms of Service</a>
                <a href="{{ route('privacy') }}" class="text-sm font-medium text-slate-600 no-underline hover:text-primary-600">Cookie Policy</a>
                <a href="{{ route('contact') }}" class="text-sm font-medium text-slate-600 no-underline hover:text-primary-600">Compliance</a>
            </nav>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        togglePrivacySection(1);
    });

    function togglePrivacySection(num) {
        var panel = document.getElementById('privacy-panel-' + num);
        var chevron = document.getElementById('privacy-chevron-' + num);
        var btn = document.getElementById('privacy-btn-' + num);
        if (!panel) return;

        var isOpen = !panel.classList.contains('hidden');

        document.querySelectorAll('.privacy-panel').forEach(function(p) { p.classList.add('hidden'); });
        document.querySelectorAll('.privacy-chevron').forEach(function(c) { c.classList.remove('rotate-180'); });
        document.querySelectorAll('[id^="privacy-btn-"]').forEach(function(b) { b.setAttribute('aria-expanded', 'false'); });

        if (!isOpen) {
            panel.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            btn.setAttribute('aria-expanded', 'true');
        }
    }
</script>
@endpush
@endsection
