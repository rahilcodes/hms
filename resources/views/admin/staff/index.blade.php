@extends('layouts.admin')

@section('title', 'Staff & Roles')

@section('content')
    <div class="space-y-8">
        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Staff Management</h1>
                <p class="text-slate-500 mt-1 font-medium">Manage administrative accounts and role-based permissions.</p>
            </div>
            <a href="{{ route('admin.staff.create') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-2xl font-bold text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 transition transform active:scale-95 duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Add New Staff
            </a>
        </div>

        {{-- Stats Row (Optional but nice) --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-2xl font-black text-slate-900 leading-none">{{ $staff->count() }}</span>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Staff</p>
                </div>
            </div>
            <!-- Add more stats if needed -->
        </div>

        {{-- Staff Table --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Name & Email
                        </th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                            Role</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">
                            Status</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($staff as $member)
                        <tr class="hover:bg-slate-50/50 transition duration-150">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-500 border border-slate-200">
                                        {{ strtoupper(substr($member->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900">{{ $member->name }}</h3>
                                        <p class="text-xs text-slate-400 font-medium">{{ $member->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-center">
                                @php
                                    $roleColors = [
                                        'super_admin' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'admin' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'receptionist' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'revenue' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'housekeeping' => 'bg-pink-100 text-pink-700 border-pink-200',
                                    ];
                                    $roleLabel = str_replace('_', ' ', ucwords($member->role, '_'));
                                @endphp
                                <span
                                    class="px-3 py-1 rounded-full text-[10px] font-bold border {{ $roleColors[$member->role] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                    {{ $roleLabel }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="flex items-center justify-center gap-1.5 text-[10px] font-bold text-emerald-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.staff.edit', $member) }}"
                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5M16.5 3.5a2.121 2.121 0 113 3L7 19l-4 1 1-4L16.5 3.5z" />
                                        </svg>
                                    </a>
                                    @if($member->id !== auth('admin')->id())
                                        <form action="{{ route('admin.staff.destroy', $member) }}" method="POST"
                                            onsubmit="return confirm('Are you sure you want to remove this staff member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection