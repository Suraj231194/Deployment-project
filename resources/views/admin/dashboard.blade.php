@extends('admin.layout')

@section('title', 'Admin Dashboard - Biogenix')

@section('admin_content')
            
            <!-- Welcome Header -->
            <div class="mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Overview</h2>
                    <p class="text-sm text-slate-500 mt-1">Welcome back. Here's what's happening in your biogenic supply chain today.</p>
                </div>
                
                <!-- Quick Search -->
                <div class="relative w-full md:w-80">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" placeholder="Search analytics or orders..." class="w-full bg-white border border-slate-200 shadow-sm text-sm rounded-xl pl-9 pr-4 py-2.5 focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-700 placeholder:text-slate-400">
                </div>
            </div>

            <!-- KPI Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                <!-- Card 1 -->
                <a href="{{ route('admin.orders') }}" class="ajax-link bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between h-[130px] lg:h-[140px] hover:shadow-[0_4px_16px_-3px_rgba(6,81,237,0.15)] transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-100 text-primary-800">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 00-2-2m4-10a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2V9z"></path></svg>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-md mb-auto mt-0.5">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            +12%
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-[12px] lg:text-[13px] font-semibold text-slate-500 mb-0.5">Total Orders</p>
                        <h3 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">1,284</h3>
                    </div>
                </a>

                <!-- Card 2 -->
                <a href="{{ route('admin.orders') }}" class="ajax-link bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between h-[130px] lg:h-[140px] hover:shadow-[0_4px_16px_-3px_rgba(6,81,237,0.15)] transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-md mb-auto mt-0.5">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            +5%
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="text-[12px] lg:text-[13px] font-semibold text-slate-500 mb-0.5">Today's Orders</p>
                        <h3 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">142</h3>
                    </div>
                </a>

                <!-- Card 3 -->
                <a href="{{ route('admin.orders') }}" class="ajax-link bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between h-[130px] lg:h-[140px] hover:shadow-[0_4px_16px_-3px_rgba(6,81,237,0.15)] transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-secondary-50 text-secondary-700">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2v1l2 2m-3 5h3m-3 4h3"></path></svg>
                        </div>
                        <div class="text-[11px] font-semibold text-slate-400 mb-auto mt-2">Current</div>
                    </div>
                    <div class="mt-2">
                        <p class="text-[12px] lg:text-[13px] font-semibold text-slate-500 mb-0.5">Pending Dispatch</p>
                        <h3 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">48</h3>
                    </div>
                </a>

                <!-- Card 4 -->
                <a href="{{ route('admin.orders') }}" class="ajax-link bg-white rounded-2xl p-5 lg:p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col justify-between h-[130px] lg:h-[140px] hover:shadow-[0_4px_16px_-3px_rgba(6,81,237,0.15)] transition-shadow cursor-pointer">
                    <div class="flex items-center justify-between">
                        <div class="h-10 w-10 flex items-center justify-center rounded-xl bg-rose-50 text-rose-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-md mb-auto mt-0.5">! Urgent</div>
                    </div>
                    <div class="mt-2">
                        <p class="text-[12px] lg:text-[13px] font-semibold text-slate-500 mb-0.5">Same-day Delivery</p>
                        <h3 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight">12</h3>
                    </div>
                </a>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Revenue Snapshot -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 lg:p-7 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 h-auto sm:h-[400px] flex flex-col justify-between relative overflow-hidden">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Revenue Snapshot</h3>
                            <p class="text-[11px] lg:text-xs font-semibold text-slate-400 mt-1">Global performance tracking</p>
                            <div class="mt-4 flex flex-wrap items-center gap-3 lg:gap-4">
                                <h2 class="text-3xl lg:text-4xl font-extrabold text-primary-800 tracking-tight">$248,590.00</h2>
                                <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-md h-fit">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                    18.2%
                                </span>
                            </div>
                        </div>
                        <!-- Toggle -->
                        <div id="chartToggle" class="bg-slate-100 rounded-lg p-1 flex items-center shadow-inner self-start">
                            <button data-period="weekly" class="chart-toggle-btn px-3 lg:px-4 py-1.5 text-[11px] font-bold text-slate-500 rounded-md transition hover:text-slate-800 cursor-pointer">Weekly</button>
                            <button data-period="monthly" class="chart-toggle-btn px-3 lg:px-4 py-1.5 text-[11px] font-bold text-primary-800 bg-white rounded-md shadow-sm cursor-pointer">Monthly</button>
                            <button data-period="yearly" class="chart-toggle-btn px-3 lg:px-4 py-1.5 text-[11px] font-bold text-slate-500 rounded-md transition hover:text-slate-800 cursor-pointer">Yearly</button>
                        </div>
                    </div>

                    <!-- Fake Chart Area -->
                    <div id="chartBars" class="mt-8 flex-1 grid grid-cols-7 gap-2 lg:gap-4 items-end px-1 lg:px-2 pt-10 min-h-[150px]">
                        <div class="w-full bg-slate-100 rounded-t-sm h-[30%]"></div>
                        <div class="w-full bg-slate-100 rounded-t-sm h-[45%]"></div>
                        <div class="w-full bg-slate-100 rounded-t-sm h-[35%]"></div>
                        <div class="w-full bg-slate-100 rounded-t-sm h-[55%]"></div>
                        <div class="w-full bg-slate-100 rounded-t-sm h-[40%]"></div>
                        <div class="w-full bg-slate-100 rounded-t-sm h-[60%]"></div>
                        <div class="w-full bg-primary-600 rounded-t-sm h-[85%] shadow-[0_0_15px_rgba(9,27,63,0.3)]"></div>
                    </div>
                    
                    <!-- Chart Labels -->
                    <div class="grid grid-cols-7 gap-2 lg:gap-4 mt-4 px-1 lg:px-2 text-center text-[9px] lg:text-[10px] font-bold text-slate-400">
                        <span>MON</span>
                        <span>TUE</span>
                        <span>WED</span>
                        <span>THU</span>
                        <span>FRI</span>
                        <span>SAT</span>
                        <span>SUN</span>
                    </div>
                </div>

                <!-- Fulfillment Distribution -->
                <div class="bg-white rounded-2xl p-6 lg:p-7 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 flex flex-col">
                    <h3 class="text-base font-bold text-slate-900 mb-6">Fulfillment Distribution</h3>
                    
                    <div class="space-y-6 flex-1">
                        <!-- Bar 1 -->
                        <div>
                            <div class="flex justify-between text-[11px] lg:text-[12px] font-semibold mb-2">
                                <span class="text-slate-700">Direct Delivery</span>
                                <span class="text-slate-500">62%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="h-2 w-[62%] rounded-full bg-primary-600"></div>
                            </div>
                        </div>

                        <!-- Bar 2 -->
                        <div>
                            <div class="flex justify-between text-[11px] lg:text-[12px] font-semibold mb-2">
                                <span class="text-slate-700">Express Logistics</span>
                                <span class="text-slate-500">28%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="h-2 w-[28%] rounded-full bg-primary-600"></div>
                            </div>
                        </div>

                        <!-- Bar 3 -->
                        <div>
                            <div class="flex justify-between text-[11px] lg:text-[12px] font-semibold mb-2">
                                <span class="text-slate-700">Courier Partners</span>
                                <span class="text-slate-500">10%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="h-2 w-[10%] rounded-full bg-primary-600"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Box -->
                    <div class="mt-8 bg-slate-100 border border-blue-100 rounded-xl p-4 lg:p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="h-4 w-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                            <span class="text-[10px] lg:text-[11px] font-bold text-primary-800 uppercase tracking-wider">Storage Alert</span>
                        </div>
                        <p class="text-[11px] lg:text-[12px] text-slate-600 leading-relaxed mb-3">Warehouse B-4 is reaching 90% capacity for temperature-sensitive biologics. Immediate dispatch recommended.</p>
                        <a href="#" class="text-[11px] lg:text-[12px] font-bold text-primary-800 hover:underline underline-offset-2">View Capacity Report</a>
                    </div>
                </div>
            </div>

            <!-- Priority Orders Table -->
            <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden mt-6">
                <div class="px-5 lg:px-7 py-5 lg:py-6 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Priority Orders</h3>
                    <a href="{{ route('admin.orders') }}" class="ajax-link text-[12px] lg:text-[13px] font-bold text-primary-800 hover:underline underline-offset-2 cursor-pointer">View All Orders</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse whitespace-nowrap">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Order ID</th>
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Client</th>
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Biogenic Type</th>
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Value</th>
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100">Status</th>
                                <th class="px-5 lg:px-7 py-3 lg:py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100/80 text-[12px] lg:text-[13px] font-semibold text-slate-900">
                            <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer">
                                <td class="px-5 lg:px-7 py-4 lg:py-5">#BGX-9012</td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-6 w-6 lg:h-7 lg:w-7 rounded bg-primary-50 text-primary-600 font-bold flex items-center justify-center text-[10px] lg:text-[11px]">M</div>
                                        MetroLabs Inc.
                                    </div>
                                </td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5 text-slate-600 font-medium">Reagent Kit Alpha</td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5">$4,290</td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5">
                                    <span class="inline-flex items-center px-2 py-1 bg-amber-50 text-amber-700 border border-amber-200/60 text-[8px] lg:text-[9px] font-extrabold uppercase tracking-wider rounded">Dispatching</span>
                                </td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5 text-center">
                                    <div class="relative inline-block">
                                        <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" /></svg></button>
                                        <div class="hidden absolute right-0 top-full mt-1 w-40 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-20">
                                            <a href="{{ route('admin.orders.view') }}" class="ajax-link flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer"><svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> View</a>
                                            <button class="flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition w-full text-left cursor-pointer"><svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg> Edit</button>
                                            <button onclick="AdminConfirm.show({title:'Cancel Order?',message:'This will cancel order #BGX-9012.',confirmText:'Cancel Order'}).then(r=>{if(r)AdminToast.show('Order cancelled','success')})" class="flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-rose-600 hover:bg-rose-50 transition w-full text-left cursor-pointer"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Cancel</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer">
                                <td class="px-5 lg:px-7 py-4 lg:py-5">#BGX-8994</td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-6 w-6 lg:h-7 lg:w-7 rounded bg-primary-50 text-primary-600 font-bold flex items-center justify-center text-[10px] lg:text-[11px]">G</div>
                                        GenTech Solutions
                                    </div>
                                </td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5 text-slate-600 font-medium">Synthetic Enzyme B2</td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5">$12,800</td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5">
                                    <span class="inline-flex items-center px-2 py-1 bg-violet-50 text-violet-700 border border-violet-200/60 text-[8px] lg:text-[9px] font-extrabold uppercase tracking-wider rounded">In Transit</span>
                                </td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5 text-center">
                                    <div class="relative inline-block">
                                        <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg></button>
                                        <div class="hidden absolute right-0 top-full mt-1 w-40 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-20">
                                            <a href="{{ route('admin.orders.view') }}" class="ajax-link flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer"><svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> View</a>
                                            <button class="flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition w-full text-left cursor-pointer"><svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg> Edit</button>
                                            <button class="flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-rose-600 hover:bg-rose-50 transition w-full text-left cursor-pointer"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Cancel</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr class="hover:bg-slate-50/50 transition-colors cursor-pointer">
                                <td class="px-5 lg:px-7 py-4 lg:py-5">#BGX-8851</td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-6 w-6 lg:h-7 lg:w-7 rounded bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-[10px] lg:text-[11px]">U</div>
                                        University Hospital
                                    </div>
                                </td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5 text-slate-600 font-medium">Rapid Test Sets</td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5">$840</td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5">
                                    <span class="inline-flex items-center px-2 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/60 text-[8px] lg:text-[9px] font-extrabold uppercase tracking-wider rounded">Delivered</span>
                                </td>
                                <td class="px-5 lg:px-7 py-4 lg:py-5 text-center">
                                    <div class="relative inline-block">
                                        <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg hover:bg-slate-100 transition cursor-pointer"><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/></svg></button>
                                        <div class="hidden absolute right-0 top-full mt-1 w-40 bg-white border border-slate-200 rounded-xl shadow-lg py-1 z-20">
                                            <a href="{{ route('admin.orders.view') }}" class="ajax-link flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition cursor-pointer"><svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg> View</a>
                                            <button class="flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition w-full text-left cursor-pointer"><svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg> Edit</button>
                                            <button class="flex items-center gap-2 px-4 py-2 text-[13px] font-medium text-rose-600 hover:bg-rose-50 transition w-full text-left cursor-pointer"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg> Cancel</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            </div>

@endsection

@push('scripts')
<script>
// ─── Revenue Toggle ───
const chartData = {
    weekly: [30, 45, 35, 55, 40, 60, 85],
    monthly: [50, 65, 40, 75, 55, 80, 90],
    yearly: [70, 55, 80, 60, 75, 85, 95]
};

document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.chart-toggle-btn').forEach(b => {
            b.classList.remove('text-primary-800', 'bg-white', 'shadow-sm');
            b.classList.add('text-slate-500');
        });
        btn.classList.remove('text-slate-500');
        btn.classList.add('text-primary-800', 'bg-white', 'shadow-sm');

        const bars = document.querySelectorAll('#chartBars > div');
        const data = chartData[btn.dataset.period];
        bars.forEach((bar, i) => {
            bar.style.transition = 'height 0.4s ease';
            bar.style.height = data[i] + '%';
        });
    });
});
</script>
@endpush
