@php
    $panelClass = 'rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] md:p-7';
    $inputClass = 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary-600 focus:ring-1 focus:ring-primary-600';
    $labelClass = 'text-[13px] font-semibold text-slate-700';
    $profileUser = $profileUser ?? auth()->user();
    $profileAddress = $profileAddress ?? null;
    $profileSummary = $profileSummary ?? ['orders_count' => 0, 'tickets_count' => 0, 'status_label' => 'Unknown'];
    $passwordLastChangedLabel = $passwordLastChangedLabel ?? 'Last changed date not available';
    $isVerified = ! empty($profileUser?->email_verified_at) || ($profileUser && $profileUser->status === 'active');
@endphp

{{-- Avatar & Quick Stats --}}
<div class="{{ $panelClass }}">
    <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-5">
            <div class="relative">
                <div class="h-20 w-20 overflow-hidden rounded-2xl border-2 border-primary-100 bg-primary-50 shadow-md">
                    <img src="{{ asset('upload/icons/logo.jpg') }}" alt="Profile avatar" class="h-full w-full object-cover">
                </div>
                <button type="button" class="absolute -bottom-1 -right-1 flex h-8 w-8 items-center justify-center rounded-xl border-2 border-white bg-primary-600 text-white shadow-sm transition hover:bg-primary-700">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                </button>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">{{ $profileUser?->name ?? 'Customer Profile' }}</h3>
                <p class="mt-0.5 text-sm text-slate-500">{{ $profileUser?->email ?? 'No email available' }}</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full {{ $isVerified ? 'bg-primary-50 text-primary-600' : 'bg-secondary-50 text-secondary-700' }} px-2.5 py-1 text-xs font-semibold">
                        <span class="h-1.5 w-1.5 rounded-full {{ $isVerified ? 'bg-primary-600' : 'bg-secondary-600' }}"></span>
                        {{ $isVerified ? 'Verified' : 'Verification Pending' }}
                    </span>
                    <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-700">B2C Customer</span>
                </div>
            </div>
        </div>
        <div class="flex divide-x divide-slate-200 rounded-2xl border border-slate-200 bg-slate-50">
            <div class="px-5 py-3 text-center">
                <p class="text-lg font-bold text-slate-900">{{ $profileSummary['orders_count'] }}</p>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Orders</p>
            </div>
            <div class="px-5 py-3 text-center">
                <p class="text-lg font-bold text-slate-900">{{ $profileSummary['tickets_count'] }}</p>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Tickets</p>
            </div>
            <div class="px-5 py-3 text-center">
                <p class="text-lg font-bold {{ $profileSummary['status_label'] === 'Active' ? 'text-primary-600' : 'text-secondary-700' }}">{{ $profileSummary['status_label'] }}</p>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Status</p>
            </div>
        </div>
    </div>
</div>

{{-- Personal Information --}}
<div class="{{ $panelClass }}">
    <div class="flex items-center gap-3 border-b border-slate-100 pb-5">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-900">Personal Information</h3>
            <p class="text-sm text-slate-500">Keep your contact information up to date.</p>
        </div>
    </div>

    <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div class="space-y-2">
            <label class="{{ $labelClass }}">Full Name</label>
            <input name="name" class="{{ $inputClass }}" value="{{ old('name', $profileUser?->name) }}" placeholder="Full name">
        </div>
        <div class="space-y-2">
            <label class="{{ $labelClass }}">Mobile Number</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center rounded-l-xl border-r border-slate-200 bg-slate-100 px-3 text-sm font-medium text-slate-500">+91</span>
                <input name="phone" class="{{ $inputClass }} pl-16" value="{{ old('phone', $profileUser?->phone) }}" placeholder="Mobile number">
            </div>
        </div>
        <div class="space-y-2 md:col-span-2">
            <label class="{{ $labelClass }}">Email Address</label>
            <div class="relative">
                <svg class="absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input name="email" id="profile_email_input" class="{{ $inputClass }} pl-11 bg-slate-50 cursor-not-allowed" value="{{ old('email', $profileUser?->email) }}" placeholder="Email" readonly>
                <button type="button" onclick="toggleModal('changeEmailModal', true)" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-white px-3 py-1.5 text-[11px] font-bold text-primary-800 shadow-sm border border-slate-200 hover:bg-slate-50 transition">Change</button>
            </div>
            <p class="text-xs text-slate-400">This email will be used for account notifications and security updates.</p>
        </div>
    </div>
</div>

{{-- Verification Required banner --}}
<div class="rounded-2xl border border-primary-100 bg-gradient-to-r from-primary-50 to-primary-50/30 p-5">
    <div class="flex items-start gap-3">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-100 text-primary-600">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <p class="font-semibold text-primary-800">Verification Required</p>
            <p class="mt-1 text-sm leading-6 text-slate-600">Changing your email will require re-verification. We'll send a OTP to your new address.</p>
        </div>
    </div>
</div>

{{-- Address Information --}}
<div class="{{ $panelClass }}">
    <div class="flex items-center justify-between border-b border-slate-100 pb-5">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Address Information</h3>
                <p class="text-sm text-slate-500">Your primary shipping and delivery address.</p>
            </div>
        </div>
        <a href="{{ route('customer.addresses.preview') }}" class="text-[13px] font-bold text-primary-600 hover:text-primary-700 transition no-underline">Manage Addresses</a>
    </div>

    <div class="mt-6 grid gap-5 md:grid-cols-2">
        <div class="space-y-2 md:col-span-2">
            <label class="{{ $labelClass }}">Street Address</label>
            <input name="address_line1" class="{{ $inputClass }}" value="{{ old('address_line1', $profileAddress?->line1) }}" placeholder="Street address">
        </div>
        <div class="space-y-2">
            <label class="{{ $labelClass }}">City</label>
            <input name="city" class="{{ $inputClass }}" value="{{ old('city', $profileAddress?->city) }}" placeholder="City">
        </div>
        <div class="space-y-2">
            <label class="{{ $labelClass }}">State / Province</label>
            <input name="state" class="{{ $inputClass }}" value="{{ old('state', $profileAddress?->state) }}" placeholder="State">
        </div>
        <div class="space-y-2">
            <label class="{{ $labelClass }}">Postal Code</label>
            <input name="postal_code" class="{{ $inputClass }}" value="{{ old('postal_code', $profileAddress?->postal_code) }}" placeholder="Postal code">
        </div>
        <div class="space-y-2">
            <label class="{{ $labelClass }}">Country</label>
            <input name="country" class="{{ $inputClass }}" value="{{ old('country', $profileAddress?->country) }}" placeholder="Country">
        </div>
    </div>
</div>

{{-- Security section --}}
<div class="{{ $panelClass }}">
    <div class="flex items-center gap-3 border-b border-slate-100 pb-5">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-secondary-50 text-secondary-700">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-slate-900">Security & Password</h3>
            <p class="text-sm text-slate-500">Manage your account security settings.</p>
        </div>
    </div>

    <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-700">Password</p>
            <p class="mt-1 text-sm text-slate-500">{{ $passwordLastChangedLabel }}</p>
        </div>
        <button type="button" onclick="toggleModal('changePasswordModal', true)" class="inline-flex h-11 shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            Change Password
        </button>
    </div>


</div>
