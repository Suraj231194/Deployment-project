@php
    $successMessage = session('success') ?: session('status');
    $errorMessage = session('error') ?: ($errors->first('email') ?: $errors->first('password'));
    $kickerClass = 'inline-flex items-center rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-primary-700';
    $fieldClass = 'grid gap-2';
    $labelClass = 'text-sm font-semibold text-slate-700';
    $inputBaseClass = 'h-14 w-full rounded-2xl border border-slate-300 bg-white px-4 text-base font-medium text-slate-900 transition placeholder:text-slate-400 focus:border-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-600/10';
    $inputWithIconClass = $inputBaseClass . ' px-12';
    $linkClass = 'font-semibold text-primary-700 no-underline transition hover:text-primary-600';
    $submitClass = 'inline-flex h-14 w-full items-center justify-center rounded-2xl bg-primary-600 text-base font-semibold text-white shadow-[0_16px_35px_-18px_rgba(26,77,46,0.35)] transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-70';
@endphp

@extends('layouts.app')

@section('title', 'Login - Biogenix')

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
                        Sign in to manage catalog access, quotations, and procurement workflows from one consistent portal.
                    </p>
                </div>
            </aside>

            <div class="flex items-center bg-[linear-gradient(180deg,rgba(255,255,255,1)_0%,rgba(252,253,255,1)_100%)] px-5 py-8 sm:px-7 lg:px-10">
                <div class="mx-auto w-full max-w-md">
                    <div>
                        <p class="{{ $kickerClass }}">Secure Access</p>
                        <h1 class="mt-3 text-3xl font-bold leading-tight tracking-tight text-slate-950 md:text-4xl">Sign in to your account</h1>
                        <p class="mt-4 text-sm leading-7 text-slate-500 md:text-base">
                            Enter your credentials to continue with product discovery, order review, and account-specific pricing.
                        </p>
                    </div>

                    @if ($errorMessage)
                        <div class="mt-8 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm font-medium leading-6 text-rose-700" role="alert">
                            <svg class="mt-0.5 h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="9"></circle>
                                <path d="M12 8v5"></path>
                                <path d="M12 16h.01"></path>
                            </svg>
                            <span>{{ $errorMessage }}</span>
                        </div>
                    @elseif ($successMessage)
                        <div class="mt-8 flex items-start gap-3 rounded-2xl border border-primary-200 bg-primary-50 px-4 py-4 text-sm font-medium leading-6 text-primary-600" role="status">
                            <svg class="mt-0.5 h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ $successMessage }}</span>
                        </div>
                    @endif

                    <form id="loginForm" method="POST" action="{{ route('login') }}" class="mt-8 grid gap-5" novalidate>
                        @csrf

                        <div class="{{ $fieldClass }}" data-field-group>
                            <label for="loginEmail" class="{{ $labelClass }}">Email Address</label>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <input
                                    type="email"
                                    name="email"
                                    id="loginEmail"
                                    class="{{ $inputWithIconClass }} @error('email') border-rose-300 focus:border-rose-400 focus:ring-rose-200 @enderror"
                                    placeholder="researcher@biogenix.com"
                                    value="{{ old('email') }}"
                                    autocomplete="email"
                                    required
                                >
                            </div>
                            @error('email')
                                <p class="text-sm font-medium text-rose-600">{{ $message }}</p>
                            @else
                                <p data-field-error class="hidden text-sm font-medium text-rose-600"></p>
                            @enderror
                        </div>

                        <div class="{{ $fieldClass }}" data-field-group>
                            <div class="flex items-center justify-between gap-3">
                                <label for="loginPassword" class="{{ $labelClass }}">Password</label>
                                <a href="{{ route('forgot.password') }}" class="{{ $linkClass }} text-sm">Forgot Password?</a>
                            </div>
                            <div class="relative">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                                    <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
                                </svg>
                                <input
                                    type="password"
                                    name="password"
                                    id="loginPassword"
                                    class="{{ $inputWithIconClass }} pr-12 @error('password') border-rose-300 focus:border-rose-400 focus:ring-rose-200 @enderror"
                                    placeholder="Enter your password"
                                    autocomplete="current-password"
                                    required
                                >
                                <button type="button" id="togglePassword" class="absolute right-3 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Show password" aria-pressed="false">
                                    <svg data-password-hidden-icon class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg data-password-visible-icon class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m3 3 18 18"></path>
                                        <path d="M10.6 10.7a3 3 0 0 0 4.2 4.2"></path>
                                        <path d="M9.9 5.2A10.5 10.5 0 0 1 12 5c6 0 9.5 7 9.5 7a18.8 18.8 0 0 1-3.2 3.9"></path>
                                        <path d="M6.2 6.2C3.9 7.9 2.5 12 2.5 12s3.5 7 9.5 7c1.7 0 3.2-.4 4.6-1.1"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-sm font-medium text-rose-600">{{ $message }}</p>
                            @else
                                <p data-field-error class="hidden text-sm font-medium text-rose-600"></p>
                            @enderror
                        </div>

                        <label for="rememberCheck" class="inline-flex items-center gap-3 text-sm text-slate-600">
                            <input
                                type="checkbox"
                                name="remember"
                                id="rememberCheck"
                                value="1"
                                class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-600"
                                @checked(old('remember'))
                            >
                            <span>Keep me signed in</span>
                        </label>

                        <button type="submit" id="loginSubmitBtn" class="{{ $submitClass }}">
                            Sign In
                        </button>
                    </form>

                    <div class="mt-8 border-t border-slate-200 pt-5">
                        <p class="text-center text-sm leading-7 text-slate-500">
                            Need assistance?
                            <a href="{{ route('contact') }}" class="{{ $linkClass }}">Contact Support</a>
                        </p>

                        <p class="mt-3 text-center text-sm leading-7 text-slate-500">
                            New here?
                            <a href="{{ route('signup') }}" class="{{ $linkClass }}">Create an account</a>
                        </p>

                        <div class="mt-3 flex flex-wrap items-center justify-between gap-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                            <span>&copy; 2026 Biogenix Corp.</span>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('privacy') }}" class="text-inherit no-underline">Privacy</a>
                                <a href="{{ route('terms') }}" class="text-inherit no-underline">Terms</a>
                                <a href="{{ route('contact') }}" class="text-inherit no-underline">Security</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof setupPasswordToggle === 'function') {
            setupPasswordToggle('loginPassword', 'togglePassword');
        }

        const loginForm = document.getElementById('loginForm');
        const loginSubmitBtn = document.getElementById('loginSubmitBtn');

        if (loginForm && loginSubmitBtn) {
            loginForm.addEventListener('submit', function () {
                loginSubmitBtn.disabled = true;
                loginSubmitBtn.classList.add('cursor-not-allowed', 'opacity-70');
                loginSubmitBtn.setAttribute('aria-disabled', 'true');
            });
        }
    });
</script>
@endpush
