@props([
    'portal' => 'b2c',
    'active' => 'profile',
    'backUrl' => null,
    'backLabel' => 'Back',
    'title' => null,
    'description' => null,
    'eyebrow' => null,
    'framed' => false,
])

@php
    $portal = $portal === 'b2b' ? 'b2b' : 'b2c';
    $flashSuccess = session('success') ?: session('status');
    $flashError = session('error');
@endphp

<div class="min-h-screen bg-slate-50 py-4 lg:py-8">
    <div class="mx-auto flex w-full max-w-[96rem] flex-col gap-6 px-4 sm:px-6 lg:flex-row lg:gap-8 lg:px-8 xl:px-12 2xl:px-16">
        @include('partials.account-sidebar', ['portal' => $portal, 'active' => $active])

        <div id="customer-main-content" class="flex-1 min-w-0 space-y-6 pb-12">
            {{-- Page header — same pattern as admin dashboard --}}
            <div class="mb-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    @if ($title)
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $title }}</h2>
                    @endif
                    @if ($description)
                        <p class="text-sm text-slate-500 mt-1">{{ $description }}</p>
                    @endif
                </div>
                @isset($headerActions)
                    <div class="flex w-full items-center gap-3 sm:w-auto">
                        {{ $headerActions }}
                    </div>
                @endisset
            </div>

            @if ($flashSuccess)
                <x-alert type="success">
                    {{ $flashSuccess }}
                </x-alert>
            @endif

            @if ($flashError)
                <x-alert type="error">
                    {{ $flashError }}
                </x-alert>
            @endif

            @if ($errors->any())
                <x-alert type="error">
                    <p class="font-semibold">Please review the highlighted fields.</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            @isset($metrics)
                <div>
                    {{ $metrics }}
                </div>
            @endisset

            <div class="space-y-6">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="flex flex-wrap items-center justify-end gap-3 rounded-2xl border border-slate-100 bg-white p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)]">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>
