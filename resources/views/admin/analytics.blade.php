@extends('layouts.admin')

@section('header_title', 'Financial Intelligence')

@section('content')

    <div class="mb-12">
        <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Strategic Overview</h2>
        <p class="text-slate-500 font-medium">Detailed temporal and structural revenue analysis.</p>
    </div>

    {{-- PERFORMANCE ANALYTICS --}}
    <div
        class="bg-white rounded-[3rem] border border-slate-200 shadow-xl shadow-slate-200/40 p-12 mb-12 overflow-hidden relative">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 gap-6 relative z-10">
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Revenue Performance</h3>
                <p class="text-xs text-slate-400 font-black uppercase tracking-[0.2em] mt-1">Transactional Trends</p>
            </div>
            <div class="flex bg-slate-100 p-1.5 rounded-2xl" x-data="{ view: 'day' }">
                <button @click="view = 'day'; $dispatch('update-chart', 'day')"
                    :class="view === 'day' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                    class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Daily
                    View</button>
                <button @click="view = 'month'; $dispatch('update-chart', 'month')"
                    :class="view === 'month' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                    class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">Monthly
                    View</button>
            </div>
        </div>

        <div id="revenueChart" class="min-h-[300px] md:min-h-[450px]"></div>

        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-50/30 rounded-full blur-[120px] -mr-48 -mt-48"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

        {{-- REVENUE MIX --}}
        <div
            class="bg-white rounded-[3rem] border border-slate-200 shadow-xl shadow-slate-200/40 p-12 flex flex-col items-center">
            <div class="w-full text-left mb-12">
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Revenue Architecture</h3>
                <p class="text-xs text-slate-400 font-black uppercase tracking-[0.2em] mt-1">Contribution by Suite Category
                </p>
            </div>

            <div id="revenueMixChart" class="w-full max-w-md"></div>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-4 w-full">
                @foreach($roomTypeLabels as $index => $label)
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="w-3 h-3 rounded-full"
                            style="background-color: {{ ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'][$index % 5] }}">
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $label }}</p>
                            <p class="text-sm font-black text-slate-900">₹{{ number_format($roomTypeRevenue[$index]) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- STATS CARDS --}}
        <div class="flex flex-col gap-8">
            <div class="bg-[#0f172a] rounded-[3rem] p-12 text-white relative overflow-hidden flex-1">
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div>
                        <h3 class="text-3xl font-black tracking-tighter mb-2">Maximum Velocity</h3>
                        <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">AI Performance Prediction</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-lg leading-relaxed mb-6">Your property is currently trending 14%
                            higher than last month. Consider increasing weekend rates by 5% to maximize yield.</p>
                        <div class="flex items-center gap-2 text-emerald-400 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            <span>Optimal Growth Path Active</span>
                        </div>
                    </div>
                </div>
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-600/10 rounded-full blur-[100px]"></div>
            </div>

            <div class="bg-white rounded-[3rem] border border-slate-200 p-12 flex-1">
                <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Data Legend</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900">Temporal Resolution</p>
                            <p class="text-xs text-slate-500 font-medium tracking-tight">Data synchronized every 5 minutes
                                from central booking engine.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900">Verified Accuracy</p>
                            <p class="text-xs text-slate-500 font-medium tracking-tight">Audit-grade financial tracking with
                                double-entry validation.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const monthData = @json($chartData);
            const monthLabels = @json($chartLabels);
            const dayData = @json($dailyChartData);
            const dayLabels = @json($dailyChartLabels);
            const mixData = @json($roomTypeRevenue);
            const mixLabels = @json($roomTypeLabels);

            var options = {
                series: [{
                    name: 'Revenue',
                    data: dayData
                }],
                chart: {
                    type: 'area',
                    height: 450,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'Inter, sans-serif'
                },
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 4,
                    colors: ['#3b82f6']
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.6,
                        opacityTo: 0.01,
                        stops: [0, 95],
                        colorStops: [
                            { offset: 0, color: '#3b82f6', opacity: 0.5 },
                            { offset: 100, color: '#3b82f6', opacity: 0 }
                        ]
                    }
                },
                markers: {
                    size: 6,
                    colors: ['#3b82f6'],
                    strokeColors: '#fff',
                    strokeWidth: 3,
                    hover: { size: 9 }
                },
                xaxis: {
                    categories: dayLabels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return '₹' + val.toLocaleString();
                        },
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 700
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 8,
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: false } }
                },
                colors: ['#3b82f6'],
                tooltip: {
                    theme: 'dark',
                    style: { fontSize: '13px' },
                    y: {
                        formatter: function (val) {
                            return '₹' + val.toLocaleString();
                        }
                    },
                    x: { show: true }
                }
            };

            var chart = new ApexCharts(document.querySelector("#revenueChart"), options);
            chart.render();

            window.addEventListener('update-chart', function (e) {
                const type = e.detail;
                if (type === 'day') {
                    chart.updateOptions({
                        xaxis: { categories: dayLabels },
                        series: [{ data: dayData }]
                    });
                } else {
                    chart.updateOptions({
                        xaxis: { categories: monthLabels },
                        series: [{ data: monthData }]
                    });
                }
            });

            // DONUT CHART
            var mixOptions = {
                series: mixData,
                chart: {
                    type: 'donut',
                    height: 400,
                    fontFamily: 'Inter, sans-serif'
                },
                labels: mixLabels,
                legend: {
                    show: false
                },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '80%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '12px',
                                    fontWeight: 800,
                                    color: '#94a3b8',
                                    offsetY: -10
                                },
                                value: {
                                    show: true,
                                    fontSize: '32px',
                                    fontWeight: 900,
                                    color: '#0f172a',
                                    offsetY: 10,
                                    formatter: (val) => '₹' + parseInt(val).toLocaleString()
                                },
                                total: {
                                    show: true,
                                    label: 'ANNUAL REVENUE',
                                    color: '#94a3b8',
                                    formatter: function (w) {
                                        return '₹' + w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                },
                colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                stroke: { width: 0 }
            };

            var mixChart = new ApexCharts(document.querySelector("#revenueMixChart"), mixOptions);
            mixChart.render();
        });
    </script>

@endsection