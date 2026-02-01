@extends('layouts.admin')

@section('header_title', isset($company) ? 'Edit Corporate Profile' : 'New Corporate Profile')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('admin.companies.index') }}"
                class="text-sm font-bold text-slate-400 hover:text-slate-900 transition flex items-center gap-2">
                &larr; Back to Companies
            </a>
        </div>

        <form action="{{ isset($company) ? route('admin.companies.update', $company) : route('admin.companies.store') }}"
            method="POST" class="space-y-6">
            @csrf
            @if(isset($company)) @method('PUT') @endif

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30">
                    <h3 class="text-lg font-bold text-slate-900">Profile Details</h3>
                    <p class="text-xs text-slate-400 font-medium">Basic information for the corporate client</p>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Company
                            Name</label>
                        <input type="text" name="name" value="{{ old('name', $company->name ?? '') }}" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">GST
                            Number</label>
                        <input type="text" name="gst_number" value="{{ old('gst_number', $company->gst_number ?? '') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Credit
                            Limit (₹)</label>
                        <input type="number" name="credit_limit"
                            value="{{ old('credit_limit', $company->credit_limit ?? 0) }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Email
                            Address</label>
                        <input type="email" name="email" value="{{ old('email', $company->email ?? '') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Phone
                            Number</label>
                        <input type="text" name="phone" value="{{ old('phone', $company->phone ?? '') }}"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Registered
                            Address</label>
                        <textarea name="address" rows="3"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 focus:bg-white transition">{{ old('address', $company->address ?? '') }}</textarea>
                    </div>
                </div>

                <div class="px-8 py-6 bg-slate-50/30 border-t border-slate-100 flex justify-between items-center">
                    @if(isset($company))
                        <button type="button"
                            onclick="confirm('Delete this profile?') && document.getElementById('delete-form').submit()"
                            class="text-[10px] font-black text-rose-500 uppercase tracking-widest hover:text-rose-700 transition">
                            Delete Profile
                        </button>
                    @else
                        <span></span>
                    @endif

                    <button type="submit"
                        class="px-8 py-3 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:shadow-xl hover:shadow-blue-200 transition transform active:scale-95">
                        {{ isset($company) ? 'Save Changes' : 'Create Profile' }}
                    </button>
                </div>
            </div>
        </form>

        @if(isset($company))
            <form id="delete-form" action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="hidden">
                @csrf @method('DELETE')
            </form>
        @endif
    </div>
@endsection