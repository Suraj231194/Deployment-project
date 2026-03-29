@props([
    'steps' => [],
    'currentStep' => 1,
])

<div {{ $attributes->class(['w-full py-4']) }}>
    <div class="flex items-start">
        @foreach ($steps as $index => $step)
            @php
                $stepNum = $index + 1;
                $isActive = $stepNum <= $currentStep;
                $isCurrent = $stepNum === $currentStep;
            @endphp

            <div class="flex flex-1 items-start @if($loop->last) max-w-max flex-none @endif">
                <div class="relative z-10 flex flex-col items-center text-center">
                    <div @class([
                        'flex h-10 w-10 items-center justify-center rounded-full border-2 transition-all duration-300',
                        'border-primary-600 bg-primary-600 text-white shadow-lg shadow-primary-600/20' => $isActive,
                        'border-slate-300 bg-white text-slate-400' => !$isActive,
                        'scale-110 ring-4 ring-primary-100' => $isCurrent,
                    ])>
                        @if ($isActive && !$isCurrent)
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            <span class="text-sm font-bold">{{ $stepNum }}</span>
                        @endif
                    </div>
                    <span @class([
                        'mt-3 max-w-[8rem] text-xs font-bold uppercase tracking-[0.18em] transition-colors duration-300',
                        'text-primary-700' => $isActive,
                        'text-slate-400' => !$isActive,
                    ])>
                        {{ $step }}
                    </span>
                </div>

                @unless ($loop->last)
                    <div class="mt-5 flex-1 px-3 sm:px-4">
                        <div @class([
                            'h-0.5 w-full rounded-full transition-colors duration-300',
                            'bg-primary-600' => $stepNum < $currentStep,
                            'bg-slate-200' => $stepNum >= $currentStep,
                        ])></div>
                    </div>
                @endunless
            </div>
        @endforeach
    </div>
</div>
