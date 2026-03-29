@php
    $panelClass = 'space-y-6 rounded-2xl border border-slate-100 bg-white p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] md:p-7';
    $inputClass = 'h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-medium text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-primary-600 focus:ring-1 focus:ring-primary-600';
    $savedAddresses = $savedAddresses ?? collect();
    $editingAddressId = (string) session('editing_address_id');
    $countryOptions = ['India', 'United States', 'United Kingdom'];
@endphp

@forelse ($savedAddresses as $address)
    @php
        $errorBagName = 'addressUpdate_'.$address->id;
        $addressErrors = $errors->getBag($errorBagName);
        $isEditingCurrentAddress = $editingAddressId === (string) $address->id;
        $line1Value = $isEditingCurrentAddress ? old('line1', $address->line1) : $address->line1;
        $line2Value = $isEditingCurrentAddress ? old('line2', $address->line2) : $address->line2;
        $cityValue = $isEditingCurrentAddress ? old('city', $address->city) : $address->city;
        $stateValue = $isEditingCurrentAddress ? old('state', $address->state) : $address->state;
        $postalCodeValue = $isEditingCurrentAddress ? old('postal_code', $address->postal_code) : $address->postal_code;
        $countryValue = $isEditingCurrentAddress ? old('country', $address->country) : $address->country;
        $defaultShippingValue = $isEditingCurrentAddress ? old('is_default_shipping', $address->is_default_shipping) : $address->is_default_shipping;
        $defaultBillingValue = $isEditingCurrentAddress ? old('is_default_billing', $address->is_default_billing) : $address->is_default_billing;
    @endphp

    <form method="POST" action="{{ route('customer.addresses.update', $address->id) }}" class="{{ $panelClass }}">
        @csrf
        @method('PUT')

        <div class="flex flex-col gap-4 border-b border-slate-100 pb-5 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-lg font-bold text-slate-900">Saved Address #{{ $loop->iteration }}</h3>
                    @if ($address->is_default_shipping)
                        <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-600">Default Shipping</span>
                    @endif
                    @if ($address->is_default_billing)
                        <span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-1 text-xs font-semibold text-primary-600">Default Billing</span>
                    @endif
                </div>
                <p class="mt-1 text-sm text-slate-500">Update this saved address directly and keep your delivery details current.</p>
            </div>

            <button type="submit" class="inline-flex h-10 shrink-0 items-center justify-center rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700">
                Save Address
            </button>
        </div>

        @if ($isEditingCurrentAddress && session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-2 md:col-span-2">
                <label for="line1_{{ $address->id }}" class="text-sm font-semibold text-slate-700">Address Line 1</label>
                <input id="line1_{{ $address->id }}" name="line1" class="{{ $inputClass }} @if($addressErrors->has('line1')) border-rose-300 focus:border-rose-400 focus:ring-rose-100 @endif" value="{{ $line1Value }}" placeholder="Address line 1" required>
                @if ($addressErrors->has('line1'))
                    <p class="text-xs font-medium text-rose-600">{{ $addressErrors->first('line1') }}</p>
                @endif
            </div>

            <div class="space-y-2 md:col-span-2">
                <label for="line2_{{ $address->id }}" class="text-sm font-semibold text-slate-700">Address Line 2 (Optional)</label>
                <input id="line2_{{ $address->id }}" name="line2" class="{{ $inputClass }} @if($addressErrors->has('line2')) border-rose-300 focus:border-rose-400 focus:ring-rose-100 @endif" value="{{ $line2Value }}" placeholder="Address line 2">
                @if ($addressErrors->has('line2'))
                    <p class="text-xs font-medium text-rose-600">{{ $addressErrors->first('line2') }}</p>
                @endif
            </div>

            <div class="space-y-2">
                <label for="city_{{ $address->id }}" class="text-sm font-semibold text-slate-700">City</label>
                <input id="city_{{ $address->id }}" name="city" class="{{ $inputClass }} @if($addressErrors->has('city')) border-rose-300 focus:border-rose-400 focus:ring-rose-100 @endif" value="{{ $cityValue }}" placeholder="City" required>
                @if ($addressErrors->has('city'))
                    <p class="text-xs font-medium text-rose-600">{{ $addressErrors->first('city') }}</p>
                @endif
            </div>

            <div class="space-y-2">
                <label for="state_{{ $address->id }}" class="text-sm font-semibold text-slate-700">State</label>
                <input id="state_{{ $address->id }}" name="state" class="{{ $inputClass }} @if($addressErrors->has('state')) border-rose-300 focus:border-rose-400 focus:ring-rose-100 @endif" value="{{ $stateValue }}" placeholder="State" required>
                @if ($addressErrors->has('state'))
                    <p class="text-xs font-medium text-rose-600">{{ $addressErrors->first('state') }}</p>
                @endif
            </div>

            <div class="space-y-2">
                <label for="postal_code_{{ $address->id }}" class="text-sm font-semibold text-slate-700">Postal Code</label>
                <input id="postal_code_{{ $address->id }}" name="postal_code" class="{{ $inputClass }} @if($addressErrors->has('postal_code')) border-rose-300 focus:border-rose-400 focus:ring-rose-100 @endif" value="{{ $postalCodeValue }}" placeholder="Postal code" required>
                @if ($addressErrors->has('postal_code'))
                    <p class="text-xs font-medium text-rose-600">{{ $addressErrors->first('postal_code') }}</p>
                @endif
            </div>

            <div class="space-y-2">
                <label for="country_{{ $address->id }}" class="text-sm font-semibold text-slate-700">Country</label>
                <select id="country_{{ $address->id }}" name="country" class="{{ $inputClass }} @if($addressErrors->has('country')) border-rose-300 focus:border-rose-400 focus:ring-rose-100 @endif" required>
                    @foreach ($countryOptions as $countryOption)
                        <option value="{{ $countryOption }}" @selected($countryValue === $countryOption)>{{ $countryOption }}</option>
                    @endforeach
                </select>
                @if ($addressErrors->has('country'))
                    <p class="text-xs font-medium text-rose-600">{{ $addressErrors->first('country') }}</p>
                @endif
            </div>
        </div>

        <div class="space-y-2">
            <label class="flex items-center gap-3 text-sm font-semibold text-slate-800">
                <input type="checkbox" name="is_default_shipping" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500" @checked($defaultShippingValue)>
                Set as Default Shipping Address
            </label>
            <label class="flex items-center gap-3 text-sm font-semibold text-slate-800">
                <input type="checkbox" name="is_default_billing" value="1" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500" @checked($defaultBillingValue)>
                Set as Default Billing Address
            </label>
        </div>
    </form>
@empty
    <div class="{{ $panelClass }}">
        <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-900">No saved addresses yet</h3>
                <p class="mt-1 text-sm text-slate-500">Add your first address so future delivery and billing flows can reuse it quickly.</p>
            </div>
            <button type="button" onclick="toggleModal('addAddressModal', true)" class="inline-flex h-10 items-center justify-center rounded-xl bg-primary-600 px-5 text-[13px] font-bold text-white shadow-sm transition hover:bg-primary-700">
                Add Address
            </button>
        </div>
    </div>
@endforelse
