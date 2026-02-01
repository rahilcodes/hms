@extends('layouts.admin')

@section('header_title', 'Corporate Management')

@section('content')
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Corporate Clients</h3>
                <p class="text-xs text-slate-400 font-medium">Manage corporate partnerships and credit limits</p>
            </div>
            <a href="{{ route('admin.companies.create') }}"
                class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 rounded-xl hover:shadow-lg hover:shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                + New Company
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <th class="px-8 py-5 text-left">Company Name</th>
                        <th class="px-8 py-5 text-left">GST Number</th>
                        <th class="px-8 py-5 text-left">Contact Info</th>
                        <th class="px-8 py-5 text-left">Credit Limit</th>
                        <th class="px-8 py-5 text-left">Status</th>
                        <th class="px-8 py-5 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($companies as $company)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-xs">
                                        {{ strtoupper(substr($company->name, 0, 2)) }}
                                    </div>
                                    <p class="text-xs font-bold text-slate-900">{{ $company->name }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-xs font-bold text-slate-600">
                                {{ $company->gst_number ?? 'N/A' }}
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-[10px] font-bold text-slate-900">{{ $company->email }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $company->phone }}</p>
                            </td>
                            <td class="px-8 py-5">
                                <span
                                    class="text-xs font-black text-slate-900">₹{{ number_format($company->credit_limit) }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <span
                                    class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider {{ $company->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                                    {{ $company->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="{{ route('admin.companies.edit', $company) }}"
                                    class="p-2 text-slate-400 hover:text-blue-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">No Corporate Profiles
                                        Found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div class="px-8 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
@endsection