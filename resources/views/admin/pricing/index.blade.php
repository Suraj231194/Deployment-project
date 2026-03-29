@extends('admin.layout')

@section('title', 'Pricing Configuration - Biogenix Admin')

@section('admin_content')

<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex text-[13px] text-slate-500 font-medium mb-2">
        <a href="{{ route('admin.dashboard') }}" class="ajax-link hover:text-slate-900 transition flex items-center gap-1.5 cursor-pointer">
            Admin
        </a>
        <span class="mx-2 text-slate-300">/</span>
        <span class="text-slate-900 font-semibold">Pricing Management</span>
    </nav>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pricing Configuration</h1>
            <p class="text-sm text-slate-500 mt-1 max-w-2xl">Manage global price matrices, quantity-based discounts, and custom overrides for Biogenix products.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <button onclick="AdminConfirm.show({title:'Discard Draft?',message:'All unsaved pricing changes will be lost.',confirmText:'Discard',danger:true}).then(r=>{if(r)AdminToast.show('Draft discarded','info')})" class="px-5 py-2.5 rounded-lg text-sm font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition shadow-sm cursor-pointer">
                Discard Draft
            </button>
            <button onclick="AdminBtnLoading.start(this);setTimeout(()=>{AdminBtnLoading.stop(this);AdminToast.show('Pricing changes published successfully!','success')},1500)" class="bg-primary-600 hover:bg-primary-700 transition text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow-md shadow-primary-600/20 cursor-pointer">
                Publish Changes
            </button>
        </div>
    </div>

    <!-- Section 1: Customer-Type Base Rates -->
    <div class="space-y-4">
        <div class="flex items-center gap-3 mb-2">
            <svg class="h-5 w-5 text-primary-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <h2 class="text-lg font-bold text-slate-900">Customer-Type Base Rates</h2>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Tier 1 -->
            <div class="bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded-md">Tier 1</span>
                        <svg class="h-5 w-5 text-slate-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Guest / Public</h3>
                    <p class="text-[13px] text-slate-500 font-medium leading-relaxed mb-6">Standard MSRP for unregistered clinic visitors.</p>
                    
                    <div class="flex items-baseline gap-2 mb-8">
                        <span class="text-sm font-semibold text-slate-400">Base Rate:</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tight">100%</span>
                    </div>
                </div>
                
                <div class="pt-5 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[13px] font-semibold text-slate-600">Auto-update MSRP</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer" checked>
                        <div class="w-10 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                </div>
            </div>

            <!-- Tier 2 -->
            <div class="bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-6 bg-primary-600 text-white text-[9px] font-black uppercase tracking-widest px-3 py-1.5 rounded-b-md">Active</div>
                
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-primary-800 text-[10px] font-black uppercase tracking-widest rounded-md">Tier 2</span>
                        <!-- active spacer -->
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2 mt-2">Retail Partners</h3>
                    <p class="text-[13px] text-slate-500 font-medium leading-relaxed mb-6">Standard pricing for pharmacies and local distributors.</p>
                    
                    <div class="flex items-baseline gap-2 mb-8">
                        <span class="text-sm font-semibold text-slate-400">Base Rate:</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tight">85%</span>
                    </div>
                </div>
                
                <div class="pt-5 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[13px] font-semibold text-slate-600">Requires Tax ID</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" value="" class="sr-only peer" checked>
                        <div class="w-10 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    </label>
                </div>
            </div>

            <!-- Tier 3 -->
            <div class="bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-5">
                        <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest rounded-md">Tier 3</span>
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">B2B Institutional</h3>
                    <p class="text-[13px] text-slate-500 font-medium leading-relaxed mb-6">Volume accounts for hospitals and medical research groups.</p>
                    
                    <div class="flex items-baseline gap-2 mb-8">
                        <span class="text-sm font-semibold text-slate-400">Base Rate:</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tight">72%</span>
                    </div>
                </div>
                
                <div class="pt-5 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-[13px] font-bold text-slate-900">Negotiable Rates</span>
                    <a href="#" class="text-primary-800 hover:text-primary-600 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- Section 2: Quantity Slab Discounts -->
    <div class="pt-2 space-y-4">
        <div class="flex items-center justify-between mb-4 flex-wrap gap-4">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 text-primary-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <h2 class="text-lg font-bold text-slate-900">Quantity Slab Discounts</h2>
            </div>
            <button type="button" class="text-[13px] font-bold text-primary-800 hover:text-primary-800 transition flex items-center gap-1.5 cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                Add Slab
            </button>
        </div>

        <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden mt-2 pb-2">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Quantity Slab</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Guest Discount</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Retail Discount</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">B2B Discount</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100/60 text-[13px] font-bold text-slate-700">
                        <!-- Slab 1 -->
                        <tr class="hover:bg-slate-50 transition cursor-pointer">
                            <td class="px-6 py-5 text-slate-900">1 - 10 units</td>
                            <td class="px-6 py-5">0%</td>
                            <td class="px-6 py-5">0%</td>
                            <td class="px-6 py-5">5%</td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 bg-emerald-100 text-primary-600 text-[10px] font-bold rounded">Standard</span>
                            </td>
                            <td class="px-6 py-5 text-right flex justify-end">
                                <x-ui.action-icon type="edit">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                </x-ui.action-icon>
                            </td>
                        </tr>
                        <!-- Slab 2 -->
                        <tr class="hover:bg-slate-50 transition cursor-pointer">
                            <td class="px-6 py-5 text-slate-900">11 - 50 units</td>
                            <td class="px-6 py-5">2.5%</td>
                            <td class="px-6 py-5">5%</td>
                            <td class="px-6 py-5">12%</td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded">Bulk Tier 1</span>
                            </td>
                            <td class="px-6 py-5 text-right flex justify-end">
                                <x-ui.action-icon type="edit">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                </x-ui.action-icon>
                            </td>
                        </tr>
                        <!-- Slab 3 -->
                        <tr class="hover:bg-slate-50 transition cursor-pointer">
                            <td class="px-6 py-5 text-slate-900">51 - 250 units</td>
                            <td class="px-6 py-5">5%</td>
                            <td class="px-6 py-5">10%</td>
                            <td class="px-6 py-5">20%</td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 bg-fuchsia-100 text-fuchsia-700 text-[10px] font-bold rounded">Bulk Tier 2</span>
                            </td>
                            <td class="px-6 py-5 text-right flex justify-end">
                                <x-ui.action-icon type="edit">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                </x-ui.action-icon>
                            </td>
                        </tr>
                        <!-- Slab 4 -->
                        <tr class="hover:bg-slate-50 transition cursor-pointer">
                            <td class="px-6 py-5 text-slate-900">251+ units</td>
                            <td class="px-6 py-5">10%</td>
                            <td class="px-6 py-5">15%</td>
                            <td class="px-6 py-5">35%</td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 bg-amber-100 text-secondary-700 text-[10px] font-bold rounded">Enterprise</span>
                            </td>
                            <td class="px-6 py-5 text-right flex justify-end">
                                <x-ui.action-icon type="edit">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                </x-ui.action-icon>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 3: Special Pricing Overrides -->
    <div class="pt-2 space-y-4">
        <div class="flex items-center gap-3 mb-2">
            <svg class="h-5 w-5 text-primary-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <h2 class="text-lg font-bold text-slate-900">Special Pricing Overrides</h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- SKU Override Card -->
            <div class="bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-start gap-4 mb-6">
                        <div class="h-10 w-10 rounded-xl bg-slate-100 text-primary-800 flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 border-b-0 pb-0 mb-1">SKU-Specific Override</h3>
                            <p class="text-[12px] text-slate-500 font-medium leading-relaxed">Set custom rates for specific reagents or equipment.</p>
                        </div>
                    </div>

                    <!-- List -->
                    <div class="space-y-4 mb-8">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div>
                                <p class="text-[13px] font-bold text-slate-900">BGX-7740 (Cryo-Serum)</p>
                                <p class="text-[11px] font-semibold text-slate-400 mt-0.5">Global Flat Rate</p>
                            </div>
                            <span class="text-base font-black text-slate-900">$449.00</span>
                        </div>
                        
                        <div class="flex items-center justify-between pb-1">
                            <div>
                                <p class="text-[13px] font-bold text-slate-900">LYS-9921 (Lysis Buffer)</p>
                                <p class="text-[11px] font-semibold text-slate-400 mt-0.5">Retail Exemption</p>
                            </div>
                            <span class="text-[13px] font-bold text-slate-900">No Discount</span>
                        </div>
                    </div>
                </div>
                
                <button class="w-full mt-auto py-2.5 rounded-lg text-[13px] font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition border border-slate-200 cursor-pointer">
                    Manage SKU Rules
                </button>
            </div>

            <!-- Campaign Override Card -->
            <div class="bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-start gap-4 mb-6">
                        <div class="h-10 w-10 rounded-xl bg-slate-100 text-primary-800 flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 border-b-0 pb-0 mb-1">Campaign & Seasonal Overrides</h3>
                            <p class="text-[12px] text-slate-500 font-medium leading-relaxed">Scheduled pricing changes for research symposiums.</p>
                        </div>
                    </div>

                    <!-- List -->
                    <div class="space-y-5 mb-8 border-l-[3px] border-primary-600 pl-4">
                        
                        <div class="pb-1 text-left">
                            <p class="text-[13px] font-bold text-slate-900 mb-1 leading-tight">Summer Science Expo</p>
                            <p class="text-[12px] text-slate-500 font-medium leading-snug mb-2">15% Additional discount for B2B. Active Jul 01 - Aug 15.</p>
                            <span class="inline-flex items-center px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[9px] font-black uppercase tracking-widest rounded">Scheduled</span>
                        </div>
                        
                        <div class="pb-1 text-left pt-2 border-t border-slate-100/50">
                            <p class="text-[13px] font-bold text-slate-900 mb-1 leading-tight">Year-End Research Grants</p>
                            <p class="text-[12px] text-slate-500 font-medium leading-snug mb-2">Flat 20% off Guest Tier. Active Nov 15 - Dec 31.</p>
                            <span class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-500 text-[9px] font-black uppercase tracking-widest rounded">Draft</span>
                        </div>
                        
                    </div>
                </div>
                
                <button class="w-full mt-auto py-2.5 rounded-lg text-[13px] font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 transition border border-slate-200 cursor-pointer">
                    Create Campaign
                </button>
            </div>

        </div>
    </div>

</div>

@endsection
