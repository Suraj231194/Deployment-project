@extends('layouts.app')

@section('title', 'Coming Soon')

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
                {{-- Status Badge --}}
                <div class="mx-auto mb-6 inline-flex items-center gap-2 rounded-full border border-primary-500/30 bg-primary-500/10 px-4 py-1.5 backdrop-blur-md">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                    </span>
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary-400">Something is Brewing</span>
                </div>

                {{-- Content --}}
                <h1 class="font-display text-4xl font-bold tracking-tight text-white sm:text-5xl">Launching Soon</h1>
                <p class="mx-auto mt-6 max-w-md text-lg leading-8 text-slate-300">
                    Our latest experience is just around the corner. We're shaping something new and will share it here once it's ready.
                </p>

                {{-- Notification Form --}}
                <div class="mx-auto mt-10 w-full max-w-md">
                    <form action="#" method="POST" class="relative group">
                        <input type="email" 
                               placeholder="Enter your professional email" 
                               class="h-16 w-full rounded-2xl border border-white/10 bg-white/5 px-6 pr-36 text-white outline-none transition-all focus:border-primary-500/50 focus:bg-white/10 focus:ring-4 focus:ring-primary-500/10 placeholder:text-slate-500">
                        <button type="submit" 
                                class="absolute right-2 top-2 h-12 rounded-xl bg-primary-600 px-6 text-sm font-bold text-white shadow-lg transition-all hover:bg-primary-700 active:scale-95 group-focus-within:shadow-primary-600/30">
                            Notify Me
                        </button>
                    </form>
                    <p class="mt-4 text-[11px] font-medium text-slate-500 uppercase tracking-[0.1em]">Join the waitlist for early access updates</p>
                </div>

                {{-- Optional Footer Info --}}
                <div class="mt-14 pt-10 border-t border-white/5">
                    <p class="text-[11px] font-black uppercase tracking-[0.3em] text-slate-600">
                        &copy; 2026 Biogenix Healthcare Solutions
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
