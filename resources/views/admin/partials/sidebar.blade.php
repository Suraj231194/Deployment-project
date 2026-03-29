<aside id="admin-sidebar" class="w-64 flex-shrink-0">
    <nav class="sticky top-24 space-y-1.5 bg-white p-4 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 pb-6">
        
        <h3 class="px-3 text-xs font-bold uppercase tracking-widest text-slate-400 mb-4 mt-2">Admin Portal</h3>

        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }} font-bold text-[13px] transition w-full relative">
            @if(request()->routeIs('admin.dashboard'))
                <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-r-md"></div>
                <svg class="h-5 w-5 text-indigo-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            @else
                <svg class="h-5 w-5 z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            @endif
            <span class="leading-tight z-10">Admin Dashboard</span>
        </a>

        @php
        $navLinks = [
            ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'title' => 'Products Management', 'route' => 'admin.products'],
            ['icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'title' => 'Pricing Management', 'route' => 'admin.pricing'],
            ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'Quotation/ PI', 'route' => 'admin.pi-quotation.index'],
            ['icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'title' => 'Order Management', 'route' => 'admin.orders', 'badge' => '12'],
            ['icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'title' => 'Delivery & Logistics'],
            ['icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'title' => 'Customer Management', 'route' => 'admin.customers', 'active_routes' => ['admin.customers', 'admin.customer-directory']],
            ['icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16', 'title' => 'Support Tickets', 'route' => 'admin.support-tickets', 'active_routes' => ['admin.support-tickets', 'admin.ui-fields-modification'], 'badge' => '3'],
            ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'title' => 'Roles & Permissions', 'route' => 'admin.role-permission'],
            ['icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'title' => 'Reviews & Incentives'],
            ['icon' => 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', 'title' => 'Sync Monitor', 'route' => 'admin.sync-monitor', 'badge' => 'NEW', 'badgeColor' => 'emerald'],
            ['icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'title' => 'System Settings'],
            ['icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01', 'title' => 'Global Settings', 'route' => 'admin.global-settings'],
        ];
        @endphp

        @foreach($navLinks as $link)
            @php
                $isActive = false;
                if (isset($link['active_routes'])) {
                    foreach ($link['active_routes'] as $ar) {
                        if (request()->routeIs($ar)) {
                            $isActive = true;
                            break;
                        }
                    }
                } elseif (isset($link['route'])) {
                    $isActive = request()->routeIs($link['route']);
                }
            @endphp
            <a href="{{ isset($link['route']) ? route($link['route']) : '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-[13px] transition w-full relative {{ $isActive ? 'bg-primary-600 text-white font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">
                @if($isActive)
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-white rounded-r-md"></div>
                    <svg class="h-5 w-5 text-indigo-200 z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"></path></svg>
                @else
                    <svg class="h-5 w-5 text-slate-400 z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $link['icon'] }}"></path></svg>
                @endif
                <span class="leading-tight z-10 flex-1">{{ $link['title'] }}</span>
                @if(isset($link['badge']))
                    @php $bc = isset($link['badgeColor']) ? $link['badgeColor'] : 'blue'; @endphp
                    <span class="text-[9px] font-black rounded-md px-1.5 py-0.5 z-10 {{ $isActive ? 'bg-white/20 text-white' : ($bc === 'emerald' ? 'bg-primary-50 text-primary-600' : 'bg-primary-50 text-primary-600') }}">{{ $link['badge'] }}</span>
                @endif
            </a>
        @endforeach

        {{-- User Profile --}}
        <div class="mt-6 pt-4 border-t border-slate-100">
            <div class="flex items-center gap-3 px-3 py-2">
                <div class="h-9 w-9 rounded-full bg-primary-600 text-white flex items-center justify-center text-[11px] font-black flex-shrink-0">SA</div>
                <div class="min-w-0 flex-1">
                    <p class="text-[13px] font-bold text-slate-900 truncate">Super Admin</p>
                    <p class="text-[11px] font-medium text-slate-400 truncate">admin@biogenix.com</p>
                </div>
                <button class="h-8 w-8 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-500 transition flex items-center justify-center flex-shrink-0 cursor-pointer" title="Logout">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </button>
            </div>
        </div>
    </nav>
</aside>
