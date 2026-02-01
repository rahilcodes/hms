@extends('layouts.admin')

@section('title', isset($staff) ? 'Edit Staff' : 'Add New Staff')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ isset($staff) ? 'Edit Staff Member' : 'Create New Staff' }}</h1>
            <p class="text-slate-500 mt-1 font-medium">Configure access and roles for your property staff.</p>
        </div>
        <a href="{{ route('admin.staff.index') }}" class="p-3 bg-white border border-slate-200 rounded-2xl text-slate-400 hover:text-slate-600 transition shadow-sm">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl">
            <ul class="list-disc list-inside text-sm text-rose-600 font-medium space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ isset($staff) ? route('admin.staff.update', $staff) : route('admin.staff.store') }}" method="POST" class="space-y-6">
        @csrf
        @if(isset($staff))
            @method('PUT')
        @endif

        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm p-10 space-y-10">
            {{-- Basic Info Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $staff->name ?? '') }}" required
                           class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none font-medium"
                           placeholder="e.g. John Doe">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $staff->email ?? '') }}" required
                           class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none font-medium"
                           placeholder="admin@hotel.com">
                </div>
            </div>

            {{-- Role Selection --}}
            <div class="space-y-4">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Assign Access Role</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @php
                        $roles = [
                            ['id' => 'super_admin', 'label' => 'Super Admin', 'desc' => 'Full system access.'],
                            ['id' => 'admin', 'label' => 'Admin', 'desc' => 'General management.'],
                            ['id' => 'receptionist', 'label' => 'Receptionist', 'desc' => 'Bookings & guests.'],
                            ['id' => 'revenue', 'label' => 'Revenue Manager', 'desc' => 'Pricing & inventory.'],
                            ['id' => 'housekeeping', 'label' => 'Housekeeping', 'desc' => 'Room status control.'],
                        ];
                    @endphp

                    @foreach($roles as $role)
                        <label class="cursor-pointer group">
                            <input type="radio" name="role" value="{{ $role['id'] }}" class="peer hidden" 
                                   {{ old('role', $staff->role ?? '') === $role['id'] ? 'checked' : '' }} required>
                            <div class="h-full p-6 border border-slate-100 bg-slate-50 rounded-2xl peer-checked:border-blue-600 peer-checked:bg-blue-50/50 peer-checked:shadow-lg peer-checked:shadow-blue-500/10 transition group-hover:bg-white group-hover:border-slate-300">
                                <h4 class="font-bold text-slate-900 group-peer-checked:text-blue-600">{{ $role['label'] }}</h4>
                                <p class="text-[10px] text-slate-400 mt-1 font-medium">{{ $role['desc'] }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Password Section --}}
            <div class="pt-10 border-t border-slate-100">
                <div class="mb-6">
                    <h4 class="text-sm font-bold text-slate-900">Security Credentials</h4>
                    <p class="text-xs text-slate-400 font-medium">{{ isset($staff) ? 'Leave blank to keep current password.' : 'Minimum 8 characters required.' }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                        <input type="password" name="password" 
                               {{ isset($staff) ? '' : 'required' }}
                               class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none font-medium"
                               placeholder="••••••••">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" 
                               {{ isset($staff) ? '' : 'required' }}
                               class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none font-medium"
                               placeholder="••••••••">
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.staff.index') }}" class="px-8 py-4 text-slate-500 font-bold text-sm hover:text-slate-700 transition">Cancel</a>
            <button type="submit" class="px-12 py-4 bg-blue-600 text-white rounded-2xl font-bold text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 transition transform active:scale-95 duration-200">
                {{ isset($staff) ? 'Update Staff Credentials' : 'Create Staff Member' }}
            </button>
        </div>
    </form>
</div>
@endsection
