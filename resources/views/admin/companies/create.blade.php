@extends('layouts.admin')

@section('content')

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.companies.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1 mb-2">
                &larr; Back to list
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Add Corporate Profile</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <form action="{{ route('admin.companies.store') }}" method="POST">
                @csrf

                <div class="border-b border-gray-100 pb-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 xl:mb-4 mb-4">Corporate Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Company Name</label>
                            <input type="text" name="name" required placeholder="e.g. Acme Corp" value="{{ old('name') }}"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900 @error('name') border-red-500 ring-red-500 @enderror">
                            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">GST/VAT Number <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <input type="text" name="gst_number" placeholder="22AAAAA0000A1Z5" value="{{ old('gst_number') }}"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900 @error('gst_number') border-red-500 ring-red-500 @enderror">
                            @error('gst_number') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email Contact</label>
                            <input type="email" name="email" required placeholder="contact@acme.com" value="{{ old('email') }}"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900 @error('email') border-red-500 ring-red-500 @enderror">
                            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="phone" required placeholder="+1 234 567 890" value="{{ old('phone') }}"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900 @error('phone') border-red-500 ring-red-500 @enderror">
                            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Company Address <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <textarea name="address" rows="3" placeholder="123 Corporate Blvd&#10;Suite 500&#10;City, State, Zip"
                            class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-medium text-slate-900 @error('address') border-red-500 ring-red-500 @enderror">{{ old('address') }}</textarea>
                        @error('address') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ACCOUNT SETTINGS --}}
                <div class="pb-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 xl:mb-4 mb-4">Account Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Credit Limit (₹)</label>
                            <input type="number" name="credit_limit" required min="0" step="0.01" placeholder="50000" value="{{ old('credit_limit', 0) }}"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900 @error('credit_limit') border-red-500 ring-red-500 @enderror">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Maximum allowable credit for bookings.</p>
                            @error('credit_limit') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                            <div class="mt-2.5">
                                <label class="inline-flex items-center cursor-pointer group bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100 hover:border-blue-200 transition duration-300">
                                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-3 group-hover:text-blue-600 transition">Active Profile?</span>
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} class="sr-only peer">
                                    <div
                                        class="relative w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 shadow-inner">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="w-full md:w-auto px-8 py-3.5 bg-blue-600 text-white font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 transition-all">
                        Create Corporate Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
