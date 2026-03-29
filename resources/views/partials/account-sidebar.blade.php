@php
    $portal = ($portal ?? 'b2c') === 'b2b' ? 'b2b' : 'b2c';
    $active = $active ?? 'profile';
    $user = auth()->user();
    $displayName = $user?->name ?? 'Prakhar Kapoor';
    $displayEmail = $user?->email ?? ($portal === 'b2b' ? 'prakhar@biogenix.com' : 'prakhar@example.com');
    $accountLabel = $portal === 'b2b' ? 'B2B Account' : 'B2C Customer';
    $ordersHref = route('customer.orders.preview', ['user_type' => $portal]);
    $supportHref = route('support-tickets.index');
    $navLinks = [
        ['key' => 'profile', 'label' => 'My Profile', 'href' => route('customer.profile.preview', ['user_type' => $portal]), 'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z'],
        ['key' => 'addresses', 'label' => 'Addresses', 'href' => route('customer.addresses.preview', ['user_type' => $portal]), 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z'],
        ['key' => 'orders', 'label' => 'Orders', 'href' => $ordersHref, 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ['key' => 'support', 'label' => 'Support Tickets', 'href' => $supportHref, 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z'],
    ];
@endphp

<aside id="customer-sidebar" class="w-full flex-shrink-0 lg:w-64">
    <nav class="space-y-1.5 rounded-2xl border border-slate-100 bg-white p-4 pb-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] lg:sticky lg:top-24">

        <h3 class="px-3 text-xs font-bold uppercase tracking-widest text-slate-400 mb-4 mt-2">{{ $portal === 'b2b' ? 'Business Portal' : 'Customer Portal' }}</h3>

        @foreach ($navLinks as $item)
            @php($isActive = $item['key'] === $active)
            <a
                href="{{ $item['href'] }}"
                data-key="{{ $item['key'] }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-[13px] transition w-full relative {{ $isActive ? 'bg-primary-600 text-white font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}"
            >
                @if ($isActive)
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-r-md"></div>
                    <svg class="h-5 w-5 text-indigo-200 z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                @else
                    <svg class="h-5 w-5 text-slate-400 z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" /></svg>
                @endif
                <span class="leading-tight z-10 flex-1">{{ $item['label'] }}</span>
            </a>
        @endforeach

        {{-- User Profile Footer --}}
        <div class="mt-6 pt-4 border-t border-slate-100">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="h-9 w-9 rounded-full bg-primary-600 text-white flex items-center justify-center text-[11px] font-black flex-shrink-0">
                    {{ strtoupper(substr($displayName, 0, 1)) }}{{ strtoupper(substr(strstr($displayName, ' ') ?: '', 1, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[13px] font-bold text-slate-900 truncate">{{ $displayName }}</p>
                    <p class="text-[11px] font-medium text-slate-400 truncate">{{ $displayEmail }}</p>
                </div>
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="h-8 w-8 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-500 transition flex items-center justify-center flex-shrink-0 cursor-pointer" title="Logout">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="h-8 w-8 rounded-lg hover:bg-primary-50 text-slate-400 hover:text-primary-600 transition flex items-center justify-center flex-shrink-0" title="Sign In">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    </a>
                @endauth
            </div>
        </div>
    </nav>
</aside>
