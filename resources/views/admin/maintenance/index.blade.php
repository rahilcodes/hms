@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8" x-data="maintenanceDashboard()">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Maintenance & Repairs</h1>
                <p class="text-slate-500 mt-1">Track asset health, repairs, and technician assignments.</p>
            </div>
            <a href="{{ route('admin.maintenance.create') }}"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-lg shadow-blue-600/30">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Report Issue
            </a>
        </div>

        <!-- Active Issues -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h2 class="font-bold text-slate-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Active Requests
                </h2>
                <span
                    class="text-xs font-bold bg-white border border-slate-200 px-2 py-1 rounded-md text-slate-500">{{ $pendingLogs->count() }}
                    Issues</span>
            </div>

            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Asset /
                            Location</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Issue</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Reported
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tech</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($pendingLogs as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 text-lg">
                                        @if($log->asset)
                                            @if($log->asset->type === 'AC') ❄️
                                            @elseif($log->asset->type === 'TV') 📺
                                            @else 🛠️
                                            @endif
                                        @else
                                            🏢
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">
                                            {{ $log->asset ? $log->asset->name : 'General Issue' }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $log->asset && $log->asset->roomType ? $log->asset->roomType->name : 'No Location' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs">
                                    <p class="text-sm text-slate-700 line-clamp-2" title="{{ $log->description }}">
                                        {{ $log->description }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs font-bold bg-slate-100 text-slate-600 px-2 py-1 rounded-md border border-slate-200">
                                    {{ $log->technician_name ?? 'Unassigned' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($log->status === 'pending')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-100 flex items-center gap-1 w-fit">
                                        Pending
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100 flex items-center gap-1 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> In Progress
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($log->status === 'pending')
                                    <button @click="updateStatus({{ $log->id }}, 'in-progress')"
                                        class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">
                                        Start Repair
                                    </button>
                                @else
                                    <button @click="openCompleteModal({{ $log->id }})"
                                        class="text-xs font-bold text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 px-3 py-1.5 rounded-lg transition">
                                        Mark Complete
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <p class="font-medium">All systems operational!</p>
                                    <p class="text-xs mt-1">No pending maintenance requests.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- History -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h2 class="font-bold text-slate-800">Recent History</h2>
            </div>
            <table class="w-full opacity-75 hover:opacity-100 transition">
                <tbody class="divide-y divide-slate-50">
                    @foreach($completedLogs as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-700 text-sm">{{ $log->asset ? $log->asset->name : 'General' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $log->description }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-slate-500">Fixed by {{ $log->technician_name }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span
                                    class="text-xs font-bold text-emerald-600">{{ $log->completed_at ? $log->completed_at->format('M d') : '-' }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Complete Modal -->
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModal" class="fixed inset-0 transition-opacity" aria-hidden="true"
                    @click="showModal = false">
                    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showModal"
                    class="relative z-50 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <h3 class="text-lg font-bold text-slate-900 mb-4">Complete Maintenance Task</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Repair Cost (Optional)</label>
                            <input type="number" x-model="completeCost" class="w-full rounded-lg border-slate-200"
                                placeholder="0.00">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button @click="showModal = false"
                            class="px-4 py-2 rounded-lg border border-slate-200 text-slate-600 font-bold hover:bg-slate-50">Cancel</button>
                        <button @click="submitCompletion()"
                            class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-600/30">
                            Mark as Fixed
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('maintenanceDashboard', () => ({
                showModal: false,
                activeLogId: null,
                completeCost: '',

                updateStatus(id, status) {
                    fetch(`/admin/maintenance/${id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: status })
                    })
                        .then(res => {
                            if (!res.ok) throw new Error('Network response was not ok');
                            return res.json();
                        })
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert('Error: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Something went wrong. Please check the console for details.');
                        });
                },

                openCompleteModal(id) {
                    this.activeLogId = id;
                    this.completeCost = '';
                    this.showModal = true;
                },

                submitCompletion() {
                    if (!this.activeLogId) return;

                    fetch(`/admin/maintenance/${this.activeLogId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            status: 'completed',
                            cost: this.completeCost
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            }
                        });
                }
            }));
        });
    </script>
@endsection