@extends('layouts.app')

@section('title', 'Page Not Found')

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
                {{-- 404 Graphic --}}
                <div class="mx-auto mb-8 flex items-center justify-center gap-6">
                    <span class="text-[7rem] font-black tracking-[-0.1em] text-primary-500 drop-shadow-[0_0_20px_rgba(34,197,94,0.3)] sm:text-[9rem]">4</span>
                    <div class="relative flex h-24 w-24 items-center justify-center rounded-[32px] border border-primary-500/40 bg-primary-600/20 shadow-[0_0_50px_rgba(34,197,94,0.4)]">
                        <svg class="h-12 w-12 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <span class="text-[7rem] font-black tracking-[-0.1em] text-primary-500 drop-shadow-[0_0_20px_rgba(34,197,94,0.3)] sm:text-[9rem]">4</span>
                </div>

                {{-- Content --}}
                <h1 class="font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">Page Not Found</h1>
                <p class="mx-auto mt-6 max-w-sm text-base leading-7 text-slate-300">
                    We couldn't find the resource you're looking for. The page may have been moved or the URL may be incorrect.
                </p>

                {{-- Actions --}}
                <div class="mt-10">
                    <a href="{{ route('home') }}" class="group relative inline-flex h-14 items-center justify-center gap-3 overflow-hidden rounded-2xl bg-primary-600 px-8 text-base font-bold text-white shadow-lg transition-all hover:bg-primary-700 hover:shadow-primary-600/30">
                        <svg class="relative z-10 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0 7-7 7 7M5 10v10a1 1 0 0 0 1 1h3m10-11 2 2m-2-2v10a1 1 0 0 1-1 1h-3"></path>
                        </svg>
                        <span class="relative z-10">Back to Dashboard</span>
                        <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-500 group-hover:translate-x-full"></div>
                    </a>
                </div>

                {{-- Optional Footer Info --}}
                <div class="mt-12 pt-8 border-t border-white/5">
                    <p class="text-xs font-black uppercase tracking-[0.3em] text-slate-500">
                        &copy; 2026 Biogenix Healthcare Solutions
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
