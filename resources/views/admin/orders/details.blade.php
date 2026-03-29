@extends('admin.layout')

@section('title', 'Order #ORD-7742 - Biogenix Admin')

@section('admin_content')

<div class="space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-2">
        <div>
            <nav class="flex text-[13px] text-slate-500 font-medium mb-3">
                <a href="{{ route('admin.orders') }}" class="ajax-link hover:text-slate-900 transition flex items-center gap-1.5 cursor-pointer">
                    Orders
                </a>
                <span class="mx-2 text-slate-300">/</span>
                <span class="text-slate-900 font-semibold">#ORD-7742</span>
            </nav>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.orders') }}" class="ajax-link h-10 w-10 flex items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 hover:text-slate-900 hover:bg-slate-50 transition shadow-sm cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Order #ORD-7742</h1>
                        <span class="inline-flex items-center px-2.5 py-1 bg-primary-50 text-primary-600 text-[11px] font-bold rounded-full uppercase tracking-wider">Processing</span>
                    </div>
                    <p class="text-[13px] text-slate-500 font-medium mt-1">Placed on Oct 24, 2023 &bull; 2:45 PM</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-3 self-start sm:self-end mt-2 sm:mt-0">
            <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 transition shadow-sm cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Print Invoice
            </button>
            <button id="saveChangesBtn" onclick="AdminBtnLoading.start(this);setTimeout(()=>{AdminBtnLoading.stop(this);AdminToast.show('Changes saved successfully!','success')},1200)" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold bg-primary-600 text-white hover:bg-primary-700 transition shadow-sm cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" /></svg>
                Save Changes
            </button>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-[1fr_360px] gap-6 mt-6">
        
        <!-- Left Column (Order Summary & History) -->
        <div class="space-y-6">
            
            <!-- Order Summary -->
            <div class="bg-white rounded-[20px] shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden flex flex-col">
                <div class="px-5 lg:px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white">
                    <div class="flex items-center gap-2.5">
                        <svg class="h-5 w-5 text-primary-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <h2 class="text-[15px] font-extrabold text-slate-900">Order Summary</h2>
                    </div>
                    <span class="text-[12px] font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-full border border-slate-100">3 Items</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-5 lg:px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Item Name</th>
                                <th class="px-5 lg:px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-widest">SKU</th>
                                <th class="px-5 lg:px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Quantity</th>
                                <th class="px-5 lg:px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Price</th>
                                <th class="px-5 lg:px-6 py-3.5 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 border-b border-slate-100 bg-white">
                            <!-- Item 1 -->
                            <tr class="hover:bg-slate-50/30 transition-colors cursor-pointer">
                                <td class="px-5 lg:px-6 py-4 flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="h-6 w-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                    <span class="text-[14px] font-bold text-slate-900">Genomic Kit A (V3)</span>
                                </td>
                                <td class="px-5 lg:px-6 py-4 text-[13px] font-semibold text-slate-500">GK-001</td>
                                <td class="px-5 lg:px-6 py-4 text-[13px] font-bold text-slate-900 text-center">2</td>
                                <td class="px-5 lg:px-6 py-4 text-[13px] font-semibold text-slate-600 text-right">$150.00</td>
                                <td class="px-5 lg:px-6 py-4 text-[14px] font-extrabold text-slate-900 text-right">$300.00</td>
                            </tr>
                            <!-- Item 2 -->
                            <tr class="hover:bg-slate-50/30 transition-colors cursor-pointer">
                                <td class="px-5 lg:px-6 py-4 flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="h-6 w-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                    <span class="text-[14px] font-bold text-slate-900">Pipette Tips - High Precision</span>
                                </td>
                                <td class="px-5 lg:px-6 py-4 text-[13px] font-semibold text-slate-500">PT-500</td>
                                <td class="px-5 lg:px-6 py-4 text-[13px] font-bold text-slate-900 text-center">10</td>
                                <td class="px-5 lg:px-6 py-4 text-[13px] font-semibold text-slate-600 text-right">$25.00</td>
                                <td class="px-5 lg:px-6 py-4 text-[14px] font-extrabold text-slate-900 text-right">$250.00</td>
                            </tr>
                            <!-- Item 3 -->
                            <tr class="hover:bg-slate-50/30 transition-colors cursor-pointer">
                                <td class="px-5 lg:px-6 py-4 flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="h-6 w-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                                    </div>
                                    <span class="text-[14px] font-bold text-slate-900">Lysis Buffer Solution (500ml)</span>
                                </td>
                                <td class="px-5 lg:px-6 py-4 text-[13px] font-semibold text-slate-500">BS-09</td>
                                <td class="px-5 lg:px-6 py-4 text-[13px] font-bold text-slate-900 text-center">5</td>
                                <td class="px-5 lg:px-6 py-4 text-[13px] font-semibold text-slate-600 text-right">$40.00</td>
                                <td class="px-5 lg:px-6 py-4 text-[14px] font-extrabold text-slate-900 text-right">$200.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Totals -->
                <div class="px-5 lg:px-6 py-5 bg-white flex flex-col items-end gap-3 z-10 relative">
                    <div class="w-full max-w-[280px]">
                        <div class="flex items-center justify-between py-1.5 w-full">
                            <span class="text-[13px] font-bold text-slate-500">Subtotal</span>
                            <span class="text-[14px] font-extrabold text-slate-900">$750.00</span>
                        </div>
                        <div class="flex items-center justify-between py-1.5 w-full">
                            <span class="text-[13px] font-bold text-slate-500">Shipping</span>
                            <span class="text-[14px] font-extrabold text-slate-900">$45.00</span>
                        </div>
                    </div>
                </div>
                <div class="px-5 lg:px-6 py-5 bg-slate-50 border-t border-slate-100 flex items-center justify-end z-10 relative">
                    <div class="flex items-center justify-between w-full max-w-[280px]">
                        <span class="text-[16px] font-extrabold text-slate-900">Grand Total</span>
                        <span class="text-[18px] font-extrabold text-primary-800">$795.00</span>
                    </div>
                </div>
            </div>

            <!-- Order History -->
            <div class="bg-white rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
                <div class="px-5 lg:px-6 py-4 border-b border-slate-100 flex items-center gap-2.5">
                    <svg class="h-5 w-5 text-slate-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <h2 class="text-[15px] font-extrabold text-slate-900">Order History</h2>
                </div>
                <div class="px-5 lg:px-6 py-6 pl-8">
                    <div class="relative border-l-2 border-slate-100 ml-4 space-y-8 pb-2">
                        
                        <!-- Event 1: Processing -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[17px] top-0 h-[32px] w-[32px] rounded-full bg-white border-2 border-primary-600 flex items-center justify-center">
                                <svg class="h-4 w-4 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            </div>
                            <div class="-mt-0.5">
                                <h3 class="text-[14px] font-bold text-slate-900">Order Processing</h3>
                                <p class="text-[12.5px] font-medium text-slate-500 mt-0.5">Oct 24, 2023 &bull; 3:30 PM &bull; Admin: Sarah J.</p>
                                <div class="mt-3 p-3.5 bg-slate-50 rounded-xl text-[13.5px] text-slate-600 font-medium leading-relaxed border border-slate-100">
                                    Payment confirmed. Lab equipment verified for dispatch. Shipping labels generated.
                                </div>
                            </div>
                        </div>

                        <!-- Event 2: Packed -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[17px] top-0 h-[32px] w-[32px] rounded-full bg-white border-2 border-slate-200 flex items-center justify-center">
                                <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div class="-mt-0.5">
                                <h3 class="text-[14px] font-bold text-slate-900">Items Packed</h3>
                                <p class="text-[12.5px] font-medium text-slate-500 mt-0.5">Oct 24, 2023 &bull; 3:00 PM &bull; Warehouse: Bay C</p>
                                <div class="mt-3 p-3.5 bg-slate-50 rounded-xl text-[13.5px] text-slate-600 font-medium leading-relaxed border border-slate-100">
                                    All 3 items packed with cold-chain compliance. Temperature logger attached.
                                </div>
                            </div>
                        </div>

                        <!-- Event 3: Payment Received -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[17px] top-0 h-[32px] w-[32px] rounded-full bg-white border-2 border-slate-200 flex items-center justify-center">
                                <svg class="h-4 w-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div class="-mt-0.5">
                                <h3 class="text-[14px] font-bold text-slate-900">Payment Received</h3>
                                <p class="text-[12.5px] font-medium text-slate-500 mt-0.5">Oct 24, 2023 &bull; 2:50 PM &bull; Payment Gateway</p>
                            </div>
                        </div>

                        <!-- Event 4: Order Received -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[17px] top-0 h-[32px] w-[32px] rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-[14px] font-bold text-slate-900">Order Received</h3>
                                <p class="text-[12.5px] font-medium text-slate-500 mt-0.5">Oct 24, 2023 &bull; 2:45 PM &bull; System</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column (Status & Customer Info) -->
        <div class="space-y-6">
            
            <!-- Manage Status -->
            <div class="bg-white rounded-[20px] shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden">
                <div class="px-5 lg:px-6 py-4 border-b border-slate-100">
                    <h2 class="text-[15px] font-extrabold text-slate-900">Manage Status</h2>
                </div>
                <div class="p-5 lg:p-6 space-y-5">
                    
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2.5">Order Status</label>
                        <div class="relative">
                            <select class="w-full appearance-none bg-white border border-slate-200 text-sm rounded-xl px-4 py-3 outline-none focus:border-primary-600 focus:ring-1 focus:ring-primary-600 font-semibold text-slate-800 transition shadow-sm hover:border-slate-300">
                                <option>Processing</option>
                                <option>Pending</option>
                                <option>Dispatched</option>
                                <option>Delivered</option>
                                <option>Cancelled</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-slate-400">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 9l4-4 4 4m0 6l-4 4-4-4" /></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-widest mb-2.5">Tracking Code</label>
                        <div class="relative flex gap-2">
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                                </div>
                                <input id="trackingInput" type="text" placeholder="Enter tracking number (e.g. 1Z9...)" class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl pl-10 pr-4 py-3 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 placeholder:text-slate-400 font-medium">
                            </div>
                            <button type="button" onclick="const v=document.getElementById('trackingInput').value;if(v){navigator.clipboard.writeText(v);AdminToast.show('Tracking code copied!','success')}else{AdminToast.show('Enter a tracking code first','info')}" class="h-[46px] w-[46px] flex-shrink-0 rounded-xl bg-slate-50 border border-slate-200 text-slate-500 hover:bg-slate-100 hover:text-primary-800 transition flex items-center justify-center cursor-pointer" title="Copy tracking code">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            </button>
                        </div>
                    </div>

                    <button id="updateStatusBtn" onclick="AdminBtnLoading.start(this);setTimeout(()=>{AdminBtnLoading.stop(this);AdminToast.show('Order status updated successfully!','success')},1000)" class="w-full py-3 rounded-xl text-[13px] font-bold bg-slate-100 text-primary-800 hover:bg-slate-100 transition cursor-pointer">
                        Update Status
                    </button>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="bg-white rounded-[20px] shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-slate-100 overflow-hidden relative">
                <div class="px-5 lg:px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-[15px] font-extrabold text-slate-900">Customer Info</h2>
                    <a href="#" onclick="event.preventDefault();document.getElementById('customerEditModal').classList.remove('hidden')" class="text-[13px] font-bold text-primary-800 hover:underline cursor-pointer">Edit</a>
                </div>
                <div class="p-5 lg:p-6 space-y-5">
                    
                    <div class="flex items-center gap-3.5">
                        <div class="h-11 w-11 rounded-full bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-[14px] font-bold text-slate-900">Dr. Aris Thorne</h3>
                            <p class="text-[12.5px] font-medium text-slate-500 mt-0.5">Lead Researcher, Xenon Labs</p>
                        </div>
                    </div>

                    <div class="space-y-4 pt-2">
                        <div class="flex gap-3 items-start">
                            <svg class="h-4.5 w-4.5 text-slate-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Email</p>
                                <a href="mailto:a.thorne@xenonlabs.edu" class="text-[13px] font-semibold text-slate-800 hover:text-primary-800 transition">a.thorne@xenonlabs.edu</a>
                            </div>
                        </div>
                        <div class="flex gap-3 items-start">
                            <svg class="h-4.5 w-4.5 text-slate-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Phone</p>
                                <a href="tel:+15559023341" class="text-[13px] font-semibold text-slate-800 hover:text-primary-800 transition">+1 (555) 902-3341</a>
                            </div>
                        </div>
                        <div class="flex gap-3 items-start">
                            <svg class="h-4.5 w-4.5 text-slate-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Shipping Address</p>
                                <p class="text-[13px] font-semibold text-slate-800 leading-[1.6]">
                                    42 Innovation Way, Suite 400<br>
                                    Cambridge, MA 02139<br>
                                    United States
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Alert Box -->
                    <div class="mt-6 bg-slate-100 rounded-xl p-4 flex gap-3 border border-indigo-100">
                        <svg class="h-5 w-5 text-primary-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        <p class="text-[12.5px] font-bold text-primary-800 leading-relaxed">
                            Requires signature upon delivery. Lab access code: 4492.
                        </p>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

<!-- Customer Edit Modal -->
<div id="customerEditModal" class="hidden fixed inset-0 z-[1000] flex items-center justify-center">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm cursor-pointer" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-slate-900">Edit Customer Info</h3>
            <button onclick="this.closest('#customerEditModal').classList.add('hidden')" class="h-8 w-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-700 transition cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Full Name</label>
                <input type="text" value="Dr. Aris Thorne" class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl px-4 py-2.5 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 font-medium">
            </div>
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Email</label>
                <input type="email" value="a.thorne@xenonlabs.edu" class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl px-4 py-2.5 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 font-medium">
            </div>
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Phone</label>
                <input type="tel" value="+1 (555) 902-3341" class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl px-4 py-2.5 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 font-medium">
            </div>
            <div class="space-y-1.5">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Shipping Address</label>
                <textarea rows="3" class="w-full bg-slate-50 border border-slate-200 text-sm rounded-xl px-4 py-2.5 focus:bg-white focus:border-primary-600 focus:ring-1 focus:ring-primary-600 transition outline-none text-slate-800 font-medium resize-none">42 Innovation Way, Suite 400
Cambridge, MA 02139
United States</textarea>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-6">
            <button onclick="this.closest('#customerEditModal').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl text-[13px] font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition cursor-pointer">Cancel</button>
            <button onclick="this.closest('#customerEditModal').classList.add('hidden');AdminToast.show('Customer info updated!','success')" class="flex-1 py-2.5 rounded-xl text-[13px] font-bold text-white bg-primary-600 hover:bg-primary-700 transition cursor-pointer">Save Changes</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ─── Add Note ───
document.getElementById('addNoteBtn')?.addEventListener('click', () => {
    const input = document.getElementById('noteInput');
    const text = input.value.trim();
    if (!text) { AdminToast.show('Please enter a note', 'info'); return; }
    const notesList = document.getElementById('notesList');
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) + ' • ' + now.toLocaleTimeString('en-US', {hour:'numeric', minute:'2-digit'});
    const note = document.createElement('div');
    note.className = 'p-3.5 bg-slate-100 rounded-xl border border-blue-100';
    note.innerHTML = `
        <div class="flex items-center justify-between mb-2">
            <span class="text-[12px] font-bold text-primary-800">Admin</span>
            <span class="text-[11px] font-medium text-slate-400">${dateStr}</span>
        </div>
        <p class="text-[13px] text-slate-600 font-medium leading-relaxed">${text}</p>
    `;
    notesList.appendChild(note);
    input.value = '';
    const count = notesList.children.length;
    document.getElementById('noteCount').textContent = count + (count === 1 ? ' note' : ' notes');
    AdminToast.show('Note added', 'success');
});

document.getElementById('noteInput')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') document.getElementById('addNoteBtn').click();
});
</script>
@endpush
