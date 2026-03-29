@extends('layouts.app')

@section('title', 'Access Restricted')

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
                {{-- Restricted Icon --}}
                <div class="mx-auto mb-10 flex h-28 w-28 items-center justify-center rounded-full bg-rose-500/10 shadow-inner">
                    <div class="flex h-20 w-20 items-center justify-center rounded-full bg-rose-600/20 border border-rose-500/30 text-rose-500 shadow-[0_0_40px_rgba(225,29,72,0.2)]">
                        <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                        </svg>
                    </div>
                </div>

                {{-- Content --}}
                <h1 class="font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">Access Restricted</h1>
                <p class="mx-auto mt-6 max-w-sm text-base leading-7 text-slate-300">
                    It looks like you don't have the necessary permissions to view this area. Access to this section requires additional clearance.
                </p>

                {{-- Actions --}}
                <div class="mt-12 flex flex-col gap-4 sm:flex-row sm:justify-center">
                    <a href="{{ route('contact') }}" class="group relative inline-flex h-14 items-center justify-center gap-3 overflow-hidden rounded-2xl bg-primary-600 px-8 text-base font-bold text-white shadow-lg transition-all hover:bg-primary-700 hover:shadow-primary-600/30">
                        <svg class="relative z-10 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                        </svg>
                        <span class="relative z-10">Request Access</span>
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-500 group-hover:translate-x-full"></div>
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex h-14 items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-8 text-base font-bold text-white backdrop-blur-md transition hover:bg-white/10 hover:border-white/25">
                        Contact Support
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
