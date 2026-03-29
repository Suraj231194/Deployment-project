@extends('layouts.app')

@section('title', 'Order Confirmation')
@section('customer_minimal', 'minimal')

@section('content')
    {{-- Background Container: Restricted to content area --}}
    <div class="relative min-h-[calc(100vh-120px)] w-full overflow-hidden bg-slate-900">
        {{-- Background Image with Microscope Theme --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('upload/backgrounds/order-success-bg.jpg') }}" 
                 alt="Background" 
                 class="h-full w-full object-cover opacity-50 mix-blend-overlay">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/70 via-slate-900/50 to-slate-900/90 backdrop-blur-[3px]"></div>
        </div>

        {{-- Content Area --}}
        <div class="relative z-10 flex min-h-[calc(100vh-120px)] items-center justify-center p-4">
            <div class="w-full max-w-xl rounded-[40px] border border-white/10 bg-white/5 p-8 text-center shadow-[0_32px_64px_-16px_rgba(0,0,0,0.5)] backdrop-blur-xl sm:p-12">
                {{-- Success Icon --}}
                <div class="mx-auto mb-10 flex h-28 w-28 items-center justify-center rounded-full bg-primary-500/10 shadow-inner">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary-600 text-white shadow-[0_0_40px_rgba(22,101,52,0.4)]">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                {{-- Content --}}
                <h1 class="font-display text-4xl font-bold tracking-tight text-white sm:text-5xl">Order Successful!</h1>
                <p class="mt-6 text-lg leading-8 text-slate-300">
                    Thank you for your order <span class="font-semibold text-primary-400">#{{ session('last_order_number', 'BGX-123456') }}</span>.
                    <br class="hidden sm:block">
                    We've sent a confirmation email with all the details and tracking information.
                </p>

                {{-- Actions --}}
                <div class="mt-12 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('customer.orders.preview') }}" class="group relative inline-flex h-14 items-center justify-center overflow-hidden rounded-2xl bg-primary-600 px-8 text-base font-bold text-white shadow-lg transition-all hover:bg-primary-700 hover:shadow-primary-600/30">
                        <span class="relative z-10">View Order Details</span>
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-500 group-hover:translate-x-full"></div>
                    </a>
                    <a href="{{ route('products.index') }}" class="inline-flex h-14 items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-8 text-base font-bold text-white backdrop-blur-md transition hover:bg-white/10 hover:border-white/25">
                        Continue Shopping
                    </a>
                </div>

                {{-- Optional Footer Info --}}
                <div class="mt-14 pt-10 border-t border-white/5">
                    <p class="text-sm text-slate-400">
                        Need help? <a href="{{ route('contact') }}" class="font-bold text-primary-400 hover:text-primary-300 transition-colors">Contact Support</a>
                    </p>
                    <p class="mt-6 text-[11px] font-black uppercase tracking-[0.3em] text-slate-500">
                        &copy; 2026 Biogenix Healthcare Solutions
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
