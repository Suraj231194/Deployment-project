@extends('layouts.app')

@php
    $panelClass = 'rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm md:p-8';
    $sectionTitleClass = 'text-xl font-semibold text-slate-950';
    $sectionCopyClass = 'mt-1 text-sm leading-6 text-slate-500';
    $fieldLabelClass = 'mb-2 block text-sm font-semibold text-slate-700';
    $fieldClass = 'h-11 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-900 shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10';
    $textareaClass = 'min-h-[8rem] w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10';
    $primaryButtonClass = 'inline-flex h-11 items-center justify-center rounded-xl bg-primary-600 px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700';
    $secondaryButtonClass = 'inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50';
    $dangerButtonClass = 'inline-flex h-10 items-center justify-center rounded-xl border border-rose-200 bg-rose-50 px-4 text-sm font-semibold text-rose-700 transition hover:bg-rose-100';
    $badgeClass = 'inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold';
    $tableWrapClass = 'overflow-x-auto rounded-2xl border border-slate-200';
    $tableHeadClass = 'bg-slate-50 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500';
    $tableCellClass = 'px-4 py-4 align-top text-sm text-slate-700';
@endphp

@section('title', 'FAQ Management')

@section('content')
    <div class="mx-auto w-full max-w-none space-y-6 px-4 py-6 sm:px-6 lg:px-8 xl:px-10">
        {{-- Business step: keep FAQ summary cards at the top so admins can understand content volume before editing rows. --}}
        <section class="rounded-[32px] border border-slate-200 bg-[linear-gradient(135deg,#ffffff_0%,#f8fbff_58%,#dbeafe_100%)] p-6 shadow-sm md:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Content Operations</p>
                    <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-950 md:text-4xl">FAQ Management</h1>
                    <p class="mt-3 text-sm leading-7 text-slate-600 md:text-base">
                        Manage public FAQ content, control the business display order, and keep one default-open answer ready for first-time visitors.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Total FAQs</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $faqSummary['total_faqs'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Active FAQs</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $faqSummary['active_faqs'] }}</p>
                    </div>
                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">Categories</p>
                        <p class="mt-2 text-2xl font-bold text-slate-950">{{ $faqSummary['categories'] }}</p>
                    </div>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="rounded-2xl border border-primary-200 bg-primary-50 px-4 py-4 text-sm font-medium text-primary-600">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-700">
                <p class="font-semibold">Please resolve the following issues:</p>
                <ul class="mt-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Fresher note: keep create and update in one familiar form so the flow is easy to trace. --}}
        <section class="{{ $panelClass }}">
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5.25v13.5m6.75-6.75H5.25" />
                    </svg>
                </div>
                <div>
                    <h2 class="{{ $sectionTitleClass }}">{{ $editingFaq ? 'Update FAQ' : 'Add FAQ' }}</h2>
                    <p class="{{ $sectionCopyClass }}">Keep category, wording, and sort order clear so both customers and support teams can find answers quickly.</p>
                </div>
            </div>

            <form method="POST" action="{{ $editingFaq ? route('admin.faqs.update', $editingFaq->id) : route('admin.faqs.store') }}" class="mt-6 space-y-5">
                @csrf

                @if ($editingFaq)
                    @method('PUT')
                @endif

                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label for="faq_category" class="{{ $fieldLabelClass }}">Category</label>
                        <select id="faq_category" name="category" class="{{ $fieldClass }}" required>
                            <option value="">Select category</option>
                            @foreach ($faqCategoryOptions as $faqCategoryOption)
                                <option value="{{ $faqCategoryOption }}" @selected(old('category', $editingFaq->category ?? '') === $faqCategoryOption)>
                                    {{ $faqCategoryOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="faq_sort_order" class="{{ $fieldLabelClass }}">Sort Order</label>
                        <input
                            id="faq_sort_order"
                            type="number"
                            name="sort_order"
                            min="0"
                            value="{{ old('sort_order', $editingFaq->sort_order ?? 0) }}"
                            class="{{ $fieldClass }}"
                            required
                        >
                    </div>
                </div>

                <div>
                    <label for="faq_question" class="{{ $fieldLabelClass }}">Question</label>
                    <input
                        id="faq_question"
                        name="question"
                        value="{{ old('question', $editingFaq->question ?? '') }}"
                        class="{{ $fieldClass }}"
                        required
                    >
                </div>

                <div>
                    <label for="faq_answer" class="{{ $fieldLabelClass }}">Answer</label>
                    <textarea id="faq_answer" name="answer" class="{{ $textareaClass }}" required>{{ old('answer', $editingFaq->answer ?? '') }}</textarea>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700">
                        <input type="hidden" name="is_default_open" value="0">
                        <input
                            type="checkbox"
                            name="is_default_open"
                            value="1"
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                            {{ old('is_default_open', $editingFaq?->is_default_open ?? false) ? 'checked' : '' }}
                        >
                        <span>
                            <span class="block font-semibold text-slate-950">Default Open</span>
                            <span class="mt-1 block text-slate-500">Make this the first expanded FAQ on the public page.</span>
                        </span>
                    </label>

                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-700">
                        <input type="hidden" name="is_active" value="0">
                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500"
                            {{ old('is_active', $editingFaq?->is_active ?? true) ? 'checked' : '' }}
                        >
                        <span>
                            <span class="block font-semibold text-slate-950">Active</span>
                            <span class="mt-1 block text-slate-500">Inactive FAQs stay in backend history but are hidden on the public page.</span>
                        </span>
                    </label>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button class="{{ $primaryButtonClass }}" type="submit">
                        {{ $editingFaq ? 'Update FAQ' : 'Add FAQ' }}
                    </button>

                    @if ($editingFaq)
                        <a class="{{ $secondaryButtonClass }}" href="{{ route('admin.faqs.index') }}">Cancel</a>
                    @endif
                </div>
            </form>
        </section>

        {{-- Business step: show the table in the exact backend order requested by the team. --}}
        <section class="{{ $panelClass }}">
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="{{ $sectionTitleClass }}">Existing FAQs</h2>
                    <p class="{{ $sectionCopyClass }}">Review question wording, display order, and activation status before making customer-facing changes.</p>
                </div>
                <a href="{{ route('faq') }}" class="{{ $secondaryButtonClass }}">Open Public FAQ Page</a>
            </div>

            @if ($faqs->count())
                <div class="mt-6 {{ $tableWrapClass }}">
                    <table class="min-w-full divide-y divide-slate-200 bg-white">
                        <thead class="{{ $tableHeadClass }}">
                            <tr>
                                <th class="px-4 py-3">Sr. No.</th>
                                <th class="px-4 py-3">Category</th>
                                <th class="px-4 py-3">Question</th>
                                <th class="px-4 py-3">Answer</th>
                                <th class="px-4 py-3">Sort Order</th>
                                <th class="px-4 py-3">Default Open</th>
                                <th class="px-4 py-3">is_active</th>
                                <th class="px-4 py-3">created_at</th>
                                <th class="px-4 py-3">Updated At</th>
                                <th class="px-4 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($faqs as $faq)
                                <tr>
                                    <td class="{{ $tableCellClass }} font-semibold text-slate-950">{{ $loop->iteration }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $faq->category }}</td>
                                    <td class="{{ $tableCellClass }} font-semibold text-slate-950">{{ $faq->question }}</td>
                                    <td class="{{ $tableCellClass }}">
                                        <div class="max-w-md leading-6 text-slate-600">{{ $faq->answer }}</div>
                                    </td>
                                    <td class="{{ $tableCellClass }}">{{ $faq->sort_order }}</td>
                                    <td class="{{ $tableCellClass }}">
                                        @if ($faq->is_default_open)
                                            <span class="{{ $badgeClass }} border-primary-200 bg-primary-50 text-primary-700">Yes</span>
                                        @else
                                            <span class="{{ $badgeClass }} border-slate-200 bg-slate-50 text-slate-600">No</span>
                                        @endif
                                    </td>
                                    <td class="{{ $tableCellClass }}">
                                        @if ($faq->is_active)
                                            <span class="{{ $badgeClass }} border-primary-200 bg-primary-50 text-primary-600">Active</span>
                                        @else
                                            <span class="{{ $badgeClass }} border-rose-200 bg-rose-50 text-rose-700">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="{{ $tableCellClass }}">{{ $faq->created_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                    <td class="{{ $tableCellClass }}">{{ $faq->updated_at?->format('d M Y, h:i A') ?? '-' }}</td>
                                    <td class="{{ $tableCellClass }}">
                                        <div class="flex flex-wrap gap-2">
                                            <a class="{{ $secondaryButtonClass }}" href="{{ route('admin.faqs.show', $faq->id) }}">Edit</a>
                                            <a class="{{ $secondaryButtonClass }}" href="{{ route('faq') }}#faqAccordion">View Public</a>

                                            <form method="POST" action="{{ route('admin.faqs.delete', $faq->id) }}" onsubmit="return confirm('Delete this FAQ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="{{ $dangerButtonClass }}" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">No FAQ rows found yet.</div>
            @endif
        </section>
    </div>
@endsection
