@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Subscription & Billing</h1>
                <p class="text-gray-500 mt-1">Manage your plan and payment details</p>
            </div>
            <button class="bg-black text-white px-4 py-2 rounded-lg hover:bg-gray-800 transition">
                Start Support Chat
            </button>
        </div>

        @if($subscription)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                <!-- Plan Details -->
                <div class="md:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-5">
                        <svg class="w-48 h-48 text-slate-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </div>

                    <div class="relative z-10">
                        <div class="flex items-start justify-between">
                            <div>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 mb-4 uppercase tracking-widest">
                                    {{ $subscription->plan_name }}
                                </span>
                                <h2 class="text-5xl font-black text-slate-900 tracking-tight">
                                    ₹{{ number_format($subscription->price) }}
                                    <span class="text-xl text-slate-400 font-normal">/ {{ $subscription->billing_cycle }}</span>
                                </h2>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-1">Account Status</div>
                                @if($subscription->status === 'active')
                                    <div class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-sm font-bold inline-flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Active
                                    </div>
                                @else
                                    <div class="px-3 py-1 bg-rose-50 text-rose-600 rounded-lg text-sm font-bold inline-flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        {{ ucfirst($subscription->status) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-12 grid grid-cols-2 gap-12 border-t border-slate-50 pt-8">
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-2">Member Since</p>
                                <p class="text-lg font-bold text-slate-900">{{ $subscription->starts_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase tracking-widest font-black mb-2">Next Billing Date</p>
                                <p class="text-lg font-bold {{ $subscription->next_billing_date->isPast() ? 'text-rose-600' : 'text-slate-900' }}">
                                    {{ $subscription->next_billing_date ? $subscription->next_billing_date->format('M d, Y') : 'Lifetime' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features Snapshot -->
                <div class="bg-slate-50 rounded-2xl border border-slate-100 p-8">
                    <h3 class="font-black text-slate-900 uppercase tracking-widest text-xs mb-6">Plan Features</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center text-sm text-slate-600 font-medium">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            Unlimited Bookings
                        </li>
                        <li class="flex items-center text-sm text-slate-600 font-medium">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            Full Multi-User Access
                        </li>
                        <li class="flex items-center text-sm text-slate-600 font-medium">
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            Priority Tech Support
                        </li>
                    </ul>
                    <div class="mt-8">
                        <button class="w-full bg-white border-2 border-slate-200 py-3 rounded-xl text-xs font-black text-slate-900 uppercase tracking-widest hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-300">
                            Upgrade My Plan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Invoices Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                    <h3 class="font-black text-slate-900 uppercase tracking-widest text-xs">Billing & Invoices</h3>
                    <span class="text-[10px] font-bold text-slate-400">Showing last 24 months</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                <th class="px-8 py-4">Invoice ID</th>
                                <th class="px-8 py-4">Billing Date</th>
                                <th class="px-8 py-4">Amount</th>
                                <th class="px-8 py-4">Status</th>
                                <th class="px-8 py-4 text-right">Receipt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($invoices as $inv)
                                <tr class="hover:bg-slate-50 transition group">
                                    <td class="px-8 py-4 font-mono text-xs text-slate-600">{{ $inv['id'] }}</td>
                                    <td class="px-8 py-4 text-sm font-bold text-slate-900">{{ $inv['date'] }}</td>
                                    <td class="px-8 py-4 text-sm font-black text-slate-900">{{ $inv['amount'] }}</td>
                                    <td class="px-8 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600">
                                            {{ $inv['status'] }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <button class="text-blue-600 hover:text-blue-700 font-bold text-xs flex items-center gap-1 justify-end ml-auto group-hover:translate-x-1 transition-transform">
                                            Download <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-slate-100">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-widest mb-2">No active subscription</h3>
                <p class="text-slate-500 max-w-sm mx-auto mb-8">Your account is currently running on the free trial mode. Connect with us to unlock premium features.</p>
                <button class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-500 transition shadow-lg shadow-blue-500/20">
                    Contact Creativals Sales
                </button>
            </div>
        @endif
    </div>
@endsection