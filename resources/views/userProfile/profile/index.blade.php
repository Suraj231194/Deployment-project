@extends('layouts.customer')

@php
    $portal = auth()->user()?->user_type ?? request('user_type', request('portal', 'b2c'));
    $portal = $portal === 'b2b' ? 'b2b' : 'b2c';
@endphp

@section('title', 'My Profile')
@section('customer_active', 'profile')
@section('customer_minimal', 'minimal')

@section('customer_content')
    <x-account.workspace
        :portal="$portal"
        active="profile"
        :title="$portal === 'b2b' ? 'Business Profile' : 'Personal Profile'"
        :description="$portal === 'b2b'
            ? 'Update your company details and contact information for the Biogenix platform.'
            : 'Update your personal details and contact preferences.'"
    >
        <form method="POST" action="{{ route('customer.profile.update') }}" class="space-y-6">
            @csrf
            @include('userProfile.profile.'.$portal.'.profile-form')

            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('customer.profile.preview') }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-[13px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 focus-visible:outline-none no-underline">Cancel</a>
                <button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700 focus-visible:outline-none cursor-pointer">Save Changes</button>
            </div>
        </form>

        {{-- Change Password Modal --}}
        <x-modal
            id="changePasswordModal"
            title="Change Password"
            :open="session('open_modal') === 'changePasswordModal' || $errors->getBag('updatePassword')->any()"
        >
            <form id="changePasswordForm" action="{{ route('customer.profile.password.update') }}" method="POST" class="space-y-4">
                @csrf
                @if (session('open_modal') === 'changePasswordModal' && session('error'))
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="space-y-2">
                    <label for="current_password" class="text-[13px] font-semibold text-slate-700">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary-600 focus:ring-1 focus:ring-primary-600 @error('current_password', 'updatePassword') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror" placeholder="Enter current password" required>
                    @error('current_password', 'updatePassword')
                        <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label for="password" class="text-[13px] font-semibold text-slate-700">New Password</label>
                    <input type="password" id="password" name="password" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary-600 focus:ring-1 focus:ring-primary-600 @error('password', 'updatePassword') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror" placeholder="Enter new password (min. 8 characters)" required>
                    @error('password', 'updatePassword')
                        <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label for="password_confirmation" class="text-[13px] font-semibold text-slate-700">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary-600 focus:ring-1 focus:ring-primary-600 @error('password_confirmation', 'updatePassword') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror" placeholder="Confirm new password" required>
                    @error('password_confirmation', 'updatePassword')
                        <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </form>

            <x-slot:footer>
                <button type="button" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-[13px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50" onclick="toggleModal('changePasswordModal', false)">Cancel</button>
                <button type="submit" form="changePasswordForm" class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700">Update Password</button>
            </x-slot:footer>
        </x-modal>


        {{-- Change Email Modal --}}
        <x-modal id="changeEmailModal" title="Change Email">
            {{-- Step 1: Input New Email --}}
            <div id="email-step-1" class="space-y-4">
                <div class="space-y-2">
                    <label for="new_email" class="text-[13px] font-semibold text-slate-700">New Email Address</label>
                    <input type="email" id="new_email" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary-600 focus:ring-1 focus:ring-primary-600" placeholder="Enter new email address">
                    <p id="email-error" class="hidden text-xs text-red-500"></p>
                </div>
            </div>

            {{-- Step 2: Input OTP --}}
            <div id="email-step-2" class="hidden space-y-4">
                <p class="text-[13px] text-slate-600">We've sent a 6-digit OTP to <span id="display-new-email" class="font-semibold text-slate-900"></span>. Please enter it below to verify.</p>
                <div class="space-y-2">
                    <label for="email_otp" class="text-[13px] font-semibold text-slate-700">Enter OTP</label>
                    <input type="text" id="email_otp" maxlength="6" class="h-11 w-full text-center tracking-[0.5em] rounded-xl border border-slate-200 bg-white px-4 text-xl font-bold text-slate-900 outline-none transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600" placeholder="000000">
                    <p id="otp-error" class="hidden text-xs text-red-500"></p>
                </div>
                <div class="flex items-center justify-between">
                    <button type="button" id="btn-resend-otp" class="text-[13px] font-semibold text-primary-800 hover:underline disabled:text-slate-400">Resend OTP</button>
                    <span id="resend-timer" class="text-[12px] text-slate-500"></span>
                </div>
            </div>

            {{-- Step 3: Success & Final Update --}}
            <div id="email-step-3" class="hidden space-y-4">
                <div class="rounded-xl bg-primary-50 p-4 text-center border border-primary-200">
                    <div class="flex justify-center">
                        <svg class="h-12 w-12 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-2 text-sm font-bold text-primary-600">Email Verified!</h3>
                    <p class="mt-1 text-xs text-primary-600">Your new email has been verified. Click 'Submit' to update your profile.</p>
                </div>
            </div>

            <x-slot:footer>
                <div id="email-footer-step-1" class="flex w-full items-center justify-end gap-3">
                    <button type="button" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-[13px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50" onclick="toggleModal('changeEmailModal', false)">Cancel</button>
                    <button type="button" id="btn-get-otp" class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700 disabled:opacity-50">Get OTP</button>
                </div>
                <div id="email-footer-step-2" class="hidden w-full items-center justify-end gap-3">
                    <button type="button" id="btn-back-to-email" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-[13px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">Back</button>
                    <button type="button" id="btn-verify-otp" class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700 disabled:opacity-50">Verify OTP</button>
                </div>
                <div id="email-footer-step-3" class="hidden w-full items-center justify-end gap-3">
                    <button type="button" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-[13px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50" data-modal-close="changeEmailModal">Cancel</button>
                    <button type="button" id="btn-final-email-submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700">Submit Change</button>
                </div>
            </x-slot:footer>
        </x-modal>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const step1 = document.getElementById('email-step-1');
                const step2 = document.getElementById('email-step-2');
                const step3 = document.getElementById('email-step-3');
                
                const newEmailInput = document.getElementById('new_email');
                const otpInput = document.getElementById('email_otp');
                const displayEmail = document.getElementById('display-new-email');
                const emailError = document.getElementById('email-error');
                const otpError = document.getElementById('otp-error');
                
                const btnGetOtp = document.getElementById('btn-get-otp');
                const btnVerifyOtp = document.getElementById('btn-verify-otp');
                const btnResendOtp = document.getElementById('btn-resend-otp');
                const btnBack = document.getElementById('btn-back-to-email');
                const btnFinalSubmit = document.getElementById('btn-final-email-submit');
                const resendTimer = document.getElementById('resend-timer');

                let cooldown = 0;
                let timerInterval;

                function showStep(step) {
                    step1.classList.add('hidden');
                    step2.classList.add('hidden');
                    step3.classList.add('hidden');
                    
                    const f1 = document.getElementById('email-footer-step-1');
                    const f2 = document.getElementById('email-footer-step-2');
                    const f3 = document.getElementById('email-footer-step-3');
                    if (f1) f1.classList.add('hidden');
                    if (f2) f2.classList.add('hidden');
                    if (f3) f3.classList.add('hidden');

                    if (step === 1) {
                        step1.classList.remove('hidden');
                        if (f1) f1.classList.remove('hidden');
                    }
                    if (step === 2) {
                        step2.classList.remove('hidden');
                        if (f2) f2.classList.remove('hidden');
                    }
                    if (step === 3) {
                        step3.classList.remove('hidden');
                        if (f3) f3.classList.remove('hidden');
                    }
                }

                function resetEmailModal() {
                    newEmailInput.value = '';
                    otpInput.value = '';
                    emailError.classList.add('hidden');
                    otpError.classList.add('hidden');
                    displayEmail.textContent = '';
                    showStep(1);
                    if (timerInterval) clearInterval(timerInterval);
                    resendTimer.textContent = '';
                }

                function resetPasswordModal() {
                    const currentPwd = document.getElementById('current_password');
                    const newPwd = document.getElementById('password');
                    const confirmPwd = document.getElementById('password_confirmation');
                    if (currentPwd) currentPwd.value = '';
                    if (newPwd) newPwd.value = '';
                    if (confirmPwd) confirmPwd.value = '';
                }

                // Reset modals when opened
                document.addEventListener('click', function(e) {
                    if (e.target.closest('[data-open-modal="changeEmailModal"]')) {
                        resetEmailModal();
                    }
                    if (e.target.closest('[data-open-modal="changePasswordModal"]')) {
                        resetPasswordModal();
                    }
                });

                function startTimer(seconds) {
                    cooldown = seconds;
                    btnResendOtp.disabled = true;
                    clearInterval(timerInterval);
                    timerInterval = setInterval(() => {
                        if (cooldown <= 0) {
                            clearInterval(timerInterval);
                            btnResendOtp.disabled = false;
                            resendTimer.textContent = '';
                            return;
                        }
                        resendTimer.textContent = `Resend in ${cooldown}s`;
                        cooldown--;
                    }, 1000);
                }

                btnGetOtp.addEventListener('click', async function() {
                    const email = newEmailInput.value;
                    if (!email || !email.includes('@')) {
                        emailError.textContent = "Please enter a valid email address.";
                        emailError.classList.remove('hidden');
                        return;
                    }

                    btnGetOtp.disabled = true;
                    emailError.classList.add('hidden');

                    try {
                        const response = await fetch("{{ route('signup.email-otp.send') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ email })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            displayEmail.textContent = email;
                            showStep(2);
                            startTimer(data.resend_available_in_seconds || 60);
                        } else {
                            emailError.textContent = data.message || "Failed to send OTP.";
                            emailError.classList.remove('hidden');
                        }
                    } catch (error) {
                        emailError.textContent = "Error connecting to server.";
                        emailError.classList.remove('hidden');
                    } finally {
                        btnGetOtp.disabled = false;
                    }
                });

                btnVerifyOtp.addEventListener('click', async function() {
                    const otp = otpInput.value;
                    const email = newEmailInput.value;
                    if (otp.length !== 6) return;

                    btnVerifyOtp.disabled = true;
                    otpError.classList.add('hidden');

                    try {
                        const response = await fetch("{{ route('signup.email-otp.verify') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ email, otp })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            showStep(3);
                        } else {
                            otpError.textContent = data.message || "Invalid OTP.";
                            otpError.classList.remove('hidden');
                        }
                    } catch (error) {
                        otpError.textContent = "Verification failed.";
                        otpError.classList.remove('hidden');
                    } finally {
                        btnVerifyOtp.disabled = false;
                    }
                });

                btnFinalSubmit.addEventListener('click', function() {
                    const profileEmailInput = document.getElementById('profile_email_input');
                    if (profileEmailInput) {
                        profileEmailInput.value = newEmailInput.value;
                        // Trigger the main form submit
                        const mainForm = profileEmailInput.closest('form');
                        if (mainForm) mainForm.submit();
                    }
                });

                btnResendOtp.addEventListener('click', () => btnGetOtp.click());
                btnBack.addEventListener('click', () => showStep(1));
            });

        </script>
        </script>

    </x-account.workspace>
@endsection
