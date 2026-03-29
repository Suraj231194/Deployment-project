@extends('layouts.app')

@section('title', 'Diagnostic Precision Quiz – Biogenix')
@section('meta_description', 'Test your diagnostic knowledge with the Biogenix Kits Mastery quiz. Answer 4 questions and unlock a 15% discount on your first clinical order.')

@section('content')
@php
    $quizQuestionsPayload = collect($quizQuestions ?? [])
        ->values()
        ->map(function ($quizQuestion): array {
            return [
                'id' => (int) $quizQuestion->id,
                'phase_title' => (string) $quizQuestion->phase_title,
                'question_text' => (string) $quizQuestion->question_text,
                'question_support_details' => is_array($quizQuestion->question_support_details ?? null)
                    ? $quizQuestion->question_support_details
                    : [],
                'answer_options' => collect($quizQuestion->answerOptions ?? [])
                    ->values()
                    ->map(function ($answerOption): array {
                        return [
                            'id' => (int) $answerOption->id,
                            'option_label' => (string) $answerOption->option_label,
                            'option_text' => (string) $answerOption->option_text,
                        ];
                    })
                    ->all(),
            ];
        })
        ->all();
    $hasQuizData = count($quizQuestionsPayload) > 0;
    $firstQuizQuestion = $hasQuizData ? $quizQuestionsPayload[0] : null;
    $quizPageClass = 'min-h-[calc(100vh-88px)]';
    $quizPanelClass = 'rounded-2xl border border-[var(--ui-border)] bg-[var(--ui-surface)] p-6 shadow-[var(--ui-shadow-soft)] sm:p-8';
    $quizProgressTrackClass = 'h-1.5 overflow-hidden rounded-full bg-[var(--ui-border)]';
    $quizProgressFillClass = 'h-full rounded-full bg-primary-600 transition-[width] duration-500 ease-in-out';
    $quizStepClass = 'quiz-step hidden translate-y-3 opacity-0 transition-all duration-300 [&.active]:block [&.active]:translate-y-0 [&.active]:opacity-100';
    $quizOptionClass = 'quiz-option group relative flex cursor-pointer items-center gap-4 rounded-2xl border-2 border-[var(--ui-border)] bg-[var(--ui-surface)] px-5 py-[1.1rem] transition-[border-color,background-color,box-shadow] duration-200 hover:border-[var(--color-neutral-200)] hover:bg-[var(--ui-surface-muted)] [&.selected]:border-primary-600 [&.selected]:bg-primary-600/5 [&.selected]:ring-2 [&.selected]:ring-primary-600/10';
    $quizOptionRadioClass = 'quiz-option-radio inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border-2 border-[var(--color-neutral-200)] text-[0.8rem] font-bold text-[var(--ui-text-muted)] transition-[background-color,border-color,color] duration-200 group-[.selected]:border-primary-600 group-[.selected]:bg-primary-600 group-[.selected]:text-white';
    $quizOptionCheckClass = 'quiz-option-check ml-auto hidden group-[.selected]:flex';
    $quizTipCardClass = 'quiz-tip-card rounded-[1.25rem] border border-[var(--ui-border)] bg-secondary-50 p-5';
    $quizContextCardClass = 'quiz-context-card rounded-[1.25rem] border border-[var(--ui-border)] bg-[var(--ui-surface)] p-5';
    $quizInsightCardClass = 'quiz-insight-card rounded-2xl border border-[var(--ui-border)] bg-secondary-100 p-4';
    $quizRefCardClass = 'quiz-ref-card rounded-[1.25rem] border border-[var(--ui-border)] bg-[var(--ui-surface)] p-5';
    $quizFieldClass = 'quiz-field h-12 w-full rounded-xl border border-[var(--ui-border)] bg-[var(--ui-surface-muted)] px-4 text-sm text-[var(--ui-text)] outline-none transition-[border-color,box-shadow] duration-200 placeholder:text-[var(--color-neutral-500)] focus:border-primary-600 focus:ring-2 focus:ring-primary-600/10';
    $quizPrimaryButtonClass = 'quiz-next-btn inline-flex items-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-[var(--ui-shadow-soft)] transition duration-200 hover:-translate-y-0.5 hover:bg-primary-700';
    $quizSubmitButtonClass = 'mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-600 py-3.5 text-sm font-semibold text-white shadow-[var(--ui-shadow-soft)] transition duration-200 hover:-translate-y-0.5 hover:bg-primary-700';
    $quizScoreRingClass = 'relative h-[220px] w-[220px] md:h-[240px] md:w-[240px]';
    $quizPerfTrackClass = 'h-2 overflow-hidden rounded-full bg-[var(--ui-border)]';
    $quizPerfFillClass = 'h-full rounded-full bg-[var(--ui-text)] transition-[width] delay-300 duration-1000 ease-in-out';
    $quizVoucherCodeClass = "mt-2 font-display text-2xl font-bold tracking-[0.3em] text-[var(--ui-surface)]";
    $quizImageOverlayClass = 'absolute inset-0 bg-gradient-to-b from-primary-600/20 to-primary-600/85';
@endphp
{{-- tailwind-width-safelist: w-0 w-[1%] w-[2%] w-[3%] w-[4%] w-[5%] w-[6%] w-[7%] w-[8%] w-[9%] w-[10%] w-[11%] w-[12%] w-[13%] w-[14%] w-[15%] w-[16%] w-[17%] w-[18%] w-[19%] w-[20%] w-[21%] w-[22%] w-[23%] w-[24%] w-[25%] w-[26%] w-[27%] w-[28%] w-[29%] w-[30%] w-[31%] w-[32%] w-[33%] w-[34%] w-[35%] w-[36%] w-[37%] w-[38%] w-[39%] w-[40%] w-[41%] w-[42%] w-[43%] w-[44%] w-[45%] w-[46%] w-[47%] w-[48%] w-[49%] w-[50%] w-[51%] w-[52%] w-[53%] w-[54%] w-[55%] w-[56%] w-[57%] w-[58%] w-[59%] w-[60%] w-[61%] w-[62%] w-[63%] w-[64%] w-[65%] w-[66%] w-[67%] w-[68%] w-[69%] w-[70%] w-[71%] w-[72%] w-[73%] w-[74%] w-[75%] w-[76%] w-[77%] w-[78%] w-[79%] w-[80%] w-[81%] w-[82%] w-[83%] w-[84%] w-[85%] w-[86%] w-[87%] w-[88%] w-[89%] w-[90%] w-[91%] w-[92%] w-[93%] w-[94%] w-[95%] w-[96%] w-[97%] w-[98%] w-[99%] w-full --}}
<div class="{{ $quizPageClass }}" id="quizApp">

    {{-- ═══ HEADER BAR ═══ --}}
    <div class="border-b border-[var(--ui-border)] bg-[var(--ui-surface)]">
        <div class="mx-auto w-full max-w-none px-4 py-5 sm:px-6 lg:px-8 xl:px-10">
            <div class="flex items-end justify-between">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-[var(--ui-text-muted)]" id="quizPhaseLabel">{{ $hasQuizData ? 'Assessment Phase: 01' : 'Assessment Setup' }}</p>
                    <h1 class="font-display mt-1 text-2xl font-bold tracking-tight text-[var(--ui-text)] md:text-3xl" id="quizTitle">{{ $firstQuizQuestion['phase_title'] ?? 'Diagnostic Precision Quiz' }}</h1>
                    <div class="{{ $quizProgressTrackClass }} mt-3 w-48 sm:w-64">
                        <div class="{{ $quizProgressFillClass }} w-0" id="quizProgressBar"></div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-base font-bold text-[var(--ui-text)]" id="quizStepLabel">{{ $hasQuizData ? 'Step 1 of '.count($quizQuestionsPayload) : '' }}</p>
                    <p class="text-sm text-[var(--ui-text-muted)]" id="quizPercentLabel">{{ $hasQuizData ? round(100 / count($quizQuestionsPayload)).'% Complete' : '0% Complete' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ QUIZ CONTENT ═══ --}}
    <div class="mx-auto w-full max-w-none px-4 py-8 sm:px-6 lg:px-8 xl:px-10">
        @if (! $hasQuizData)
            <div class="{{ $quizPanelClass }}">
                <h2 class="text-xl font-bold text-[var(--ui-text)]">Quiz is not available right now.</h2>
                <p class="mt-3 text-sm leading-6 text-[var(--ui-text-muted)]">Please run the quiz migration and seeder, then reload this page.</p>
            </div>
        @else

        {{-- ────────── STEP 1 ────────── --}}
        <div class="{{ $quizStepClass }} active" data-step="1">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="{{ $quizPanelClass }}">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary-600 text-sm font-bold text-white">1</span>
                            <h2 class="text-lg font-bold text-[var(--ui-text)] sm:text-xl">Which reagent kit is best suited for high-throughput automation?</h2>
                        </div>

                        <div class="mt-6 space-y-3" id="q1Options">
                            <div class="{{ $quizOptionClass }}" data-answer="A" onclick="selectOption(1, this)">
                                <span class="{{ $quizOptionRadioClass }}">A</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">Precision-X LIMS Kit</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="B" onclick="selectOption(1, this)">
                                <span class="{{ $quizOptionRadioClass }}">B</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">Bio-RGT Standard</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="C" onclick="selectOption(1, this)">
                                <span class="{{ $quizOptionRadioClass }}">C</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">Clinical-Max Assay</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="D" onclick="selectOption(1, this)">
                                <span class="{{ $quizOptionRadioClass }}">D</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">Eco-Lite Consumable</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between">
                        <button type="button" class="inline-flex cursor-not-allowed items-center gap-2 text-sm font-semibold text-[var(--color-neutral-500)]" disabled>
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
                            Previous
                        </button>
                        <button type="button" class="{{ $quizPrimaryButtonClass }}" onclick="nextStep(2)">
                            Next
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-5">
                    <div class="{{ $quizTipCardClass }}">
                        <p class="flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-secondary-700">
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-secondary-600"><svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg></span>
                            Clinical Tip
                        </p>
                        <h3 class="mt-2 text-base font-bold text-primary-600">Automation Integration</h3>
                        <p class="mt-2 text-sm leading-6 text-[var(--ui-text-muted)]">Automation-compatible kits utilize standard SBS footprints and barcoded vials. When selecting a kit for high-throughput environments, prioritize those with liquid-level sensing compatibility to minimize aspiration errors.</p>
                    </div>

                    <div class="{{ $quizContextCardClass }}">
                        <h4 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-[var(--ui-text-muted)]">Assessment Context</h4>
                        <ul class="mt-3 space-y-3">
                            <li class="flex items-center gap-2.5 text-sm text-[var(--ui-text-muted)]">
                                <svg class="h-4 w-4 shrink-0 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 12h8M8 8h8M8 16h4"/></svg>
                                Module: Reagent Classification
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-[var(--ui-text-muted)]">
                                <svg class="h-4 w-4 shrink-0 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Difficulty: Intermediate
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-[var(--ui-text-muted)]">
                                <svg class="h-4 w-4 shrink-0 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/></svg>
                                Time Limit: No constraints
                            </li>
                        </ul>
                    </div>

                    <div class="relative overflow-hidden rounded-2xl min-h-[180px]">
                        <img src="{{ asset('upload/corousel/image3.jpg') }}" alt="Automated pipetting system" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                        <div class="{{ $quizImageOverlayClass }}"></div>
                        <p class="absolute bottom-3 left-3 right-3 z-10 text-xs font-medium italic text-[var(--ui-surface)] opacity-90">Fig 1.1: Automated pipetting system with Biogenix reagents.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ────────── STEP 2 ────────── --}}
        <div class="{{ $quizStepClass }}" data-step="2">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="{{ $quizPanelClass }}">
                        <h2 class="text-lg font-bold text-[var(--ui-text)] sm:text-xl">What is the required storage temperature for the DNA Polymerase High Fidelity kit?</h2>
                        <div class="mt-6 space-y-3" id="q2Options">
                            <div class="{{ $quizOptionClass }}" data-answer="A" onclick="selectOption(2, this)">
                                <span class="{{ $quizOptionRadioClass }}">A</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">Room Temperature</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="B" onclick="selectOption(2, this)">
                                <span class="{{ $quizOptionRadioClass }}">B</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">4°C</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="C" onclick="selectOption(2, this)">
                                <span class="{{ $quizOptionRadioClass }}">C</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">-20°C</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="D" onclick="selectOption(2, this)">
                                <span class="{{ $quizOptionRadioClass }}">D</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">-80°C</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-between">
                        <button type="button" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--ui-text-muted)] transition hover:text-[var(--ui-text)]" onclick="prevStep(1)">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
                            Previous
                        </button>
                        <button type="button" class="{{ $quizPrimaryButtonClass }}" onclick="nextStep(3)">
                            Next Question
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-5">
                    <div class="{{ $quizContextCardClass }}">
                        <h3 class="flex items-center gap-2 text-base font-bold text-[var(--ui-text)]">
                            <svg class="h-5 w-5 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 12h8M8 8h8M8 16h4"/></svg>
                            Storage Best Practices
                        </h3>
                        <ul class="mt-4 space-y-4">
                            <li>
                                <p class="flex items-center gap-2 text-sm font-bold text-[var(--ui-text)]"><span class="inline-block h-2 w-2 rounded-full bg-secondary-600"></span> Enzymatic Stability</p>
                                <p class="mt-1 text-sm leading-6 text-[var(--ui-text-muted)]">Most high-fidelity polymerases lose activity if exposed to repeated freeze-thaw cycles. Always use a cooling block during use.</p>
                            </li>
                            <li>
                                <p class="flex items-center gap-2 text-sm font-bold text-[var(--ui-text)]"><span class="inline-block h-2 w-2 rounded-full bg-secondary-600"></span> Reagent Segregation</p>
                                <p class="mt-1 text-sm leading-6 text-[var(--ui-text-muted)]">Keep dNTPs and primers in separate aliquots to prevent cross-contamination during library preparation.</p>
                            </li>
                        </ul>
                    </div>
                    <div class="{{ $quizInsightCardClass }}">
                        <p class="flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-secondary-700">
                            <span class="inline-block h-2 w-2 rounded-full bg-secondary-600"></span>
                            Clinical Insight
                        </p>
                        <p class="mt-2 text-sm leading-6 text-[var(--ui-text-muted)]">Storing at -20°C in a non-frost-free freezer is critical for maintaining long-term buffer molarity.</p>
                    </div>
                    <div class="{{ $quizRefCardClass }}">
                        <h4 class="text-base font-bold text-[var(--ui-text)]">Reference Material</h4>
                        <div class="mt-3 space-y-2">
                            <div class="flex items-center justify-between rounded-xl border border-[var(--ui-border)] bg-[var(--ui-surface-muted)] px-4 py-3">
                                <span class="flex items-center gap-2 text-sm font-medium text-[var(--ui-text-muted)]">
                                    <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    Kit_Datasheet_V4.pdf
                                </span>
                                <svg class="h-4 w-4 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </div>
                            <div class="flex items-center justify-between rounded-xl border border-[var(--ui-border)] bg-[var(--ui-surface-muted)] px-4 py-3">
                                <span class="flex items-center gap-2 text-sm font-medium text-[var(--ui-text-muted)]">
                                    <svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    Storage_Protocol_Guide
                                </span>
                                <svg class="h-4 w-4 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ────────── STEP 3 ────────── --}}
        <div class="{{ $quizStepClass }}" data-step="3">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="{{ $quizPanelClass }}">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary-600 text-sm font-bold text-white">3</span>
                            <h2 class="text-lg font-bold text-[var(--ui-text)] sm:text-xl">Which certification standard governs IVD reagent manufacturing quality?</h2>
                        </div>
                        <div class="mt-6 space-y-3" id="q3Options">
                            <div class="{{ $quizOptionClass }}" data-answer="A" onclick="selectOption(3, this)">
                                <span class="{{ $quizOptionRadioClass }}">A</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">ISO 9001:2015</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="B" onclick="selectOption(3, this)">
                                <span class="{{ $quizOptionRadioClass }}">B</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">ISO 13485:2016</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="C" onclick="selectOption(3, this)">
                                <span class="{{ $quizOptionRadioClass }}">C</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">CE-IVD Directive 98/79/EC</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="D" onclick="selectOption(3, this)">
                                <span class="{{ $quizOptionRadioClass }}">D</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">GMP Annex 15</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-between">
                        <button type="button" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--ui-text-muted)] transition hover:text-[var(--ui-text)]" onclick="prevStep(2)">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
                            Previous
                        </button>
                        <button type="button" class="{{ $quizPrimaryButtonClass }}" onclick="nextStep(4)">
                            Next Question
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-5">
                    <div class="{{ $quizTipCardClass }}">
                        <p class="flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-secondary-700">
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-secondary-600"><svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg></span>
                            Clinical Tip
                        </p>
                        <h3 class="mt-2 text-base font-bold text-primary-600">Regulatory Compliance</h3>
                        <p class="mt-2 text-sm leading-6 text-[var(--ui-text-muted)]">ISO 13485 is the primary quality management standard for medical devices and IVD products. It ensures traceability, risk management, and process validation throughout the product lifecycle.</p>
                    </div>
                    <div class="{{ $quizContextCardClass }}">
                        <h4 class="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--ui-text-muted)]">Assessment Context</h4>
                        <ul class="mt-3 space-y-3">
                            <li class="flex items-center gap-2.5 text-sm text-[var(--ui-text-muted)]">
                                <svg class="h-4 w-4 shrink-0 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 12h8M8 8h8M8 16h4"/></svg>
                                Module: Compliance Standards
                            </li>
                            <li class="flex items-center gap-2.5 text-sm text-[var(--ui-text-muted)]">
                                <svg class="h-4 w-4 shrink-0 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Difficulty: Advanced
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- ────────── STEP 4 ────────── --}}
        <div class="{{ $quizStepClass }}" data-step="4">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="{{ $quizPanelClass }}">
                        <div class="flex items-start gap-3">
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary-600 text-sm font-bold text-white">4</span>
                            <h2 class="text-lg font-bold text-[var(--ui-text)] sm:text-xl">Which sample preparation method yields highest DNA purity for NGS workflows?</h2>
                        </div>
                        <div class="mt-6 space-y-3" id="q4Options">
                            <div class="{{ $quizOptionClass }}" data-answer="A" onclick="selectOption(4, this)">
                                <span class="{{ $quizOptionRadioClass }}">A</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">Phenol-chloroform extraction</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="B" onclick="selectOption(4, this)">
                                <span class="{{ $quizOptionRadioClass }}">B</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">Magnetic bead-based purification</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="C" onclick="selectOption(4, this)">
                                <span class="{{ $quizOptionRadioClass }}">C</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">Silica membrane column</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                            <div class="{{ $quizOptionClass }}" data-answer="D" onclick="selectOption(4, this)">
                                <span class="{{ $quizOptionRadioClass }}">D</span>
                                <span class="text-sm font-semibold text-[var(--ui-text)]">Salting-out precipitation</span>
                                <span class="{{ $quizOptionCheckClass }}"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-between">
                        <button type="button" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--ui-text-muted)] transition hover:text-[var(--ui-text)]" onclick="prevStep(3)">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/></svg>
                            Previous
                        </button>
                        <button type="button" class="{{ $quizPrimaryButtonClass }}" onclick="nextStep(5)">
                            Finish Quiz
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/></svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-5">
                    <div class="{{ $quizTipCardClass }}">
                        <p class="flex items-center gap-2 text-[10px] font-semibold uppercase tracking-[0.2em] text-secondary-700">
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-secondary-600"><svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20"><circle cx="10" cy="10" r="4"/></svg></span>
                            Clinical Tip
                        </p>
                        <h3 class="mt-2 text-base font-bold text-primary-600">NGS Library Prep</h3>
                        <p class="mt-2 text-sm leading-6 text-[var(--ui-text-muted)]">Magnetic bead-based purification provides the best combination of purity and automation compatibility for next-generation sequencing, with minimal carry-over contamination.</p>
                    </div>
                    <div class="relative overflow-hidden rounded-2xl min-h-[180px]">
                        <img src="{{ asset('upload/corousel/image5.jpg') }}" alt="NGS sample preparation" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                        <div class="{{ $quizImageOverlayClass }}"></div>
                        <p class="absolute bottom-3 left-3 right-3 z-10 text-xs font-medium italic text-[var(--ui-surface)] opacity-90">Fig 4.1: NGS library preparation workflow.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ────────── STEP 5: LEAD CAPTURE FORM ────────── --}}
        <div class="{{ $quizStepClass }}" data-step="5">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <div class="flex flex-col justify-center">
                    <span class="inline-flex w-fit rounded-full bg-secondary-600 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.2em] text-primary-800">Final Step</span>
                    <h2 class="font-display mt-4 text-3xl font-bold tracking-tight text-[var(--ui-text)] md:text-4xl lg:text-5xl">You're almost<br>there!</h2>
                    <p class="mt-4 max-w-md text-sm leading-6 text-[var(--ui-text-muted)] md:text-base">Enter your details to calculate your precision score and unlock your exclusive coupon code.</p>
                    <div class="mt-6">
                        <div class="flex items-center justify-between text-sm font-semibold text-[var(--ui-text)]">
                            <span>Analysis Completion</span>
                            <span>100%</span>
                        </div>
                        <div class="{{ $quizProgressTrackClass }} mt-2">
                            <div class="{{ $quizProgressFillClass }} w-full"></div>
                        </div>
                    </div>
                    <div class="mt-8 flex items-start gap-3">
                        <span class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-600">
                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold text-[var(--ui-text)]">Privacy Protocol</p>
                            <p class="mt-1 text-xs leading-5 text-[var(--ui-text-muted)]">Your clinical data is encrypted using 256-bit AES standards. We never share your results with third-party providers.</p>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="{{ $quizPanelClass }}">
                        <div class="{{ $quizProgressTrackClass }} mb-6">
                            <div class="h-full w-full rounded-full bg-secondary-600"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--ui-text-muted)]">First Name</label>
                                <input type="text" id="quizFirstName" class="{{ $quizFieldClass }}" placeholder="John">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--ui-text-muted)]">Last Name</label>
                                <input type="text" id="quizLastName" class="{{ $quizFieldClass }}" placeholder="Doe">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="mb-1.5 block text-[11px] font-semibold uppercase tracking-[0.18em] text-[var(--ui-text-muted)]">Email Address</label>
                            <input type="email" id="quizEmail" class="{{ $quizFieldClass }}" placeholder="john.doe@medical-cloud.com">
                        </div>
                        <button type="button" id="quizSubmitButton" class="{{ $quizSubmitButtonClass }}" onclick="showResults()">
                            Unlock My Score & Reward
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6"/></svg>
                        </button>
                        <p class="mt-3 flex items-center justify-center gap-1.5 text-xs text-[var(--color-neutral-500)]">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Secure submission via Biogenix Clinical Gateway
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ────────── STEP 6: RESULTS ────────── --}}
        <div class="{{ $quizStepClass }}" data-step="6">
            <div class="mb-8">
                <span class="inline-flex rounded-full bg-primary-500 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.2em] text-white">Assessment Complete</span>
                <div class="mt-4 grid grid-cols-1 gap-8 lg:grid-cols-2">
                    <div>
                        <h2 class="font-display text-3xl font-bold tracking-tight text-[var(--ui-text)] md:text-4xl lg:text-5xl xl:whitespace-nowrap" id="quizResultTitle">Advanced<br>Proficiency Level<br>Attained.</h2>
                        <p class="mt-4 max-w-lg text-sm leading-6 text-[var(--ui-text-muted)] md:text-base" id="quizResultDescription">Your technical precision in diagnostic protocols demonstrates exceptional mastery of Biogenix standards and laboratory compliance.</p>
                    </div>
                    <div class="flex items-center justify-center lg:items-start lg:justify-end lg:-mt-4">
                        <div class="{{ $quizScoreRingClass }}">
                            <svg viewBox="0 0 200 200" class="h-full w-full">
                                <circle class="stroke-[var(--ui-border)]" cx="100" cy="100" r="85" fill="none" stroke-width="10"/>
                                <circle class="stroke-primary-600 transition-[stroke-dashoffset] duration-[1200ms] ease-in-out" id="scoreRingFill" cx="100" cy="100" r="85" fill="none" stroke-width="10" stroke-linecap="round" stroke-dasharray="534" stroke-dashoffset="534" transform="rotate(-90 100 100)"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="font-display text-5xl font-bold text-[var(--ui-text)]" id="scoreValue">0%</span>
                                <span class="mt-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-[var(--ui-text-muted)]">Precision Score</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="{{ $quizPanelClass }}">
                    <h3 class="flex items-center gap-2 text-base font-bold text-[var(--ui-text)]">
                        <svg class="h-5 w-5 text-[var(--ui-text-muted)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Phase Performance Breakdown
                    </h3>
                    <div class="mt-6 space-y-5">
                        <div>
                            <div class="flex items-center justify-between text-sm"><span class="font-semibold text-[var(--ui-text)]" id="quizPerfLabel1">Kits Mastery</span><span class="font-bold text-[var(--ui-text)]" id="quizPerfValue1">98%</span></div>
                            <div class="{{ $quizPerfTrackClass }} mt-2"><div class="{{ $quizPerfFillClass }} w-0" id="quizPerfBar1" data-perf="98"></div></div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-sm"><span class="font-semibold text-[var(--ui-text)]" id="quizPerfLabel2">Storage Requirements</span><span class="font-bold text-[var(--ui-text)]" id="quizPerfValue2">85%</span></div>
                            <div class="{{ $quizPerfTrackClass }} mt-2"><div class="{{ $quizPerfFillClass }} w-0" id="quizPerfBar2" data-perf="85"></div></div>
                        </div>
                        <div>
                            <div class="flex items-center justify-between text-sm"><span class="font-semibold text-[var(--ui-text)]" id="quizPerfLabel3">System Compatibility</span><span class="font-bold text-[var(--ui-text)]" id="quizPerfValue3">94%</span></div>
                            <div class="{{ $quizPerfTrackClass }} mt-2"><div class="{{ $quizPerfFillClass }} w-0" id="quizPerfBar3" data-perf="94"></div></div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-2xl bg-primary-600 p-6 text-white shadow-sm sm:p-8">
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-secondary-600/25">
                        <svg class="h-6 w-6 text-secondary-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <h3 class="font-display mt-4 text-2xl font-bold text-[var(--ui-surface)]">Certification<br>Reward</h3>
                    <p class="mt-3 text-sm leading-6 text-[var(--ui-surface)] opacity-70">Redeem your exclusive proficiency discount on any premium diagnostic kit.</p>
                    <div class="mt-5 rounded-xl border border-[var(--glass-border)] bg-[var(--glass-border)] px-5 py-4 text-center">
                        <p class="text-[10px] font-semibold uppercase tracking-[0.25em] text-[var(--ui-surface)] opacity-60">Voucher Code</p>
                        <p class="{{ $quizVoucherCodeClass }}" id="voucherCode">BIOGENIX15</p>
                    </div>
                    <button type="button" class="mt-4 inline-flex w-full items-center justify-center rounded-xl border-2 border-[var(--glass-border)] px-6 py-3 text-sm font-bold text-[var(--ui-surface)] transition hover:bg-[var(--glass-border)]" onclick="copyVoucher()">
                        Copy and Shop
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    var quizQuestions = @json($quizQuestionsPayload);
    var quizAnswers = {};
    var currentStep = 1;
    var totalQuestions = quizQuestions.length;
    var quizSubmitUrl = @json(route('diagnostic-quiz.store'));
    var quizSubmitButtonDefaultHtml = '';
    var currentVoucherCode = document.getElementById('voucherCode') ? document.getElementById('voucherCode').textContent : 'BIOGENIX15';
    var quizOptionClassName = @json($quizOptionClass);
    var quizOptionRadioClassName = @json($quizOptionRadioClass);
    var quizOptionCheckClassName = @json($quizOptionCheckClass);

    function escapeHtml(value) {
        return String(value || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function resolveAssetUrl(path) {
        if (!path) {
            return '';
        }

        if (/^https?:\/\//i.test(path)) {
            return path;
        }

        return window.location.origin + '/' + String(path).replace(/^\/+/, '');
    }

    function getQuestionByStep(step) {
        return quizQuestions[step - 1] || null;
    }

    function findSidebarCard(questionSupportDetails, cardType) {
        var sidebarCards = Array.isArray(questionSupportDetails && questionSupportDetails.sidebar_cards)
            ? questionSupportDetails.sidebar_cards
            : [];

        return sidebarCards.find(function (sidebarCard) {
            return sidebarCard && sidebarCard.card_type === cardType;
        }) || null;
    }

    function iconMarkup(iconName) {
        if (iconName === 'check-circle') {
            return '<svg class="h-4 w-4 shrink-0 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        }

        if (iconName === 'clock') {
            return '<svg class="h-4 w-4 shrink-0 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 6v6l4 2"/></svg>';
        }

        if (iconName === 'file-green') {
            return '<svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0013.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>';
        }

        if (iconName === 'download') {
            return '<svg class="h-4 w-4 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>';
        }

        if (iconName === 'external-link') {
            return '<svg class="h-4 w-4 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>';
        }

        return '<svg class="h-4 w-4 shrink-0 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 12h8M8 8h8M8 16h4"/></svg>';
    }

    function buildOptionMarkup(stepNumber, questionData) {
        return (questionData.answer_options || []).map(function (answerOption) {
            return '<div class="' + quizOptionClassName + '" data-answer="' + escapeHtml(answerOption.option_label) + '" data-question-id="' + escapeHtml(questionData.id) + '" data-option-id="' + escapeHtml(answerOption.id) + '" onclick="selectOption(' + stepNumber + ', this)">' +
                '<span class="' + quizOptionRadioClassName + '">' + escapeHtml(answerOption.option_label) + '</span>' +
                '<span class="text-sm font-semibold text-[var(--ui-text)]">' + escapeHtml(answerOption.option_text) + '</span>' +
                '<span class="' + quizOptionCheckClassName + '"><svg class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>' +
            '</div>';
        }).join('');
    }

    function buildContextListMarkup(items) {
        return (Array.isArray(items) ? items : []).map(function (item) {
            return '<li class="flex items-center gap-2.5 text-sm text-[var(--ui-text-muted)]">' +
                iconMarkup(item.icon) +
                escapeHtml(item.text) +
            '</li>';
        }).join('');
    }

    function buildContextSectionMarkup(sections) {
        return (Array.isArray(sections) ? sections : []).map(function (section) {
            return '<li>' +
                '<p class="flex items-center gap-2 text-sm font-bold text-[var(--ui-text)]"><span class="inline-block h-2 w-2 rounded-full bg-secondary-600"></span> ' + escapeHtml(section.title) + '</p>' +
                '<p class="mt-1 text-sm leading-6 text-[var(--ui-text-muted)]">' + escapeHtml(section.description) + '</p>' +
            '</li>';
        }).join('');
    }

    function buildReferenceListMarkup(items) {
        return (Array.isArray(items) ? items : []).map(function (item) {
            return '<div class="flex items-center justify-between rounded-xl border border-[var(--ui-border)] bg-[var(--ui-surface-muted)] px-4 py-3">' +
                '<span class="flex items-center gap-2 text-sm font-medium text-[var(--ui-text-muted)]">' +
                    iconMarkup(item.leading_icon) +
                    escapeHtml(item.document_name) +
                '</span>' +
                iconMarkup(item.trailing_icon) +
            '</div>';
        }).join('');
    }

    function setPercentWidthClass(element, percent) {
        if (!element) {
            return;
        }

        var normalized = Math.max(0, Math.min(100, Math.round(Number(percent) || 0)));

        for (var widthValue = 0; widthValue <= 100; widthValue++) {
            element.classList.remove(
                widthValue === 0
                    ? 'w-0'
                    : widthValue === 100
                        ? 'w-full'
                        : 'w-[' + widthValue + '%]'
            );
        }

        element.classList.add(
            normalized === 0
                ? 'w-0'
                : normalized === 100
                    ? 'w-full'
                    : 'w-[' + normalized + '%]'
        );
    }

    function hydrateQuestionStep(stepNumber) {
        var questionData = getQuestionByStep(stepNumber);
        var stepElement = document.querySelector('.quiz-step[data-step="' + stepNumber + '"]');

        if (!questionData || !stepElement) {
            return;
        }

        // Step 1: replace the question text and answer options with the database-backed quiz content.
        var questionTitle = stepElement.querySelector('.lg\\:col-span-2 h2');
        var optionsContainer = document.getElementById('q' + stepNumber + 'Options');

        if (questionTitle) {
            questionTitle.textContent = questionData.question_text || '';
        }

        if (optionsContainer) {
            optionsContainer.innerHTML = buildOptionMarkup(stepNumber, questionData);
        }

        // Step 2: update the sidebar cards shown beside the question while keeping the same visual layout.
        var questionSupportDetails = questionData.question_support_details || {};
        var tipCard = findSidebarCard(questionSupportDetails, 'tip');
        var contextListCard = findSidebarCard(questionSupportDetails, 'context_list');
        var contextSectionsCard = findSidebarCard(questionSupportDetails, 'context_sections');
        var insightCard = findSidebarCard(questionSupportDetails, 'insight');
        var referenceListCard = findSidebarCard(questionSupportDetails, 'reference_list');
        var imageCard = findSidebarCard(questionSupportDetails, 'image');

        if (stepNumber === 1) {
            if (tipCard) {
                var stepOneTipCard = stepElement.querySelector('.quiz-tip-card');
                if (stepOneTipCard) {
                    var stepOneTipTitle = stepOneTipCard.querySelector('h3');
                    var stepOneTipBody = stepOneTipCard.querySelector('p.mt-2.text-sm');

                    if (stepOneTipTitle) stepOneTipTitle.textContent = tipCard.title || '';
                    if (stepOneTipBody) stepOneTipBody.textContent = tipCard.description || '';
                }
            }

            if (contextListCard) {
                var stepOneContextCard = stepElement.querySelector('.quiz-context-card');
                if (stepOneContextCard) {
                    var stepOneContextTitle = stepOneContextCard.querySelector('h4');
                    var stepOneContextList = stepOneContextCard.querySelector('ul');

                    if (stepOneContextTitle) stepOneContextTitle.textContent = contextListCard.title || '';
                    if (stepOneContextList) stepOneContextList.innerHTML = buildContextListMarkup(contextListCard.items);
                }
            }

            if (imageCard) {
                var stepOneImage = stepElement.querySelector('img');
                var stepOneImageCaption = stepElement.querySelector('.relative p');

                if (stepOneImage) {
                    stepOneImage.src = resolveAssetUrl(imageCard.image_path);
                    stepOneImage.alt = imageCard.image_alt_text || 'Diagnostic quiz supporting visual';
                }

                if (stepOneImageCaption) {
                    stepOneImageCaption.textContent = imageCard.image_caption || '';
                }
            }
        }

        if (stepNumber === 2) {
            if (contextSectionsCard) {
                var stepTwoContextCard = stepElement.querySelector('.quiz-context-card');
                if (stepTwoContextCard) {
                    var stepTwoContextTitle = stepTwoContextCard.querySelector('h3');
                    var stepTwoContextSections = stepTwoContextCard.querySelector('ul');

                    if (stepTwoContextTitle) {
                        stepTwoContextTitle.innerHTML = '<svg class="h-5 w-5 text-[var(--color-neutral-500)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 12h8M8 8h8M8 16h4"/></svg> ' + escapeHtml(contextSectionsCard.title || '');
                    }

                    if (stepTwoContextSections) {
                        stepTwoContextSections.innerHTML = buildContextSectionMarkup(contextSectionsCard.sections);
                    }
                }
            }

            if (insightCard) {
                var stepTwoInsightCard = stepElement.querySelector('.quiz-insight-card');
                if (stepTwoInsightCard) {
                    var stepTwoInsightParagraphs = stepTwoInsightCard.querySelectorAll('p');

                    if (stepTwoInsightParagraphs[0]) {
                        stepTwoInsightParagraphs[0].innerHTML = '<span class="inline-block h-2 w-2 rounded-full bg-secondary-600"></span> ' + escapeHtml(insightCard.eyebrow || 'Clinical Insight');
                    }

                    if (stepTwoInsightParagraphs[1]) {
                        stepTwoInsightParagraphs[1].textContent = insightCard.description || '';
                    }
                }
            }

            if (referenceListCard) {
                var stepTwoReferenceCard = stepElement.querySelector('.quiz-ref-card');
                if (stepTwoReferenceCard) {
                    var stepTwoReferenceTitle = stepTwoReferenceCard.querySelector('h4');
                    var stepTwoReferenceList = stepTwoReferenceCard.querySelector('.mt-3.space-y-2');

                    if (stepTwoReferenceTitle) stepTwoReferenceTitle.textContent = referenceListCard.title || '';
                    if (stepTwoReferenceList) stepTwoReferenceList.innerHTML = buildReferenceListMarkup(referenceListCard.items);
                }
            }
        }

        if (stepNumber === 3) {
            if (tipCard) {
                var stepThreeTipCard = stepElement.querySelector('.quiz-tip-card');
                if (stepThreeTipCard) {
                    var stepThreeTipTitle = stepThreeTipCard.querySelector('h3');
                    var stepThreeTipBody = stepThreeTipCard.querySelector('p.mt-2.text-sm');

                    if (stepThreeTipTitle) stepThreeTipTitle.textContent = tipCard.title || '';
                    if (stepThreeTipBody) stepThreeTipBody.textContent = tipCard.description || '';
                }
            }

            if (contextListCard) {
                var stepThreeContextCard = stepElement.querySelector('.quiz-context-card');
                if (stepThreeContextCard) {
                    var stepThreeContextTitle = stepThreeContextCard.querySelector('h4');
                    var stepThreeContextList = stepThreeContextCard.querySelector('ul');

                    if (stepThreeContextTitle) stepThreeContextTitle.textContent = contextListCard.title || '';
                    if (stepThreeContextList) stepThreeContextList.innerHTML = buildContextListMarkup(contextListCard.items);
                }
            }
        }

        if (stepNumber === 4) {
            if (tipCard) {
                var stepFourTipCard = stepElement.querySelector('.quiz-tip-card');
                if (stepFourTipCard) {
                    var stepFourTipTitle = stepFourTipCard.querySelector('h3');
                    var stepFourTipBody = stepFourTipCard.querySelector('p.mt-2.text-sm');

                    if (stepFourTipTitle) stepFourTipTitle.textContent = tipCard.title || '';
                    if (stepFourTipBody) stepFourTipBody.textContent = tipCard.description || '';
                }
            }

            if (imageCard) {
                var stepFourImage = stepElement.querySelector('img');
                var stepFourImageCaption = stepElement.querySelector('.relative p');

                if (stepFourImage) {
                    stepFourImage.src = resolveAssetUrl(imageCard.image_path);
                    stepFourImage.alt = imageCard.image_alt_text || 'Diagnostic quiz supporting visual';
                }

                if (stepFourImageCaption) {
                    stepFourImageCaption.textContent = imageCard.image_caption || '';
                }
            }
        }
    }

    function hydrateQuizContent() {
        if (totalQuestions === 0) {
            return;
        }

        for (var stepNumber = 1; stepNumber <= totalQuestions; stepNumber++) {
            hydrateQuestionStep(stepNumber);
        }

        updateHeader(1);
    }

    function selectOption(stepNumber, element) {
        var container = element.parentNode;
        var options = container.querySelectorAll('.quiz-option');
        options.forEach(function (optionElement) {
            optionElement.classList.remove('selected');
        });

        element.classList.add('selected');

        var questionData = getQuestionByStep(stepNumber);
        var selectedOptionId = Number(element.getAttribute('data-option-id'));

        if (questionData && selectedOptionId > 0) {
            quizAnswers[questionData.id] = selectedOptionId;
        }
    }

    function updateHeader(step) {
        var phaseLabel = document.getElementById('quizPhaseLabel');
        var title = document.getElementById('quizTitle');
        var stepLabel = document.getElementById('quizStepLabel');
        var percentLabel = document.getElementById('quizPercentLabel');
        var progressBar = document.getElementById('quizProgressBar');

        if (step <= totalQuestions) {
            var questionData = getQuestionByStep(step);
            var pct = totalQuestions > 0 ? Math.round((step / totalQuestions) * 100) : 0;
            var normalizedStepLabel = step < 10 ? '0' + step : String(step);

            phaseLabel.textContent = 'Assessment Phase: ' + normalizedStepLabel;
            title.textContent = questionData ? questionData.phase_title : 'Diagnostic Precision Quiz';
            stepLabel.textContent = 'Step ' + step + ' of ' + totalQuestions;
            percentLabel.textContent = pct + '% Complete';
            setPercentWidthClass(progressBar, pct);
        } else if (step === totalQuestions + 1) {
            phaseLabel.textContent = 'Final Step';
            title.textContent = 'Claim Your Results';
            stepLabel.textContent = '';
            percentLabel.textContent = '100% Complete';
            setPercentWidthClass(progressBar, 100);
        } else if (step === totalQuestions + 2) {
            phaseLabel.textContent = 'Assessment Complete';
            title.textContent = 'Your Results';
            stepLabel.textContent = '';
            percentLabel.textContent = 'Complete';
            setPercentWidthClass(progressBar, 100);
        }
    }

    function goToStep(step) {
        var allSteps = document.querySelectorAll('.quiz-step');
        allSteps.forEach(function (stepElement) {
            stepElement.classList.remove('active');
        });

        var target = document.querySelector('[data-step="' + step + '"]');
        if (target) {
            target.classList.add('active');
        }

        currentStep = step;
        updateHeader(step);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateCurrentQuestionSelection(step) {
        var questionData = getQuestionByStep(step);

        if (!questionData) {
            return true;
        }

        if (quizAnswers[questionData.id]) {
            return true;
        }

        if (window.BiogenixToast) {
            window.BiogenixToast.show('Please select one answer before moving ahead.', 'warning');
        }

        return false;
    }

    function nextStep(step) {
        if (currentStep <= totalQuestions && !validateCurrentQuestionSelection(currentStep)) {
            return;
        }

        goToStep(step);
    }

    function prevStep(step) {
        goToStep(step);
    }

    function clearFieldValidationState(field) {
        if (!field) {
            return;
        }

        field.classList.remove('border-primary-600', 'ring-2', 'ring-primary-600/10');
    }

    function markFieldInvalid(field) {
        if (!field) {
            return;
        }

        field.classList.add('border-primary-600', 'ring-2', 'ring-primary-600/10');
    }

    function validateLeadDetails() {
        var firstNameField = document.getElementById('quizFirstName');
        var lastNameField = document.getElementById('quizLastName');
        var emailField = document.getElementById('quizEmail');
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        clearFieldValidationState(firstNameField);
        clearFieldValidationState(lastNameField);
        clearFieldValidationState(emailField);

        if (!firstNameField || firstNameField.value.trim() === '') {
            markFieldInvalid(firstNameField);

            if (window.BiogenixToast) {
                window.BiogenixToast.show('Please enter the first name before submitting the quiz.', 'warning');
            }

            return false;
        }

        if (!emailField || !emailPattern.test(emailField.value.trim())) {
            markFieldInvalid(emailField);

            if (window.BiogenixToast) {
                window.BiogenixToast.show('Please enter a valid email address before submitting the quiz.', 'warning');
            }

            return false;
        }

        return true;
    }

    function buildSelectedAnswersPayload() {
        var selectedAnswers = {};

        quizQuestions.forEach(function (questionData) {
            if (quizAnswers[questionData.id]) {
                selectedAnswers[questionData.id] = quizAnswers[questionData.id];
            }
        });

        return selectedAnswers;
    }

    function toggleSubmitButtonState(isSubmitting) {
        var submitButton = document.getElementById('quizSubmitButton');

        if (!submitButton) {
            return;
        }

        if (!quizSubmitButtonDefaultHtml) {
            quizSubmitButtonDefaultHtml = submitButton.innerHTML;
        }

        submitButton.disabled = isSubmitting;
        submitButton.classList.toggle('cursor-not-allowed', isSubmitting);
        submitButton.classList.toggle('opacity-70', isSubmitting);

        if (isSubmitting) {
            submitButton.innerHTML = 'Submitting Quiz...';
            return;
        }

        submitButton.innerHTML = quizSubmitButtonDefaultHtml;
    }

    function applyQuizResult(resultData) {
        var resultTitle = document.getElementById('quizResultTitle');
        var resultDescription = document.getElementById('quizResultDescription');
        var voucherCode = document.getElementById('voucherCode');
        var performanceBreakdown = Array.isArray(resultData.performance_breakdown) ? resultData.performance_breakdown : [];

        if (resultTitle) {
            resultTitle.innerHTML = resultData.result_title_html || 'Assessment Complete';
        }

        if (resultDescription) {
            resultDescription.textContent = resultData.result_description || '';
        }

        if (voucherCode) {
            voucherCode.textContent = resultData.reward_coupon_code || '';
            currentVoucherCode = voucherCode.textContent;
        }

        for (var index = 0; index < 3; index++) {
            var performanceItem = performanceBreakdown[index] || { label: 'Result Segment', percentage: 0 };
            var perfLabel = document.getElementById('quizPerfLabel' + (index + 1));
            var perfValue = document.getElementById('quizPerfValue' + (index + 1));
            var perfBar = document.getElementById('quizPerfBar' + (index + 1));

            if (perfLabel) perfLabel.textContent = performanceItem.label || 'Result Segment';
            if (perfValue) perfValue.textContent = String(performanceItem.percentage || 0) + '%';
            if (perfBar) {
                perfBar.setAttribute('data-perf', String(performanceItem.percentage || 0));
                setPercentWidthClass(perfBar, 0);
            }
        }
    }

    function animateQuizResults(scorePercentage) {
        setTimeout(function () {
            var score = Number(scorePercentage || 0);
            var circumference = 534;
            var offset = circumference - (score / 100) * circumference;
            var ring = document.getElementById('scoreRingFill');
            var scoreValue = document.getElementById('scoreValue');

            if (ring) {
                ring.setAttribute('stroke-dashoffset', String(offset));
            }

            var current = 0;
            var interval = setInterval(function () {
                current += 1;

                if (current > score) {
                    clearInterval(interval);
                    return;
                }

                if (scoreValue) {
                    scoreValue.textContent = current + '%';
                }
            }, 15);

            var performanceBars = [
                document.getElementById('quizPerfBar1'),
                document.getElementById('quizPerfBar2'),
                document.getElementById('quizPerfBar3'),
            ];

            performanceBars.forEach(function (performanceBar) {
                if (!performanceBar) {
                    return;
                }

                setPercentWidthClass(performanceBar, performanceBar.getAttribute('data-perf'));
            });
        }, 300);
    }

    async function showResults() {
        if (totalQuestions === 0) {
            if (window.BiogenixToast) {
                window.BiogenixToast.show('Quiz questions are not available right now.', 'error');
            }

            return;
        }

        for (var stepNumber = 1; stepNumber <= totalQuestions; stepNumber++) {
            if (!validateCurrentQuestionSelection(stepNumber)) {
                goToStep(stepNumber);
                return;
            }
        }

        if (!validateLeadDetails()) {
            return;
        }

        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        var firstNameField = document.getElementById('quizFirstName');
        var lastNameField = document.getElementById('quizLastName');
        var emailField = document.getElementById('quizEmail');

        toggleSubmitButtonState(true);

        try {
            var response = await fetch(quizSubmitUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    participant_first_name: firstNameField ? firstNameField.value.trim() : '',
                    participant_last_name: lastNameField ? lastNameField.value.trim() : '',
                    participant_email: emailField ? emailField.value.trim() : '',
                    selected_answers: buildSelectedAnswersPayload(),
                }),
            });

            var result = await response.json();

            if (!response.ok || result.status !== 'success') {
                if (window.BiogenixToast) {
                    window.BiogenixToast.show(result.message || 'Unable to submit the quiz right now.', 'error');
                }

                return;
            }

            applyQuizResult(result.data || {});
            goToStep(totalQuestions + 2);
            animateQuizResults((result.data || {}).score_percentage || 0);
        } catch (error) {
            if (window.BiogenixToast) {
                window.BiogenixToast.show('Unable to submit the quiz right now. Please try again.', 'error');
            }
        } finally {
            toggleSubmitButtonState(false);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        hydrateQuizContent();
    });

    function copyVoucher() {
        var code = document.getElementById('voucherCode');
        if (!code) return;

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code.textContent);
        } else {
            var range = document.createRange();
            range.selectNodeContents(code);
            var selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(range);
            document.execCommand('copy');
            selection.removeAllRanges();
        }

        if (window.BiogenixToast) {
            window.BiogenixToast.show('Voucher code copied! Use ' + currentVoucherCode + ' at checkout.', 'success');
        }
    }
</script>
@endpush
