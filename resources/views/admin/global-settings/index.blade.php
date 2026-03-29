@extends('admin.layout')

@section('title', 'Global Settings - Biogenix Admin')

@section('admin_content')
@php
    $themeModes = [
        [
            'title' => 'Light Mode',
            'selected' => true,
            'icon_type' => 'sun',
        ],
        [
            'title' => 'Dark Mode',
            'selected' => false,
            'icon_type' => 'moon',
        ],
        [
            'title' => 'System Default',
            'selected' => false,
            'icon_type' => 'system',
        ],
    ];
@endphp

<div class="min-h-[calc(100vh-8rem)] space-y-8 pb-24">
    <div>
        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">General Configuration</h2>
        <p class="mt-1 text-sm text-slate-500">Manage your organization's theme preferences and portal appearance.</p>
    </div>

    <section class="space-y-4">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 text-primary-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3a1 1 0 011 1v1.05a7.002 7.002 0 015.95 5.95H20a1 1 0 110 2h-1.05a7.002 7.002 0 01-5.95 5.95V20a1 1 0 11-2 0v-1.05a7.002 7.002 0 01-5.95-5.95H4a1 1 0 110-2h1.05a7.002 7.002 0 015.95-5.95V4a1 1 0 011-1zm0 6a3 3 0 100 6 3 3 0 000-6z" />
            </svg>
            <h3 class="text-base font-bold text-slate-900">Theme Mode</h3>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:gap-6">
            @foreach ($themeModes as $mode)
                <button
                    type="button"
                    class="flex min-h-[128px] flex-col items-center justify-center gap-4 rounded-2xl p-6 text-center transition
                    {{ $mode['selected']
                        ? 'border-2 border-primary-600 bg-slate-50/60 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.12)]'
                        : 'border border-slate-200 bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:border-slate-300 hover:bg-slate-50/40'
                    }}">
                    @if ($mode['icon_type'] === 'sun')
                        <svg class="h-8 w-8 text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3m15.364 6.364-1.06-1.06M6.696 6.696 5.636 5.636m12.728 0-1.06 1.06M6.696 17.304l-1.06 1.06M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    @elseif ($mode['icon_type'] === 'moon')
                        <svg class="h-8 w-8 text-slate-900" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                    @else
                        <svg class="h-8 w-8 text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1.25-3H4.5l2.75-2.125L6 11.5l3 1.875 3-1.875-1.25 3.375L13.5 17H9.75z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 7h6v10h-6zM4 7h6v10H4z" />
                        </svg>
                    @endif

                    <span class="text-[13px] font-bold text-slate-900">{{ $mode['title'] }}</span>
                </button>
            @endforeach
        </div>
    </section>

    <section class="space-y-4">
        <div class="flex items-center gap-2">
            <svg class="h-5 w-5 -rotate-45 text-primary-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
            </svg>
            <h3 class="text-base font-bold text-slate-900">Portal Color Theme</h3>
        </div>

        <div class="rounded-2xl border border-slate-100 bg-white p-6 lg:p-7 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
            <p class="mb-6 text-[11px] font-bold uppercase tracking-widest text-slate-400">Select a preset palette</p>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 lg:gap-6">
                <button type="button" class="flex min-h-[66px] items-center gap-3 rounded-xl border-2 border-primary-600 bg-white p-3.5 text-left shadow-sm">
                    <div class="relative h-8 w-8 flex-shrink-0 rounded-full bg-slate-100">
                        <span class="absolute inset-[-5px] rounded-full border-2 border-primary-600"></span>
                        <span class="absolute inset-[-9px] rounded-full border border-slate-200"></span>
                    </div>
                    <span class="text-[13px] font-bold text-primary-800">Biogenix Blue</span>
                </button>

                <button type="button" class="flex min-h-[66px] items-center gap-3 rounded-xl border border-transparent bg-white p-3.5 text-left transition hover:border-slate-200 hover:bg-slate-50/60">
                    <span class="h-8 w-8 flex-shrink-0 rounded-full bg-slate-100 shadow-sm"></span>
                    <span class="text-[13px] font-bold text-slate-700">Forest Green</span>
                </button>

                <button type="button" class="flex min-h-[66px] items-center gap-3 rounded-xl border border-transparent bg-white p-3.5 text-left transition hover:border-slate-200 hover:bg-slate-50/60">
                    <span class="h-8 w-8 flex-shrink-0 rounded-full bg-slate-100 shadow-sm"></span>
                    <span class="text-[13px] font-bold text-slate-700">Modern Indigo</span>
                </button>

                <button type="button" class="flex min-h-[66px] items-center gap-3 rounded-xl border border-transparent bg-white p-3.5 text-left transition hover:border-slate-200 hover:bg-slate-50/60">
                    <span class="h-8 w-8 flex-shrink-0 rounded-full bg-slate-100 shadow-sm"></span>
                    <span class="text-[13px] font-bold text-slate-700">Midnight Black</span>
                </button>
            </div>
        </div>
    </section>

    <div class="sticky bottom-0 z-10 -mx-4 border-t border-slate-200 bg-white/95 px-4 py-4 backdrop-blur sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8 xl:-mx-12 xl:px-12 2xl:-mx-16 2xl:px-16">
        <div class="flex items-center justify-end gap-4">
            <button type="button" class="text-[13px] font-bold text-slate-600 transition hover:text-slate-900">
                Discard Changes
            </button>
            <button type="button" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-8 py-3 text-[13px] font-bold text-white shadow-[0_6px_18px_-6px_rgba(9,27,63,0.45)] transition hover:bg-primary-700">
                Save Changes
            </button>
        </div>
    </div>
</div>

@endsection
