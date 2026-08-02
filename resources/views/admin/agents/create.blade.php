@extends('layouts.admin')

@section('header_title', $agent ? 'Edit Agent' : 'New Travel Agent')

@section('content')
    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ $agent ? route('admin.agents.update', $agent) : route('admin.agents.store') }}" class="space-y-5">
            @csrf
            @if($agent) @method('PUT') @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-600 text-sm rounded-xl p-4">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500">Contact Name *</label>
                        <input name="name" required value="{{ old('name', $agent->name ?? '') }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Agency Name</label>
                        <input name="agency_name" value="{{ old('agency_name', $agent->agency_name ?? '') }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Phone *</label>
                        <input name="phone" required value="{{ old('phone', $agent->phone ?? '') }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Email</label>
                        <input name="email" type="email" value="{{ old('email', $agent->email ?? '') }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">GSTIN</label>
                        <input name="gst_number" value="{{ old('gst_number', $agent->gst_number ?? '') }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Commission % *</label>
                        <input name="commission_percent" type="number" step="0.5" min="0" max="50" required
                            value="{{ old('commission_percent', $agent->commission_percent ?? 10) }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500">Notes (negotiated rates, terms…)</label>
                    <textarea name="notes" rows="3" class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">{{ old('notes', $agent->notes ?? '') }}</textarea>
                </div>
                <label class="flex items-center gap-2.5 text-sm font-bold text-slate-600">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $agent->is_active ?? true)) class="w-4 h-4 rounded">
                    Active
                </label>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.agents.index') }}" class="flex-1 py-3.5 rounded-xl bg-white border border-slate-200 text-center text-sm font-bold text-slate-500">Cancel</a>
                <button class="flex-1 py-3.5 rounded-xl bg-blue-600 text-white text-sm font-black shadow">{{ $agent ? 'Update Agent' : 'Save Agent' }}</button>
            </div>
        </form>
    </div>
@endsection
