@php
    $stepOneFieldNames = ['first_name', 'last_name', 'email', 'phone', 'password', 'password_confirmation'];
    $stepTwoFieldNames = ['address_1', 'address_2', 'city', 'pincode', 'state'];
    $hasStepOneErrors = $errors->hasAny($stepOneFieldNames);
    $hasStepTwoErrors = $errors->hasAny($stepTwoFieldNames);
    $hasStepTwoOldInput = filled(old('address_1')) || filled(old('city')) || filled(old('pincode')) || filled(old('state'));
    $initialStep = $hasStepOneErrors ? 1 : (($hasStepTwoErrors || $hasStepTwoOldInput) ? 2 : 1);
    $currentLabel = $initialStep === 2 ? 'Address' : 'Personal Details';
    $labelClass = 'text-sm font-semibold text-slate-700';
    $inputClass = 'h-12 w-full rounded-2xl border border-slate-300 bg-white px-4 text-sm font-medium text-slate-900 transition placeholder:text-slate-400 focus:border-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-600/10';
    $buttonPrimaryClass = 'inline-flex h-12 items-center justify-center rounded-2xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-[0_16px_35px_-18px_rgba(37,99,235,0.7)] transition hover:bg-primary-700 disabled:cursor-not-allowed disabled:opacity-70';
    $buttonSecondaryClass = 'inline-flex h-12 items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50';
    $compactInputClass = 'h-9 w-full rounded-lg border border-slate-300 bg-white px-3 text-xs font-medium text-slate-900 transition placeholder:text-slate-400 focus:border-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-600/10';
    $compactButtonClass = 'inline-flex h-9 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-[11px] font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50';
    $fieldErrorClass = 'border-rose-300 focus:border-rose-400 focus:ring-rose-200';
    $normalizedOldEmail = strtolower(trim((string) old('email', '')));
    $isOldEmailVerified = $normalizedOldEmail !== '' && app(\App\Services\Authorization\SignupEmailOtpService::class)->hasVerifiedSignupEmail($normalizedOldEmail);
@endphp

@extends('layouts.app')

@section('title', 'Sign Up - Biogenix')

@section('content')
<div class="relative overflow-hidden px-4 py-10 sm:px-6 lg:px-8">
    <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-[24rem] bg-[radial-gradient(circle_at_top_left,rgba(37,99,235,0.18),transparent_38%),radial-gradient(circle_at_top_right,rgba(14,165,233,0.12),transparent_30%)]"></div>
    <div class="pointer-events-none absolute left-1/2 top-24 -z-10 h-72 w-72 -translate-x-[120%] rounded-full bg-primary-100/70 blur-3xl"></div>
    <div class="pointer-events-none absolute right-0 top-10 -z-10 h-80 w-80 translate-x-1/3 rounded-full bg-sky-100/70 blur-3xl"></div>

    <section class="mx-auto w-full max-w-2xl">
        <div class="overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_28px_80px_-36px_rgba(15,23,42,0.35)]">
            <form
                id="signupForm"
                method="POST"
                action="{{ route('register') }}"
                class="grid gap-8 px-5 py-7 sm:px-8 sm:py-9"
                data-disable-live-validation="true"
                data-email-otp-send-url="{{ route('signup.email-otp.send') }}"
                data-email-otp-verify-url="{{ route('signup.email-otp.verify') }}"
                data-email-otp-initial-verified="{{ $isOldEmailVerified ? 'true' : 'false' }}"
                data-email-otp-initial-email="{{ $isOldEmailVerified ? $normalizedOldEmail : '' }}"
                novalidate
            >
                @csrf
                <input type="hidden" name="user_type" value="b2c">
                <input type="hidden" name="country" value="India">

                <div class="space-y-4 text-center">
                    <div class="space-y-3">
                        <h1 class="text-3xl font-bold tracking-tight text-slate-950 sm:text-4xl">Sign Up</h1>
                        <p class="text-sm text-slate-600">
                            Are you a business owner or a healthcare professional?
                            <a href="{{ route('b2b.signup') }}" class="font-semibold text-primary-700 no-underline transition hover:text-primary-600">
                                </br>Register for a B2B Account
                            </a>
                        </p>
                    </div>
                </div>

                <div class="mb-4" aria-label="Signup progress">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-2.5">
                        <span class="text-[11px] font-bold uppercase tracking-[0.1em] text-slate-400">Step <span id="signupCurrentStep">{{ $initialStep }}</span> of 2</span>
                        <span id="signupCurrentLabel" class="text-[11px] font-bold uppercase tracking-[0.05em] text-primary-600">{{ $currentLabel }}</span>
                    </div>

                    <div id="signupProgressBar" class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden mb-4 relative" role="progressbar" aria-valuemin="1" aria-valuemax="2" aria-valuenow="{{ $initialStep }}">
                        <div id="signupProgressBarLine" class="absolute top-0 left-0 h-full bg-primary-600 rounded-full transition-all duration-300 ease-out" style="width: {{ $initialStep === 1 ? '50%' : '100%' }}"></div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        @foreach (['Personal Details', 'Address'] as $index => $stepLabel)
                            @php
                                $stepNumber = $index + 1;
                                $isCurrent = $stepNumber === $initialStep;
                                $isActive = $stepNumber <= $initialStep;
                            @endphp
                            <div
                                data-signup-step
                                data-step-index="{{ $stepNumber }}"
                                class="flex items-center gap-3 rounded-xl border px-3.5 py-2.5 transition-colors duration-200 {{ $isCurrent ? 'border-primary-600 bg-white shadow-sm ring-1 ring-primary-600' : 'border-slate-200 bg-slate-50' }}"
                            >
                                <span
                                    data-signup-step-circle
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[11px] font-bold transition-colors duration-200 {{ $isActive ? 'bg-primary-600 text-white border-transparent' : 'bg-transparent border border-slate-300 text-slate-500' }}"
                                >
                                    <span data-signup-step-number>{{ $stepNumber }}</span>
                                </span>
                                <span data-signup-step-label class="text-xs font-bold transition-colors duration-200 {{ $isActive ? 'text-primary-600' : 'text-slate-500' }}">
                                    {{ $stepLabel }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div data-signup-panel data-step="1" class="grid gap-4 @if($initialStep !== 1) hidden @endif">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div class="grid gap-2" data-field-group>
                            <label for="firstName" class="{{ $labelClass }}">First Name</label>
                            <input type="text" id="firstName" name="first_name" value="{{ old('first_name') }}" class="{{ $inputClass }} @error('first_name') {{ $fieldErrorClass }} @enderror" placeholder="Aarav">
                            <p data-field-error class="text-sm font-medium text-rose-600 @if(!$errors->has('first_name')) hidden @endif">{{ $errors->first('first_name') }}</p>
                        </div>

                        <div class="grid gap-2" data-field-group>
                            <label for="lastName" class="{{ $labelClass }}">Last Name</label>
                            <input type="text" id="lastName" name="last_name" value="{{ old('last_name') }}" class="{{ $inputClass }} @error('last_name') {{ $fieldErrorClass }} @enderror" placeholder="Sharma">
                            <p data-field-error class="text-sm font-medium text-rose-600 @if(!$errors->has('last_name')) hidden @endif">{{ $errors->first('last_name') }}</p>
                        </div>
                    </div>

                    <div class="grid gap-2" data-field-group>
                        <label for="signupEmail" class="{{ $labelClass }}">Email</label>
                        <div class="flex max-w-[23rem] flex-wrap items-center gap-2">
                            <div class="relative min-w-0 flex-1 basis-[14rem]">
                                <input type="email" id="signupEmail" name="email" value="{{ old('email') }}" class="{{ $inputClass }} pr-12 @error('email') {{ $fieldErrorClass }} @enderror" placeholder="you@example.com" autocomplete="email">
                                <span id="signupEmailVerifiedIcon" class="hidden absolute right-3 top-1/2 h-5 w-5 -translate-y-1/2 text-primary-600">
                                    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16Zm3.78-9.72a.75.75 0 10-1.06-1.06L9.25 10.69 7.78 9.22a.75.75 0 10-1.06 1.06l2 2a.75.75 0 001.06 0l4-4Z" clip-rule="evenodd" />
                                    </svg>
                                </span>
                            </div>
                            <button type="button" id="sendEmailOtpBtn" class="{{ $compactButtonClass }} shrink-0 whitespace-nowrap">
                                Get OTP
                            </button>
                        </div>
                        <p data-field-error class="text-sm font-medium text-rose-600 @if(!$errors->has('email')) hidden @endif">{{ $errors->first('email') }}</p>
                        <p id="signupEmailOtpStatus" class="hidden text-sm font-medium"></p>
                    </div>

                    <div id="signupEmailOtpBlock" class="hidden max-w-[18rem] rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                        <label for="signupEmailOtp" class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-600">Email OTP</label>
                        <div class="mt-2 flex flex-wrap items-center gap-2">
                            <input
                                type="text"
                                id="signupEmailOtp"
                                class="{{ $compactInputClass }} w-[9.5rem] shrink-0"
                                placeholder="Enter 6-digit OTP"
                                maxlength="6"
                                inputmode="numeric"
                                autocomplete="one-time-code"
                            >
                            <button type="button" id="verifyEmailOtpBtn" class="{{ $compactButtonClass }} shrink-0 whitespace-nowrap">
                                Verify OTP
                            </button>
                        </div>
                        <p class="mt-2 text-[11px] font-medium text-slate-500">Confirm the OTP to continue to the next step.</p>
                        <p id="signupEmailOtpError" class="mt-1 hidden text-sm font-medium text-rose-600"></p>
                    </div>

                    <div class="grid gap-2" data-field-group>
                        <label for="phone" class="{{ $labelClass }}">Phone Number</label>
                        <input type="text" id="phone" name="phone" maxlength="10" value="{{ old('phone') }}" class="{{ $inputClass }} @error('phone') {{ $fieldErrorClass }} @enderror" placeholder="10-digit mobile number" inputmode="numeric">
                        <p data-field-error class="text-sm font-medium text-rose-600 @if(!$errors->has('phone')) hidden @endif">{{ $errors->first('phone') }}</p>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <div class="grid gap-2" data-field-group>
                            <label for="signupPassword" class="{{ $labelClass }}">Password</label>
                            <div class="relative">
                                <input type="password" id="signupPassword" name="password" class="{{ $inputClass }} pr-12 @error('password') {{ $fieldErrorClass }} @enderror" placeholder="Minimum 8 characters" autocomplete="new-password">
                                <button type="button" id="toggleSignupPassword" class="absolute inset-y-0 right-0 inline-flex w-12 items-center justify-center text-slate-400 transition hover:text-slate-600" aria-label="Show password" aria-pressed="false">
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
                            <p data-field-error class="signup-field-error--tall text-sm font-medium text-rose-600 @if(!$errors->has('password')) hidden @endif">{{ $errors->first('password') }}</p>
                        </div>

                        <div class="grid gap-2" data-field-group>
                            <label for="confirmPassword" class="{{ $labelClass }}">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="confirmPassword" name="password_confirmation" class="{{ $inputClass }} pr-12 @error('password_confirmation') {{ $fieldErrorClass }} @enderror" placeholder="Repeat your password" autocomplete="new-password">
                                <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 inline-flex w-12 items-center justify-center text-slate-400 transition hover:text-slate-600" aria-label="Show password" aria-pressed="false">
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
                            <p data-field-error class="signup-field-error--tall text-sm font-medium text-rose-600 @if(!$errors->has('password_confirmation')) hidden @endif">{{ $errors->first('password_confirmation') }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" class="{{ $buttonPrimaryClass }} min-w-[8rem]" id="nextBtn" disabled aria-disabled="true">Next</button>
                    </div>
                </div>

                <div data-signup-panel data-step="2" class="grid gap-4 @if($initialStep !== 2) hidden @endif">
                    <div class="grid gap-2" data-field-group>
                        <label for="addressLine1" class="{{ $labelClass }}">Flat / House / Building</label>
                        <input type="text" id="addressLine1" name="address_1" value="{{ old('address_1') }}" class="{{ $inputClass }} @error('address_1') {{ $fieldErrorClass }} @enderror" placeholder="Flat number, house, or building name">
                        <p data-field-error class="text-sm font-medium text-rose-600 @if(!$errors->has('address_1')) hidden @endif">{{ $errors->first('address_1') }}</p>
                    </div>

                    <div class="grid gap-2" data-field-group>
                        <label for="addressLine2" class="{{ $labelClass }}">Area / Street / Sector</label>
                        <input type="text" id="addressLine2" name="address_2" value="{{ old('address_2') }}" class="{{ $inputClass }} @error('address_2') {{ $fieldErrorClass }} @enderror" placeholder="Street, area, sector, or landmark">
                        <p data-field-error class="text-sm font-medium text-rose-600 @if(!$errors->has('address_2')) hidden @endif">{{ $errors->first('address_2') }}</p>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <div class="grid gap-2" data-field-group>
                            <label for="city" class="{{ $labelClass }}">Town / City</label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}" class="{{ $inputClass }} @error('city') {{ $fieldErrorClass }} @enderror" placeholder="Mumbai">
                            <p data-field-error class="text-sm font-medium text-rose-600 @if(!$errors->has('city')) hidden @endif">{{ $errors->first('city') }}</p>
                        </div>

                        <div class="grid gap-2" data-field-group>
                            <label for="pincode" class="{{ $labelClass }}">Pincode</label>
                            <input type="text" id="pincode" name="pincode" value="{{ old('pincode') }}" class="{{ $inputClass }} @error('pincode') {{ $fieldErrorClass }} @enderror" placeholder="400001" inputmode="numeric">
                            <p data-field-error class="text-sm font-medium text-rose-600 @if(!$errors->has('pincode')) hidden @endif">{{ $errors->first('pincode') }}</p>
                        </div>
                    </div>

                    <div class="grid gap-3 md:grid-cols-2">
                        <div class="grid gap-2" data-field-group>
                            <label for="state" class="{{ $labelClass }}">State / UT</label>
                            <select id="state" name="state" data-old-value="{{ old('state') }}" class="{{ $inputClass }} @error('state') {{ $fieldErrorClass }} @enderror">
                                <option value="">Select State / UT</option>
                            </select>
                            <p data-field-error class="text-sm font-medium text-rose-600 @if(!$errors->has('state')) hidden @endif">{{ $errors->first('state') }}</p>
                        </div>

                        <div class="grid gap-2">
                            <label for="countryDisplay" class="{{ $labelClass }}">Country</label>
                            <input type="text" id="countryDisplay" value="India" disabled class="{{ $inputClass }} cursor-not-allowed bg-slate-100 text-slate-500">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <button type="button" class="{{ $buttonSecondaryClass }}" id="backBtn">Back</button>
                        <button type="submit" class="{{ $buttonPrimaryClass }}" id="signupSubmitBtn">Sign Up</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
    #signupForm [data-field-error] {
        min-height: 0.15rem;
        line-height: 1.15rem;
    }
    #signupForm .signup-field-error--tall {
        min-height: 3rem;
        line-height: 1.35rem;
    }
    #signupForm [data-field-error].hidden {
        display: block !important;
        visibility: hidden;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/signup.js') }}?v=20260321-1"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tab1 = document.querySelector('[data-step-index="1"]');
        const barLine = document.getElementById('signupProgressBarLine');
        const progressWrap = document.getElementById('signupProgressBar');
        
        if (progressWrap && barLine && tab1) {
            const updateStepStyles = () => {
                const currentStepStr = progressWrap.getAttribute('aria-valuenow');
                const currentStep = currentStepStr ? parseInt(currentStepStr) : 1;
                barLine.style.width = currentStep === 1 ? '50%' : '100%';
                
                document.querySelectorAll('[data-signup-step]').forEach((node, index) => {
                    const stepNumber = index + 1;
                    const isCurrent = stepNumber === currentStep;
                    const isActive = stepNumber <= currentStep;
                    
                    if (isCurrent) {
                        node.classList.add('border-primary-600', 'bg-white', 'ring-1', 'ring-primary-600');
                        node.classList.remove('border-primary-200', 'bg-primary-50/80', 'border-slate-200', 'bg-slate-50', 'shadow-sm');
                    } else {
                        node.classList.add('border-slate-200', 'bg-slate-50');
                        node.classList.remove('border-primary-600', 'ring-1', 'ring-primary-600', 'border-primary-200', 'bg-primary-50/80', 'bg-white', 'shadow-sm');
                    }
                    
                    const circle = node.querySelector('[data-signup-step-circle]');
                    if (circle) {
                        if (isActive) {
                            circle.classList.add('bg-primary-600', 'text-white', 'border-transparent');
                            circle.classList.remove('bg-transparent', 'border', 'border-slate-300', 'text-slate-500', 'text-slate-400', 'bg-white');
                        } else {
                            circle.classList.add('bg-transparent', 'border', 'border-slate-300', 'text-slate-500');
                            circle.classList.remove('bg-primary-600', 'text-white', 'border-transparent', 'text-slate-400', 'bg-white');
                        }
                    }
                    
                    const label = node.querySelector('[data-signup-step-label]');
                    if (label) {
                        if (isActive) {
                            label.classList.add('text-primary-600');
                            label.classList.remove('text-slate-500', 'text-slate-400', 'text-slate-900');
                        } else {
                            label.classList.add('text-slate-500');
                            label.classList.remove('text-primary-600', 'text-slate-400', 'text-slate-900');
                        }
                    }
                });
            };

            const observer = new MutationObserver(updateStepStyles);
            observer.observe(progressWrap, { attributes: true, attributeFilter: ['aria-valuenow'] });
            
            setTimeout(updateStepStyles, 50);

            document.querySelectorAll('[data-signup-step]').forEach((tab) => {
                tab.style.cursor = 'pointer';
                tab.addEventListener('click', () => {
                    const stepClicked = parseInt(tab.getAttribute('data-step-index'));
                    const currentStepStr = progressWrap.getAttribute('aria-valuenow');
                    const currentStep = currentStepStr ? parseInt(currentStepStr) : 1;
                    
                    if (stepClicked === currentStep) return;
                    
                    if (stepClicked === 2 && currentStep === 1) {
                        const nextBtn = document.getElementById('nextBtn');
                        if(nextBtn) nextBtn.click();
                    } else if (stepClicked === 1 && currentStep === 2) {
                        const backBtn = document.getElementById('backBtn');
                        if(backBtn) backBtn.click();
                    }
                });
            });
        }
    });
</script>
@endpush
