@php
    $showSuccessState = request()->boolean('sent') || filled(session('status')) || filled(session('success'));
    $kickerClass = 'inline-flex items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-primary-700';
    $labelClass = 'text-sm font-semibold text-slate-700';
    $inputClass = 'h-14 w-full rounded-2xl border border-slate-300 bg-white px-12 text-base font-medium text-slate-900 transition placeholder:text-slate-400 focus:border-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-600/10';
    $linkClass = 'font-semibold text-primary-700 no-underline transition hover:text-primary-600';
    $submitClass = 'inline-flex h-14 w-full items-center justify-center rounded-2xl bg-primary-600 text-base font-semibold text-white shadow-[0_16px_35px_-18px_rgba(26,77,46,0.35)] transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-70';
@endphp

@extends('layouts.app')

@section('title', 'Forgot Password - Biogenix')

@section('content')
<div class="mx-auto w-full max-w-6xl py-2 md:py-4">
    <section class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_70px_-32px_rgba(15,23,42,0.35)]">
        <div class="grid min-h-[40rem] grid-cols-1 lg:grid-cols-[minmax(0,1fr)_minmax(420px,500px)]">
            <aside class="relative hidden overflow-hidden lg:flex lg:items-end lg:p-9">
                <img
                    src="{{ asset('upload/corousel/home2.jpg') }}"
                    alt="Biogenix laboratory"
                    class="absolute inset-0 h-full w-full object-cover"
                    loading="lazy"
                    decoding="async"
                >
                <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(13,43,25,0.24)_0%,rgba(13,43,25,0.72)_46%,#1A4D2E_100%)]"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_62%_18%,rgba(255,255,255,0.22),transparent_28%)]"></div>
                <div class="absolute left-7 top-5 h-4 w-72 rounded-full bg-white/75 shadow-lg"></div>
                <div class="absolute left-[3.2rem] top-0 h-6 w-px bg-white/25"></div>
                <div class="absolute left-[18.8rem] top-0 h-6 w-px bg-white/25"></div>

                <div class="relative z-10 max-w-sm">
                    <div class="flex items-center gap-3">
                        <svg class="h-8 w-8 shrink-0 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M7 3h10"></path>
                            <path d="M10 3v3"></path>
                            <path d="M14 3v3"></path>
                            <path d="M7 6h10l-1 2.5v4.5a5 5 0 0 1-4 4.9V21"></path>
                            <path d="M17 6l1 2.5"></path>
                            <path d="M7 6 6 8.5"></path>
                            <path d="M8.5 13h7"></path>
                            <path d="M9 21h6"></path>
                            <path d="M6 21h12"></path>
                        </svg>
                        <p class="text-4xl font-bold tracking-tight text-secondary-600">Biogenix</p>
                    </div>

                    <p class="mt-6 text-lg font-semibold leading-9 text-secondary-600">
                        Securely recover your account access. Follow the instructions to reset your password and continue your workflows.
                    </p>
                </div>
            </aside>

            <div class="flex items-center bg-[linear-gradient(180deg,rgba(255,255,255,1)_0%,rgba(252,253,255,1)_100%)] px-5 py-8 sm:px-7 lg:px-10">
            <div class="mx-auto w-full max-w-md text-center">
                <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-primary-700 shadow-sm">
                    @if ($showSuccessState)
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 7 13.8 15.2a2.7 2.7 0 0 1-3.82 0L2 7"></path>
                            <path d="M4 5h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"></path>
                            <path d="m9 12 2 2 4-4"></path>
                        </svg>
                    @else
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 8V4"></path>
                            <path d="M8.5 6.5 12 3l3.5 3.5"></path>
                            <path d="M12 21a8 8 0 1 0-8-8"></path>
                            <path d="M4 16v5h5"></path>
                            <path d="M4 21l4-4"></path>
                        </svg>
                    @endif
                </div>

                @if ($showSuccessState)
                    <p class="{{ $kickerClass }} mt-5">Reset Link Sent</p>
                    <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight text-slate-950 md:text-4xl">Check your email</h1>
                    <p class="mx-auto mt-4 max-w-sm text-sm leading-7 text-slate-500 md:text-base">
                        We sent a password reset link to your registered email address. Follow the instructions in your inbox to secure your account.
                    </p>

                    <div class="mt-6 grid gap-4">
                        <a href="{{ route('login') }}" class="inline-flex h-11 w-full items-center justify-center rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700">Back to Login</a>
                    </div>

                    <div class="mt-6 border-t border-slate-200 pt-5">
                        <p class="text-sm leading-6 text-slate-400">Didn't receive the email?</p>
                        <div class="mt-2 inline-flex flex-wrap items-center justify-center gap-3 text-sm font-semibold text-slate-500">
                            <a href="{{ route('forgot.password') }}" class="{{ $linkClass }}">Resend link</a>
                            <span class="text-slate-300">|</span>
                            <a href="{{ route('contact') }}" class="{{ $linkClass }}">Contact support</a>
                        </div>
                    </div>

                    <div class="mt-6 rounded-3xl border border-primary-100 bg-primary-50 p-4 text-left">
                        <div class="mx-auto inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-primary-700 shadow-sm">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 3 5 6v6c0 5 3.5 8 7 9 3.5-1 7-4 7-9V6l-7-3Z"></path>
                                <path d="M9.5 12.5 12 15l3.5-4"></path>
                            </svg>
                        </div>
                        <div class="mt-3 rounded-2xl bg-white px-4 py-3 shadow-sm">
                            <p class="{{ $kickerClass }} bg-white px-0 py-0 text-primary-700">Security Tip</p>
                            <p class="mt-1.5 text-sm leading-6 text-slate-700">Biogenix will never ask for your password over email or phone.</p>
                        </div>
                    </div>
                @else
                    <p class="{{ $kickerClass }} mt-5">Account Recovery</p>
                    <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight text-slate-950 md:text-4xl">Forgot your password?</h1>
                    <p class="mx-auto mt-4 max-w-sm text-sm leading-7 text-slate-500 md:text-base">
                        Enter the email address associated with your Biogenix account and we will send you a secure reset link.
                    </p>

                    <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}" data-success-url="{{ route('forgot.password', ['sent' => 1]) }}" class="mt-6 grid gap-5 text-left" novalidate>
                        @csrf

                        <div class="grid gap-2" data-field-group>
                            <label for="forgotEmail" class="{{ $labelClass }}">Work Email</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 6h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Z"></path>
                                    <path d="m22 8-8.97 6.39a1.8 1.8 0 0 1-2.06 0L2 8"></path>
                                </svg>
                                <input
                                    type="text"
                                    id="forgotEmail"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="{{ $inputClass }}"
                                    placeholder="name@biogenix.com"
                                    autocomplete="email"
                                >
                            </div>
                            <p data-field-error class="hidden text-sm font-medium text-rose-600"></p>
                        </div>

                        <button type="submit" id="forgotSubmitBtn" class="{{ $submitClass }}">
                            Send Reset Link
                        </button>
                    </form>

                    <div class="mt-6 border-t border-slate-200 pt-5">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 text-sm font-semibold text-primary-700 no-underline hover:text-primary-600">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5"></path>
                                <path d="m12 19-7-7 7-7"></path>
                            </svg>
                            Back to login
                        </a>
                        <p class="mt-3 text-sm leading-6 text-slate-400">
                            Having trouble?
                            <a href="{{ route('contact') }}" class="{{ $linkClass }}">Contact support</a>
                        </p>
                    </div>
                @endif

                <div class="mt-6 border-t border-slate-200 pt-4 text-center text-xs leading-6 text-slate-400">
                    &copy; 2026 Biogenix Laboratories. Precision diagnostics, consistent delivery.
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const forgotForm = document.getElementById('forgotPasswordForm');
        const forgotSubmitBtn = document.getElementById('forgotSubmitBtn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || forgotForm?.querySelector('input[name="_token"]')?.value
            || '';

        if (!forgotForm || !forgotSubmitBtn) {
            return;
        }

        forgotForm.addEventListener('submit', async function (event) {
            const fieldGroup = forgotForm.querySelector('[data-field-group]');
            const fieldError = forgotForm.querySelector('[data-field-error]');
            const emailInput = document.getElementById('forgotEmail');

            event.preventDefault();
            forgotSubmitBtn.disabled = true;
            forgotSubmitBtn.classList.add('cursor-not-allowed', 'opacity-70');
            forgotSubmitBtn.setAttribute('aria-disabled', 'true');

            if (fieldError) {
                fieldError.textContent = '';
                fieldError.classList.add('hidden');
            }

            if (fieldGroup) {
                fieldGroup.classList.remove('text-rose-600');
            }

            if (emailInput) {
                emailInput.classList.remove('border-rose-400', 'focus:border-rose-500', 'focus:ring-rose-500/20');
            }

            const formData = new FormData(forgotForm);

            try {
                const response = await fetch(forgotForm.action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: formData,
                });

                if (response.ok) {
                    window.location.href = forgotForm.dataset.successUrl || '{{ route('forgot.password', ['sent' => 1]) }}';
                    return;
                }

                let responseBody = {};
                try {
                    responseBody = await response.json();
                } catch (jsonError) {
                    responseBody = {};
                }

                const emailError = responseBody?.errors?.email?.[0] || responseBody?.message || 'Unable to send reset link right now. Please try again.';

                if (fieldError) {
                    fieldError.textContent = emailError;
                    fieldError.classList.remove('hidden');
                }

                if (fieldGroup) {
                    fieldGroup.classList.add('text-rose-600');
                }

                if (emailInput) {
                    emailInput.classList.add('border-rose-400', 'focus:border-rose-500', 'focus:ring-rose-500/20');
                }
            } catch (error) {
                if (fieldError) {
                    fieldError.textContent = 'Unable to send reset link right now. Please try again.';
                    fieldError.classList.remove('hidden');
                }

                if (fieldGroup) {
                    fieldGroup.classList.add('text-rose-600');
                }

                if (emailInput) {
                    emailInput.classList.add('border-rose-400', 'focus:border-rose-500', 'focus:ring-rose-500/20');
                }
            } finally {
                forgotSubmitBtn.disabled = false;
                forgotSubmitBtn.classList.remove('cursor-not-allowed', 'opacity-70');
                forgotSubmitBtn.removeAttribute('aria-disabled');
            }
        });
    });
</script>
@endpush
