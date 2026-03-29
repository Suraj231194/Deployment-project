@extends('layouts.app')

@section('title', 'Under Maintenance')

@section('content')
    {{-- Background Container: Restricted to content area --}}
    <div class="relative min-h-[calc(100vh-120px)] w-full overflow-hidden bg-slate-900">
        {{-- Background Image with Microscope Theme --}}
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('upload/backgrounds/order-success-bg.jpg') }}" 
                 alt="Background" 
                 class="h-full w-full object-cover opacity-40 mix-blend-overlay">
            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/60 via-slate-900/40 to-slate-900/80 backdrop-blur-[2px]"></div>
        </div>

        {{-- Content Area --}}
        <div class="relative z-10 flex min-h-[calc(100vh-120px)] items-center justify-center p-4">
            <div class="w-full max-w-xl rounded-[40px] border border-white/10 bg-white/5 p-8 text-center shadow-[0_32px_64px_-16px_rgba(0,0,0,0.5)] backdrop-blur-xl sm:p-12">
                {{-- Maintenance Icon --}}
                <div class="mx-auto mb-10 flex h-28 w-28 items-center justify-center rounded-full bg-primary-500/10 shadow-inner">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary-600/20 border border-primary-500/30 text-primary-500 shadow-[0_0_40px_rgba(22,101,52,0.2)]">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.829-5.83m0 0a2.652 2.652 0 113.75-3.75M15.17 11.42a2.652 2.652 0 11-3.75 3.75M11.42 15.17l-5.83 5.83A2.652 2.652 0 111.84 17.25l5.83-5.83m0 0a2.652 2.652 0 113.75-3.75m0 0l-5.83-5.83L1.84 5.67a2.652 2.652 0 113.75-3.75l5.83 5.83m0 0a2.652 2.652 0 113.75-3.75l5.83-5.83L17.25 1.84a2.652 2.652 0 113.75 3.75l-5.83 5.83" />
                        </svg>
                    </div>
                </div>

                {{-- Content --}}
                <h1 class="font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">Under Maintenance</h1>
                <p class="mx-auto mt-6 max-w-sm text-base leading-7 text-slate-300">
                    We're currently fine-tuning our systems to serve you better. We'll be back online shortly with an improved experience.
                </p>

                {{-- Actions --}}
                <div class="mt-12 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('maintenance') }}" class="group relative inline-flex h-14 items-center justify-center gap-3 overflow-hidden rounded-2xl bg-primary-600 px-8 text-base font-bold text-white shadow-lg transition-all hover:bg-primary-700 hover:shadow-primary-600/30">
                        <svg class="relative z-10 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span class="relative z-10">Check Status</span>
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-500 group-hover:translate-x-full"></div>
                    </a>
                    <a href="{{ route('home') }}" class="inline-flex h-14 items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-8 text-base font-bold text-white backdrop-blur-md transition hover:bg-white/10 hover:border-white/25">
                        Return Home
                    </a>
                </div>

                {{-- Optional Footer Info --}}
                <div class="mt-14 pt-10 border-t border-white/5">
                    <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-500">
                        &copy; 2026 Biogenix Healthcare Solutions
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
