@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Guest Requests</h1>
                <p class="text-sm text-gray-500">Manage real-time service requests from guests.</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.location.reload()"
                    class="p-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-xl flex items-center gap-4">
                <div class="p-3 bg-indigo-100 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-indigo-500 uppercase tracking-widest">Pending</p>
                    <p class="text-2xl font-black text-indigo-900">{{ $requests->where('status', 'pending')->count() }}</p>
                </div>
            </div>
            <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-center gap-4">
                <div class="p-3 bg-blue-100 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-widest">In Progress</p>
                    <p class="text-2xl font-black text-blue-900">{{ $requests->where('status', 'in_progress')->count() }}
                    </p>
                </div>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-xl flex items-center gap-4">
                <div class="p-3 bg-emerald-100 rounded-lg text-emerald-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest">Completed</p>
                    <p class="text-2xl font-black text-emerald-900">{{ $requests->where('status', 'completed')->count() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Requests List -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 divide-y divide-gray-100">
                @forelse($requests as $req)
                    <div
                        class="p-6 hover:bg-gray-50 transition flex flex-col md:flex-row gap-6 md:items-center justify-between {{ $req->status === 'pending' ? 'bg-indigo-50/30' : '' }}">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0 
                                            {{ $req->type === 'housekeeping' ? 'bg-blue-100 text-blue-600' : '' }}
                                            {{ $req->type === 'amenities' ? 'bg-emerald-100 text-emerald-600' : '' }}
                                            {{ $req->type === 'maintenance' ? 'bg-amber-100 text-amber-600' : '' }}
                                            {{ $req->type === 'other' ? 'bg-gray-100 text-gray-600' : '' }}">
                                @if($req->type === 'housekeeping')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4">
                                        </path>
                                    </svg>
                                @elseif($req->type === 'amenities')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                @elseif($req->type === 'maintenance')
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <span
                                        class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest bg-gray-900 text-white">
                                        Room {{ $req->booking->assignedRooms->pluck('number')->join(', ') ?: 'N/A' }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-900">{{ $req->booking->guest_name }}</span>
                                    <span class="text-xs text-gray-400">• {{ $req->created_at->diffForHumans() }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 capitalize">{{ $req->type }} Request</h3>
                                <p class="text-gray-600 text-sm mt-1">{{ $req->request ?: 'No additional details.' }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            @if($req->status === 'pending')
                                <form action="{{ route('admin.requests.update', $req) }}" method="POST" x-data="{ loading: false }">
                                    @csrf @method('PATCH')
                                    <button type="submit" name="status" value="in_progress" :disabled="loading"
                                        @click="loading = true"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-lg transition shadow-lg shadow-blue-500/20 disabled:opacity-50 flex items-center gap-2">
                                        <svg x-show="loading" class="animate-spin h-3 w-3 text-white" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span x-text="loading ? 'Starting...' : 'Accept & Start'"></span>
                                    </button>
                                </form>
                            @elseif($req->status === 'in_progress')
                                <form action="{{ route('admin.requests.update', $req) }}" method="POST" x-data="{ loading: false }">
                                    @csrf @method('PATCH')
                                    <button type="submit" name="status" value="completed" :disabled="loading"
                                        @click="loading = true"
                                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg transition shadow-lg shadow-emerald-500/20 disabled:opacity-50 flex items-center gap-2">
                                        <svg x-show="loading" class="animate-spin h-3 w-3 text-white" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span x-text="loading ? 'Finalizing...' : 'Mark Complete'"></span>
                                    </button>
                                </form>
                            @else
                                <button disabled
                                    class="px-4 py-2 bg-gray-100 text-gray-400 text-sm font-bold rounded-lg cursor-not-allowed">
                                    Completed {{ $req->updated_at->format('H:i') }}
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div
                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">All caught up!</h3>
                        <p class="text-gray-500">No pending guest requests at the moment.</p>
                    </div>
                @endforelse
            </div>

            @if($requests->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection