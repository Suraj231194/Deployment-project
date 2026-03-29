@extends('admin.layout')

@section('title', 'UI Fields Modification - Biogenix Admin')

@section('admin_content')

    {{-- Back Arrow + Breadcrumb --}}
    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('admin.support-tickets') }}" class="ajax-link h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 bg-white hover:bg-slate-50 hover:border-slate-300 transition shrink-0 cursor-pointer" title="Back to Support Tickets">
            <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <nav class="flex items-center text-[11px] font-bold uppercase tracking-widest text-slate-400 gap-2">
            <a href="{{ route('admin.dashboard') }}" class="ajax-link hover:text-slate-700 transition cursor-pointer">Configuration</a>
            <svg class="h-3 w-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('admin.support-tickets') }}" class="ajax-link hover:text-slate-700 transition cursor-pointer">Support Ticket System</a>
            <svg class="h-3 w-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            <span class="text-slate-600">UI Fields Modification</span>
        </nav>
    </div>

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">UI Fields Modification</h1>
            <p class="text-sm text-slate-500 mt-1">Customize ticket forms, define custom fields, and set global data validation rules.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <button id="btn-discard" class="px-5 py-2.5 rounded-lg text-sm font-bold border border-slate-200 text-slate-700 bg-white hover:bg-slate-50 transition cursor-pointer">
                Discard Changes
            </button>
            <button id="btn-save-config" onclick="saveConfiguration()" class="bg-primary-600 hover:bg-primary-700 transition text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md shadow-primary-600/20 cursor-pointer">
                Save Configuration
            </button>
        </div>
    </div>

    {{-- ─── Standard Fields ─── --}}
    <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden mb-5">

        <div class="px-6 py-5 border-b border-slate-100">
            <h2 class="text-[15px] font-extrabold text-slate-900">Standard Fields</h2>
            <p class="text-[13px] text-slate-500 mt-0.5">Manage visibility and requirement status for core system fields.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left whitespace-nowrap">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest w-1/2">Field Name</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Field Type</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Visible</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Required</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">

                    @php
                    $stdFields = [
                        ['icon' => 'M4 6h16M4 10h16M4 14h8', 'name' => 'Ticket Subject',  'type' => 'Text Input',          'visible' => true, 'required' => true,  'locked' => true],
                        ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'name' => 'Description',     'type' => 'Rich Text Area',      'visible' => true, 'required' => true,  'locked' => false],
                        ['icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z', 'name' => 'Category',        'type' => 'Dropdown Selection',  'visible' => true, 'required' => true,  'locked' => false],
                        ['icon' => 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13', 'name' => 'Attachments',     'type' => 'File Upload',         'visible' => true, 'required' => false, 'locked' => false],
                    ];
                    @endphp

                    @foreach($stdFields as $i => $f)
                    <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['icon'] }}"/>
                                </svg>
                                <span class="text-[13px] font-bold text-slate-900">{{ $f['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-[13px] text-slate-600 font-medium">{{ $f['type'] }}</td>
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" {{ $f['visible'] ? 'checked' : '' }} {{ $f['locked'] ? 'disabled' : '' }}
                                class="h-4 w-4 rounded border-slate-300 text-primary-800 focus:ring-primary-600 accent-primary-600 cursor-pointer {{ $f['locked'] ? 'opacity-60 cursor-not-allowed' : '' }}">
                        </td>
                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" {{ $f['required'] ? 'checked' : '' }} {{ $f['locked'] ? 'disabled' : '' }}
                                class="h-4 w-4 rounded border-slate-300 text-primary-800 focus:ring-primary-600 accent-primary-600 cursor-pointer {{ $f['locked'] ? 'opacity-60 cursor-not-allowed' : '' }}">
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($f['locked'])
                                <span class="text-[11px] font-bold text-slate-400 italic tracking-wide">SYSTEM LOCK</span>
                            @else
                                <x-ui.action-icon type="edit" class="ml-auto" title="Settings">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </x-ui.action-icon>
                            @endif
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>

    {{-- ─── Custom Attributes + Validation Rules ─── --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

        {{-- Left: Custom Attributes --}}
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-[15px] font-extrabold text-slate-900">Custom Attributes</h2>
                    <p class="text-[13px] text-slate-500 mt-0.5">Define additional metadata for specific ticket types.</p>
                </div>
                <button id="btn-add-attr" onclick="openAddAttributeModal()" class="bg-primary-600 hover:bg-primary-700 transition text-white px-4 py-2 rounded-lg text-[13px] font-bold flex items-center gap-2 shrink-0 cursor-pointer">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add New Attribute
                </button>
            </div>

            <div class="space-y-3" id="custom-attributes-list">

                {{-- Attribute 1: Lab Reference ID --}}
                <div class="flex items-start gap-3 border border-slate-100 rounded-xl p-4 bg-slate-50 group hover:border-slate-200 transition" data-attr-id="1">
                    <div class="flex items-center gap-1.5 mt-0.5 shrink-0 cursor-grab text-slate-300 hover:text-slate-400">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 110 4 2 2 0 010-4zM7 8a2 2 0 110 4 2 2 0 010-4zM7 14a2 2 0 110 4 2 2 0 010-4zM13 2a2 2 0 110 4 2 2 0 010-4zM13 8a2 2 0 110 4 2 2 0 010-4zM13 14a2 2 0 110 4 2 2 0 010-4z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[12px] font-extrabold text-slate-900 uppercase tracking-wide">Lab Reference ID</span>
                            <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-600 text-[9px] font-extrabold rounded uppercase tracking-wide">Alphanumeric</span>
                        </div>
                        <p class="text-[12px] text-slate-500">Required for 'Technical Support' and 'Lab Results' categories.</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="flex flex-col items-center gap-0.5">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Visible</span>
                            <input type="checkbox" checked class="h-4 w-4 rounded border-slate-300 accent-primary-600 cursor-pointer">
                        </div>
                        <x-ui.action-icon type="delete">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </x-ui.action-icon>
                        <x-ui.action-icon type="edit">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                        </x-ui.action-icon>
                    </div>
                </div>

                {{-- Attribute 2: Product Version --}}
                <div class="flex items-start gap-3 border border-slate-100 rounded-xl p-4 bg-slate-50 group hover:border-slate-200 transition" data-attr-id="2">
                    <div class="flex items-center gap-1.5 mt-0.5 shrink-0 cursor-grab text-slate-300 hover:text-slate-400">
                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 110 4 2 2 0 010-4zM7 8a2 2 0 110 4 2 2 0 010-4zM7 14a2 2 0 110 4 2 2 0 010-4zM13 2a2 2 0 110 4 2 2 0 010-4zM13 8a2 2 0 110 4 2 2 0 010-4zM13 14a2 2 0 110 4 2 2 0 010-4z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[12px] font-extrabold text-slate-900 uppercase tracking-wide">Product Version</span>
                            <span class="inline-flex items-center px-2 py-0.5 bg-violet-50 text-violet-600 text-[9px] font-extrabold rounded uppercase tracking-wide">Selectable</span>
                        </div>
                        <p class="text-[12px] text-slate-500">Optional field for all software-related inquiries.</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="flex flex-col items-center gap-0.5">
                            <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Visible</span>
                            <input type="checkbox" checked class="h-4 w-4 rounded border-slate-300 accent-primary-600 cursor-pointer">
                        </div>
                        <x-ui.action-icon type="delete" title="Delete">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </x-ui.action-icon>
                        <x-ui.action-icon type="edit" title="Edit">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                        </x-ui.action-icon>
                    </div>
                </div>

            </div>
        </div>

        {{-- Right: Validation Rules --}}
        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 p-5">
            <div class="flex items-center gap-2 mb-5">
                <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-[11px] font-extrabold uppercase tracking-widest text-slate-700">Validation Rules</span>
            </div>

            {{-- Min Description Length --}}
            <div class="mb-5">
                <label class="block text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Min. Description Length</label>
                <div class="flex items-center gap-2">
                    <input type="number" value="20" min="0" class="flex-1 bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-[13px] font-bold text-slate-900 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition">
                    <span class="text-[12px] font-semibold text-slate-500 whitespace-nowrap">chars</span>
                </div>
            </div>

            {{-- Max Attachment Size --}}
            <div class="mb-5">
                <label class="block text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Max. Attachment Size</label>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1">
                        <select class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-[13px] font-bold text-slate-900 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition pr-7 cursor-pointer">
                            <option>5 MB</option>
                            <option selected>10 MB</option>
                            <option>20 MB</option>
                            <option>50 MB</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-2 flex items-center">
                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </div>
                    <span class="text-[12px] font-semibold text-slate-500 whitespace-nowrap">per file</span>
                </div>
            </div>

            {{-- Allowed File Formats --}}
            <div>
                <label class="block text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-2">Allowed File Formats</label>
                <div class="flex flex-wrap gap-2" id="file-formats-list">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg text-[12px] font-bold border border-slate-200">
                        .PDF
                        <button onclick="removeFormat(this)" class="text-slate-400 hover:text-rose-500 transition ml-0.5 cursor-pointer">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg text-[12px] font-bold border border-slate-200">
                        .JPG
                        <button onclick="removeFormat(this)" class="text-slate-400 hover:text-rose-500 transition ml-0.5 cursor-pointer">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg text-[12px] font-bold border border-slate-200">
                        .PNG
                        <button onclick="removeFormat(this)" class="text-slate-400 hover:text-rose-500 transition ml-0.5 cursor-pointer">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg text-[12px] font-bold border border-slate-200">
                        .LOG
                        <button onclick="removeFormat(this)" class="text-slate-400 hover:text-rose-500 transition ml-0.5 cursor-pointer">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </span>
                    <button id="btn-add-format" onclick="addFormat()" class="inline-flex items-center gap-1 px-2.5 py-1 border border-dashed border-slate-300 text-slate-400 rounded-lg text-[12px] font-bold hover:border-primary-600 hover:text-primary-800 transition cursor-pointer">
                        + Add
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ─── Add Attribute Modal ─── --}}
    <div id="add-attr-modal" class="fixed inset-0 z-[1000] flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm cursor-pointer" onclick="closeAddAttributeModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6" style="animation: attr-fade 0.2s ease-out forwards;">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-[15px] font-extrabold text-slate-900">Add Custom Attribute</h3>
                <button onclick="closeAddAttributeModal()" class="h-8 w-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="block text-[12px] font-bold text-slate-600 mb-1">Attribute Name</label>
                    <input id="new-attr-name" type="text" placeholder="e.g. Lab Reference ID" class="w-full bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-lg px-3 py-2.5 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition placeholder:text-slate-400">
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-slate-600 mb-1">Data Type</label>
                    <select id="new-attr-type" class="w-full bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-lg px-3 py-2.5 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition">
                        <option value="alphanumeric">Alphanumeric</option>
                        <option value="selectable">Selectable</option>
                        <option value="numeric">Numeric</option>
                        <option value="boolean">Boolean</option>
                        <option value="date">Date</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[12px] font-bold text-slate-600 mb-1">Description</label>
                    <input id="new-attr-desc" type="text" placeholder="Brief description of this field..." class="w-full bg-slate-50 border border-slate-200 text-[13px] font-semibold text-slate-700 rounded-lg px-3 py-2.5 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition placeholder:text-slate-400">
                </div>
                <div class="flex items-center gap-2 pt-1">
                    <input id="new-attr-visible" type="checkbox" checked class="h-4 w-4 rounded border-slate-300 accent-primary-600 cursor-pointer">
                    <label for="new-attr-visible" class="text-[13px] font-semibold text-slate-700 cursor-pointer">Visible by default</label>
                </div>
            </div>
            <div class="mt-5 flex gap-3 justify-end">
                <button onclick="closeAddAttributeModal()" class="px-5 py-2.5 rounded-xl text-sm font-bold border border-slate-200 text-slate-600 hover:bg-slate-50 transition cursor-pointer">Cancel</button>
                <button onclick="createAttribute()" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-primary-600 hover:bg-primary-700 text-white shadow-md shadow-primary-600/20 transition cursor-pointer">Add Attribute</button>
            </div>
        </div>
    </div>

<style>
    @keyframes attr-fade { from { opacity: 0; transform: scale(0.96) translateY(8px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    input[type="checkbox"] { accent-color: #1A4D2E; }
</style>

<script>
(function() {
    // ─── Save Configuration ───
    window.saveConfiguration = function() {
        const btn = document.getElementById('btn-save-config');
        const original = btn.textContent;
        btn.textContent = 'Saving…'; btn.disabled = true;
        setTimeout(() => { btn.textContent = '✓ Saved'; }, 800);
        setTimeout(() => { btn.textContent = original; btn.disabled = false; }, 2200);
    };

    // ─── Discard Changes ───
    document.getElementById('btn-discard')?.addEventListener('click', () => {
        if (confirm('Discard all unsaved changes?')) window.location.reload();
    });

    // ─── Add Attr Modal ───
    window.openAddAttributeModal = function() {
        document.getElementById('add-attr-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        document.getElementById('new-attr-name')?.focus();
    };
    window.closeAddAttributeModal = function() {
        document.getElementById('add-attr-modal').classList.add('hidden');
        document.body.style.overflow = '';
    };

    // ─── Create Attribute ───
    window.createAttribute = function() {
        const name = document.getElementById('new-attr-name').value.trim();
        const type = document.getElementById('new-attr-type').value;
        const desc = document.getElementById('new-attr-desc').value.trim();
        const visible = document.getElementById('new-attr-visible').checked;
        if (!name) { document.getElementById('new-attr-name').focus(); return; }

        const typeColors = { alphanumeric: 'bg-slate-100 text-slate-600', selectable: 'bg-violet-50 text-violet-600', numeric: 'bg-secondary-50 text-secondary-700', boolean: 'bg-primary-50 text-primary-600', date: 'bg-primary-50 text-primary-600' };
        const list = document.getElementById('custom-attributes-list');
        const id = Date.now();
        const div = document.createElement('div');
        div.className = 'flex items-start gap-3 border border-slate-100 rounded-xl p-4 bg-slate-50 group hover:border-slate-200 transition';
        div.setAttribute('data-attr-id', id);
        div.innerHTML = `
            <div class="flex items-center gap-1.5 mt-0.5 shrink-0 cursor-grab text-slate-300 hover:text-slate-400">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M7 2a2 2 0 110 4 2 2 0 010-4zM7 8a2 2 0 110 4 2 2 0 010-4zM7 14a2 2 0 110 4 2 2 0 010-4zM13 2a2 2 0 110 4 2 2 0 010-4zM13 8a2 2 0 110 4 2 2 0 010-4zM13 14a2 2 0 110 4 2 2 0 010-4z"/></svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[12px] font-extrabold text-slate-900 uppercase tracking-wide">${name}</span>
                    <span class="inline-flex items-center px-2 py-0.5 ${typeColors[type] || 'bg-slate-100 text-slate-600'} text-[9px] font-extrabold rounded uppercase tracking-wide">${type}</span>
                </div>
                <p class="text-[12px] text-slate-500">${desc || 'No description provided.'}</p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <div class="flex flex-col items-center gap-0.5">
                    <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Visible</span>
                    <input type="checkbox" ${visible ? 'checked' : ''} class="h-4 w-4 rounded border-slate-300 accent-primary-600 cursor-pointer">
                </div>
                <x-ui.action-icon type="delete" onclick="this.closest('[data-attr-id]').remove()" title="Delete">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </x-ui.action-icon>
                <x-ui.action-icon type="edit" title="Edit">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                </x-ui.action-icon>
            </div>`;
        list.appendChild(div);
        closeAddAttributeModal();
        document.getElementById('new-attr-name').value = '';
        document.getElementById('new-attr-desc').value = '';
    };

    // ─── Remove file format tag ───
    window.removeFormat = function(btn) {
        btn.closest('span')?.remove();
    };

    // ─── Add file format ───
    window.addFormat = function() {
        const ext = prompt('Enter file extension (e.g. .XLSX):');
        if (!ext?.trim()) return;
        const val = ext.trim().toUpperCase().startsWith('.') ? ext.trim().toUpperCase() : '.' + ext.trim().toUpperCase();
        const list = document.getElementById('file-formats-list');
        const addBtn = document.getElementById('btn-add-format');
        const span = document.createElement('span');
        span.className = 'inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 text-slate-800 rounded-lg text-[12px] font-bold border border-slate-200';
        span.innerHTML = `${val}<button onclick="removeFormat(this)" class="text-slate-400 hover:text-rose-500 transition ml-0.5 cursor-pointer"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>`;
        list.insertBefore(span, addBtn);
    };

    // ─── Escape to close modal ───
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeAddAttributeModal(); });
})();
</script>

@endsection
