@extends('layouts.admin')

@section('header_title', 'AI Rate Intelligence')

@section('content')
    <div class="space-y-6">

        {{-- HEADER STATS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">30-Day Outlook</h3>
                        <p class="text-2xl font-black text-slate-900">72% <span
                                class="text-sm font-medium text-emerald-500">+5%</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Proj. RevPAR</h3>
                        <p class="text-2xl font-black text-slate-900">$142 <span
                                class="text-sm font-medium text-emerald-500">+$12</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">AI Suggestions</h3>
                        <p class="text-2xl font-black text-slate-900">3 <span
                                class="text-sm font-medium text-slate-400">Active</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHART CARD --}}
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <h3 class="font-bold text-lg text-slate-900 mb-6">Occupancy Forecast</h3>
            <div class="h-64 relative" x-data="yieldChart()" x-init="initChart()">
                <canvas id="occupancyChart"></canvas>
            </div>
        </div>

        {{-- INSIGHTS GRID --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- ACTIONABLE INSIGHTS --}}
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">AI Recommendations</h3>

                @foreach($insights as $insight)
                    <div
                        class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition group relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>

                        <div class="flex gap-4">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $insight['type'] == 'opportunity' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                                @if($insight['type'] == 'opportunity')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">{{ $insight['title'] }}</h4>
                                <p class="text-sm text-slate-500 mt-1 mb-3">{{ $insight['message'] }}</p>
                                <button
                                    class="text-xs font-bold px-3 py-1.5 rounded-lg bg-slate-900 text-white hover:bg-blue-600 transition">
                                    {{ $insight['action'] }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

            {{-- COMPETITOR ANALYSIS --}}
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Market Intelligence</h3>

                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3 font-bold text-slate-500">Competitor</th>
                                <th class="px-6 py-3 font-bold text-slate-500">Rate (Avg)</th>
                                <th class="px-6 py-3 font-bold text-slate-500">Trend</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr>
                                <td class="px-6 py-4 font-bold text-slate-900">Grand Luxury Hotel</td>
                                <td class="px-6 py-4 text-slate-600">$245</td>
                                <td class="px-6 py-4 text-rose-500 font-medium">▼ $10</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-bold text-slate-900">City Center Suites</td>
                                <td class="px-6 py-4 text-slate-600">$189</td>
                                <td class="px-6 py-4 text-emerald-500 font-medium">▲ $5</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-bold text-slate-900">Budget Stay</td>
                                <td class="px-6 py-4 text-slate-600">$99</td>
                                <td class="px-6 py-4 text-slate-400 font-medium">-</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="p-4 bg-slate-50 border-t border-slate-100 text-center">
                        <p class="text-xs text-slate-400">Data refreshed: 10 mins ago</p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('yieldChart', () => ({
                initChart() {
                    const ctx = document.getElementById('occupancyChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($dates),
                            datasets: [
                                {
                                    label: 'Occupancy %',
                                    data: @json($occupancyData),
                                    borderColor: '#2563eb',
                                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'RevPAR ($)',
                                    data: @json($revenueData),
                                    borderColor: '#9333ea', // Purple
                                    borderDash: [5, 5],
                                    tension: 0.4,
                                    fill: false,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    title: { display: true, text: 'Occupancy %' },
                                    min: 0,
                                    max: 100
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    title: { display: true, text: 'Expected RevPAR ($)' },
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                },
                            }
                        }
                    });
                }
            }));
        });
    </script>
@endsection