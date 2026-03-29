@extends('layouts.app')

@section('title', 'Book a Meeting')

@section('content')
@php
    $sectionCardClass = 'rounded-[var(--ui-radius-card)] border border-slate-200/80 bg-white/95 p-6 shadow-[var(--ui-shadow-card)] backdrop-blur md:p-8';
    $accentCardClass = 'relative overflow-hidden rounded-[var(--ui-radius-card)] border border-primary-100/70 bg-gradient-to-br from-white via-primary-50/55 to-white p-6 shadow-[var(--ui-shadow-panel)] backdrop-blur md:p-8';
    $inputClass = 'block min-h-12 w-full rounded-2xl border border-slate-200 bg-white/95 px-4 py-3 text-sm text-slate-900 shadow-[var(--ui-shadow-soft)] transition placeholder:text-slate-400 focus:border-primary-500 focus:outline-none focus:ring-4 focus:ring-primary-500/10';
    $primaryButtonClass = 'inline-flex min-h-12 items-center justify-center rounded-2xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-600/20 transition hover:-translate-y-px hover:bg-primary-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500/20';
    $meetingUser = auth()->user();

    // Business step: keep old values visible after validation so the user can correct only the fields that failed.
    $minimumMeetingDate = $minimumMeetingDate ?? now()->toDateString();
    $preferredDateValue = old('preferred_date', $minimumMeetingDate);
    $startTimeValue = old('start_time', '09:00');
    $endTimeValue = old('end_time', '10:00');
    $fullNameValue = old('full_name', $meetingUser?->name ?? '');
    $emailValue = old('email', $meetingUser?->email ?? '');
    $phoneValue = preg_replace('/[^0-9]/', '', (string) old('phone', $meetingUser?->phone ?? ''));
    $phoneValue = strlen($phoneValue) > 10 ? substr($phoneValue, -10) : $phoneValue;
    $organizationNameValue = old('organization_name', '');
@endphp

<div class="bg-gradient-to-b from-white via-primary-50/20 to-white">
    <section class="relative overflow-hidden bg-primary-800 py-16 text-white md:py-24">
        <img src="{{ asset('upload/corousel/image3.jpg') }}" alt="Biogenix Meeting" class="absolute inset-0 h-full w-full object-cover opacity-20" loading="lazy" decoding="async">
        <div class="absolute inset-0 bg-gradient-to-t from-primary-800/95 via-primary-800/70 to-primary-600/30"></div>
        <div class="relative z-10 mx-auto w-full max-w-none px-4 text-center sm:px-6 lg:px-8 xl:px-10">
           
            <h1 class="mx-auto max-w-5xl font-display text-4xl font-bold tracking-tight text-secondary-600 md:text-5xl lg:text-6xl">
                Accelerate your diagnostics operations with Biogenix.
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-base leading-8 text-secondary-600 md:text-lg">
                Schedule a one-on-one session with our specialists to discuss product catalogs, bulk procurement, and enterprise supply chain solutions tailored to your lab or hospital.
            </p>
        </div>
    </section>

    <section class="bg-transparent py-12 md:py-20">
        <div class="mx-auto grid w-full max-w-none grid-cols-1 gap-12 px-4 sm:px-6 lg:grid-cols-12 lg:gap-8 lg:px-8 xl:px-10">
            <div class="flex flex-col justify-center space-y-8 lg:col-span-5">
                <div>
                    <h2 class="font-display text-3xl font-semibold tracking-tight text-slate-950 md:text-4xl">Why book a meeting?</h2>
                    <p class="mt-3 max-w-none text-base leading-8 text-slate-600">We go beyond simple transactions. A dedicated session allows us to map our diagnostic solutions directly to your clinical throughput and budget requirements.</p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start gap-4 rounded-3xl border border-slate-200/80 bg-white/80 p-4 shadow-[var(--ui-shadow-soft)] backdrop-blur">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Tailored Product Mapping</h3>
                            <p class="mt-1 text-sm text-slate-600">Get personalized recommendations across IVD kits, reagents, and instruments matching your facility's scale.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 rounded-3xl border border-slate-200/80 bg-white/80 p-4 shadow-[var(--ui-shadow-soft)] backdrop-blur">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-primary-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Custom Commercial Models</h3>
                            <p class="mt-1 text-sm text-slate-600">Discuss bulk pricing, credit terms, and compliant procurement models suited for modern healthcare accounts.</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 rounded-3xl border border-slate-200/80 bg-white/80 p-4 shadow-[var(--ui-shadow-soft)] backdrop-blur">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-700">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900">Dedicated Support Pipeline</h3>
                            <p class="mt-1 text-sm text-slate-600">Learn how our Lucknow-led fulfillment hub and priority escalation desks ensure maximum uptime for your lab.</p>
                        </div>
                    </div>
                </div>

                <div class="{{ $sectionCardClass }}">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-full bg-slate-200">
                            <img src="{{ asset('upload/icons/logo.jpg') }}" alt="Expert" class="h-full w-full object-cover">
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Enterprise Sales Team</p>
                            <p class="text-xs text-slate-500">Biogenix Corporate Office</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm italic text-slate-700">"We aim to streamline your entire procurement process. A quick 15-minute chat lets us tailor a solution exactly for your scale and regional requirements."</p>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="{{ $accentCardClass }}">
                    <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-primary-50 opacity-50 blur-3xl"></div>

                    <div class="mb-8 border-b border-slate-100 pb-5">
                        <h2 class="font-display text-3xl font-semibold tracking-tight text-slate-950">Schedule Your Session</h2>
                        <p class="mt-2 max-w-none text-base leading-8 text-slate-600">Choose your preferred date and time range, then share your details. Our team will confirm the meeting over email or phone.</p>
                    </div>

                    <form id="meetingForm" action="{{ route('book-meeting.store') }}" method="POST" class="relative z-10">
                        @csrf

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <div>
                                <label for="preferred_date" class="mb-1 block text-sm font-medium text-slate-700">Preferred Date</label>
                                <input id="preferred_date" name="preferred_date" type="date" min="{{ $minimumMeetingDate }}" value="{{ $preferredDateValue }}" class="{{ $inputClass }} @error('preferred_date') border-rose-400 ring-4 ring-rose-500/10 @enderror" required>
                                @error('preferred_date')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-end">
                                <p class="w-full rounded-2xl border border-primary-100/80 bg-primary-50/70 px-4 py-3 text-sm font-medium text-slate-600">
                                    Meeting hours: <span class="font-semibold text-slate-900">09:00 to 18:00 IST</span>
                                </p>
                            </div>

                            <div>
                                <label for="start_time" class="mb-2 block text-sm font-medium text-slate-700">Start time</label>
                                <div class="relative">
                                    <input id="start_time" name="start_time" type="time" min="09:00" max="18:00" value="{{ $startTimeValue }}" class="{{ $inputClass }} @error('start_time') border-rose-400 ring-4 ring-rose-500/10 @enderror pr-11" required>
                                </div>
                                @error('start_time')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_time" class="mb-2 block text-sm font-medium text-slate-700">End time</label>
                                <div class="relative">
                                    <input id="end_time" name="end_time" type="time" min="09:00" max="18:00" value="{{ $endTimeValue }}" class="{{ $inputClass }} @error('end_time') border-rose-400 ring-4 ring-rose-500/10 @enderror pr-11" required>
                                </div>
                                @error('end_time')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                @error('time_range')
                                    <p class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="full_name" class="mb-1 block text-sm font-medium text-slate-700">Full Name</label>
                                <input id="full_name" name="full_name" type="text" value="{{ $fullNameValue }}" class="{{ $inputClass }} @error('full_name') border-rose-400 ring-4 ring-rose-500/10 @enderror" placeholder="e.g. Dr. Jane Doe" maxlength="150" required>
                                @error('full_name')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Work Email</label>
                                <input id="email" name="email" type="email" value="{{ $emailValue }}" class="{{ $inputClass }} @error('email') border-rose-400 ring-4 ring-rose-500/10 @enderror" placeholder="you@hospital.com" maxlength="150" required>
                                @error('email')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">Phone Number</label>
                                <div class="relative">
                                    <input id="phone" name="phone" type="text" value="{{ $phoneValue }}" class="{{ $inputClass }} @error('phone') border-rose-400 ring-4 ring-rose-500/10 @enderror pl-12" placeholder="9876543210" maxlength="10" inputmode="numeric" required>
                                </div>
                                @error('phone')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="organization_name" class="mb-1 flex items-center justify-between text-sm font-medium text-slate-700">
                                    <span>Organization / Hospital Name</span>
                                    <span class="text-xs font-normal text-slate-400">Optional</span>
                                </label>
                                <input id="organization_name" name="organization_name" type="text" value="{{ $organizationNameValue }}" class="{{ $inputClass }} @error('organization_name') border-rose-400 ring-4 ring-rose-500/10 @enderror" placeholder="Name of your institution" maxlength="150">
                                @error('organization_name')
                                    <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <button type="submit" id="meetingSubmitBtn" class="{{ $primaryButtonClass }} w-full md:w-auto">Confirm Meeting Request</button>
                            <p class="text-sm font-medium text-slate-500">Business meetings are confirmed manually by our team.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-gradient-to-b from-white via-primary-50/10 to-white py-14 md:py-20">
        <div class="mx-auto w-full max-w-none px-4 sm:px-6 lg:px-8 xl:px-10">
            <x-ui.section-heading title="Meeting Formats" subtitle="Pick a format that fits your objective: commercial, technical, or strategic." />
            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ([
                    ['title' => 'Discovery (15-20 min)', 'copy' => 'Quick fitment call to map product lines to your workflow and budget expectations.'],
                    ['title' => 'Solution Deep Dive (30-40 min)', 'copy' => 'Technical deep dive with specs, compatibility, validation approach, and QA expectations.'],
                    ['title' => 'Procurement Alignment (25-30 min)', 'copy' => 'Commercial and logistics review: SLAs, delivery windows, payment terms, and governance.'],
                ] as $format)
                    <article class="h-full rounded-[var(--ui-radius-card)] border border-slate-200/80 bg-white/95 p-6 shadow-[var(--ui-shadow-card)] backdrop-blur transition hover:-translate-y-1.5 hover:border-primary-100 hover:shadow-[var(--ui-shadow-panel)] md:p-8">
                        <h3 class="font-display text-xl font-semibold text-slate-950">{{ $format['title'] }}</h3>
                        <p class="mt-3 text-sm leading-6 text-slate-600">{{ $format['copy'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');

        if (!startTimeInput || !endTimeInput) {
            return;
        }

        // Business step: keep the end time aligned with the chosen start time so the user gets a cleaner schedule selection experience.
        const syncEndTimeWindow = function () {
            endTimeInput.min = startTimeInput.value || '09:00';

            if (endTimeInput.value && startTimeInput.value && endTimeInput.value <= startTimeInput.value) {
                endTimeInput.value = '';
            }
        };

        syncEndTimeWindow();
        startTimeInput.addEventListener('input', syncEndTimeWindow);
    });
</script>
@endpush
@endsection
