@extends('layouts.customer')

@php
    $portal = auth()->user()?->user_type ?? request('user_type', request('portal', 'b2c'));
    $portal = $portal === 'b2b' ? 'b2b' : 'b2c';
    $countryOptions = ['India', 'United States', 'United Kingdom'];
@endphp

@section('title', 'Addresses')
@section('customer_active', 'addresses')
@section('customer_minimal', 'minimal')

@section('customer_content')
    <x-account.workspace
        :portal="$portal"
        active="addresses"
        title="Addresses"
        description="Manage your saved delivery and billing addresses."
    >
        <x-slot:headerActions>
            <button
                type="button"
                onclick="toggleModal('addAddressModal', true)"
                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700 focus-visible:outline-none cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add Address
            </button>
        </x-slot:headerActions>
        @include('userProfile.addresses.'.$portal.'.addresses-form')

        <x-modal
            id="addAddressModal"
            title="Add New Address"
            :open="session('open_modal') === 'addAddressModal' || $errors->getBag('addressCreate')->any()"
        >
            <form id="addAddressForm" action="{{ route('customer.addresses.store') }}" method="POST" class="space-y-4">
                @csrf

                @if (session('open_modal') === 'addAddressModal' && session('error'))
                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="space-y-2">
                    <label for="add_line1" class="text-[13px] font-semibold text-slate-700">Address Line 1</label>
                    <input id="add_line1" name="line1" value="{{ old('line1') }}" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600 @error('line1', 'addressCreate') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror" placeholder="Enter building, street name" required>
                    @error('line1', 'addressCreate')
                        <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="add_line2" class="text-[13px] font-semibold text-slate-700">Address Line 2 (Optional)</label>
                    <input id="add_line2" name="line2" value="{{ old('line2') }}" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600 @error('line2', 'addressCreate') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror" placeholder="Apartment, suite, landmark">
                    @error('line2', 'addressCreate')
                        <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label for="add_city" class="text-[13px] font-semibold text-slate-700">City</label>
                        <input id="add_city" name="city" value="{{ old('city') }}" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600 @error('city', 'addressCreate') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror" placeholder="City" required>
                        @error('city', 'addressCreate')
                            <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="add_state" class="text-[13px] font-semibold text-slate-700">State</label>
                        <input id="add_state" name="state" value="{{ old('state') }}" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600 @error('state', 'addressCreate') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror" placeholder="State" required>
                        @error('state', 'addressCreate')
                            <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="add_postal_code" class="text-[13px] font-semibold text-slate-700">Postal Code</label>
                        <input id="add_postal_code" name="postal_code" value="{{ old('postal_code') }}" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600 @error('postal_code', 'addressCreate') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror" placeholder="Postal code" required>
                        @error('postal_code', 'addressCreate')
                            <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="add_country" class="text-[13px] font-semibold text-slate-700">Country</label>
                        <select id="add_country" name="country" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition focus:border-primary-600 focus:ring-1 focus:ring-primary-600 @error('country', 'addressCreate') border-rose-300 focus:border-rose-400 focus:ring-rose-100 @enderror" required>
                            @foreach ($countryOptions as $countryOption)
                                <option value="{{ $countryOption }}" @selected(old('country', 'India') === $countryOption)>{{ $countryOption }}</option>
                            @endforeach
                        </select>
                        @error('country', 'addressCreate')
                            <p class="text-xs font-medium text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-1">
                    <label class="flex items-center gap-3 text-sm font-semibold text-slate-800 cursor-pointer">
                        <input type="checkbox" name="is_default_shipping" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-800 focus:ring-primary-600" @checked(old('is_default_shipping'))>
                        Set as Default Shipping Address
                    </label>
                    <label class="flex items-center gap-3 text-sm font-semibold text-slate-800 cursor-pointer">
                        <input type="checkbox" name="is_default_billing" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-800 focus:ring-primary-600" @checked(old('is_default_billing'))>
                        Set as Default Billing Address
                    </label>
                </div>
            </form>

            <x-slot:footer>
                <button type="button" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-[13px] font-bold text-slate-700 shadow-sm transition hover:bg-slate-50" data-modal-close="addAddressModal">Cancel</button>
                <button type="submit" form="addAddressForm" class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700">Save Address</button>
            </x-slot:footer>
        </x-modal>

        <script>
        </script>
        </script>
    </x-account.workspace>
@endsection

