@extends('titanium.layouts.app')

@section('header_title', 'Billing & Subscription')

@section('content')
<div class="max-w-6xl mx-auto space-y-12">
    
    <!-- Page Header -->
    <div class="flex items-end justify-between">
        <div>
             <h2 class="text-3xl font-black tracking-tight text-white mb-2">Subscription Manager</h2>
             <p class="text-gray-400">Manage your hotel's billing plan and view invoice history.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="px-4 py-2 rounded-lg bg-gray-800 border border-gray-700 flex items-center gap-4">
                 <div class="flex items-center gap-2">
                     <div class="w-2 h-2 rounded-full {{ $subscription->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></div>
                     <span class="text-sm font-bold text-white uppercase tracking-widest">{{ $subscription->status }}</span>
                 </div>
                 <form action="{{ route('titanium.subscription.toggle') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-[10px] font-black uppercase tracking-tighter text-blue-400 hover:text-white transition">
                        Toggle Plan
                    </button>
                 </form>
             </div>
        </div>
    </div>

    <!-- Current Plan Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Status Card -->
        <div class="col-span-1 bg-gradient-to-br from-blue-900 to-gray-900 border border-blue-800 rounded-3xl p-8 relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition duration-700"></div>
            
            <h3 class="text-xs font-bold text-blue-400 uppercase tracking-widest mb-6 relative z-10">Current Plan</h3>
            @if($subscription->status === 'active')
                <div class="flex items-baseline gap-2 mb-2 relative z-10">
                    <span class="text-5xl font-black text-white tracking-tighter">{{ $subscription->plan_name }}</span>
                </div>
                <p class="text-blue-200/60 font-medium mb-8 relative z-10">
                    Next billing on {{ \Carbon\Carbon::parse($subscription->next_billing_date)->format('M d, Y') }}
                </p>

                <div class="flex items-center gap-4 relative z-10">
                    <span class="text-3xl font-bold text-white">${{ number_format($subscription->price) }}</span>
                    <span class="text-sm text-blue-300 font-medium">/ month</span>
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-4 relative z-10">
                    <div class="w-12 h-12 rounded-full bg-rose-500/20 flex items-center justify-center text-rose-400 mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                    <span class="text-2xl font-black text-white tracking-tighter">No Active Plan</span>
                    <p class="text-xs text-blue-200/60 mt-2 font-bold uppercase tracking-widest">Select a plan below to activate</p>
                </div>
            @endif
        </div>

        <!-- Payment Method (Mock) -->
        <div class="col-span-2 bg-gray-800 border border-gray-700 rounded-3xl p-8 flex items-center justify-between">
            <div>
                 <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4">Payment Method</h3>
                 <div class="flex items-center gap-4">
                     <div class="w-16 h-10 bg-gray-700 rounded flex items-center justify-center text-white font-bold tracking-widest">
                         VISA
                     </div>
                     <div>
                         <p class="font-bold text-white text-lg">•••• •••• •••• 4242</p>
                         <p class="text-xs text-gray-400">Expires 12/28</p>
                     </div>
                 </div>
            </div>
            <button class="px-6 py-3 rounded-xl bg-gray-700 hover:bg-gray-600 text-white font-bold text-sm transition">
                Update Card
            </button>
        </div>
    </div>

    <!-- Plan Selection -->
    <div>
        <h3 class="text-xl font-bold text-white mb-8">Available Plans</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach(['basic' => ['price' => 49, 'features' => ['Housekeeping', 'Front Desk']], 
                      'pro' => ['price' => 149, 'features' => ['Waitlist', 'CRM', 'Financials']], 
                      'enterprise' => ['price' => 499, 'features' => ['Everything', 'Priority Support', 'API Access']]] as $key => $planData)
                
                <div class="relative bg-gray-800 border {{ Str::contains(strtolower($subscription->plan_name), $key) ? 'border-blue-500 ring-4 ring-blue-500/20' : 'border-gray-700' }} rounded-2xl p-8 flex flex-col">
                    @if(Str::contains(strtolower($subscription->plan_name), $key))
                        <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-blue-600 text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full shadow-lg">
                            Current Plan
                        </div>
                    @endif

                    <h4 class="text-lg font-bold text-white capitalize mb-2">{{ $key }}</h4>
                    <div class="flex items-baseline gap-1 mb-6">
                        <span class="text-3xl font-black text-white">${{ $planData['price'] }}</span>
                        <span class="text-sm text-gray-400">/mo</span>
                    </div>

                    <ul class="space-y-3 mb-8 flex-1">
                        @foreach($planData['features'] as $feature)
                            <li class="flex items-center gap-3 text-sm text-gray-300">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    <form method="POST" action="{{ route('subscription.update') }}">
                        @csrf
                        <input type="hidden" name="plan" value="{{ $key }}">
                        <button type="submit" 
                            class="w-full py-3 rounded-xl font-bold text-sm transition
                            {{ Str::contains(strtolower($subscription->plan_name), $key) 
                                ? 'bg-blue-600 text-white hover:bg-blue-500 shadow-lg shadow-blue-900/20 cursor-default opacity-50' 
                                : 'bg-gray-700 text-white hover:bg-white hover:text-gray-900' }}"
                            {{ Str::contains(strtolower($subscription->plan_name), $key) ? 'disabled' : '' }}>
                            {{ Str::contains(strtolower($subscription->plan_name), $key) ? 'Active' : 'Switch to ' . ucfirst($key) }}
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Invoice History -->
    <div>
        <h3 class="text-xl font-bold text-white mb-6">Invoice History</h3>
        <div class="bg-gray-800 border border-gray-700 rounded-2xl overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-700 bg-gray-900/50">
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Invoice ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Amount</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 text-sm text-gray-300">
                    @foreach($invoices as $invoice)
                        <tr class="hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 font-mono text-white">{{ $invoice['id'] }}</td>
                            <td class="px-6 py-4">{{ $invoice['date'] }}</td>
                            <td class="px-6 py-4 font-bold text-white">{{ $invoice['amount'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold uppercase tracking-wider">
                                    {{ $invoice['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-gray-400 hover:text-white font-medium underline">Download</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
