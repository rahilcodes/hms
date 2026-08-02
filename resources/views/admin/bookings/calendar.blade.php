@extends('layouts.admin')

@section('header_title', 'Booking Calendar')

@section('content')

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col min-h-[800px]">
        <div
            class="px-8 py-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 bg-slate-50/30">
            <div>
                <h3 class="text-lg font-bold text-slate-900 leading-tight">Visual Timeline</h3>
                <p class="text-xs text-slate-400 font-medium mt-1">Manage occupancy and arrivals in real-time</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Confirmed</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500 shadow-sm shadow-amber-200"></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Pending</span>
                </div>
                <a href="{{ route('admin.bookings.create') }}"
                    class="px-5 py-2.5 text-xs font-black text-white bg-blue-600 rounded-xl hover:shadow-lg hover:shadow-blue-200 transition-all transform hover:-translate-y-0.5 active:scale-95">
                    + Quick Booking
                </a>
            </div>
        </div>

        <div class="p-8 flex-1">
            <div id="calendar" class="h-full"></div>
        </div>
    </div>

    {{-- FULLCALENDAR ASSETS --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var isMobile = window.innerWidth < 768;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: isMobile ? 'listWeek' : 'dayGridMonth',
                headerToolbar: isMobile ? {
                    left: 'prev,next',
                    center: 'title',
                    right: 'listWeek,timeGridDay'
                } : {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
                },
                height: isMobile ? 'auto' : '800px',
                contentHeight: isMobile ? 'auto' : 800,
                aspectRatio: isMobile ? 0.8 : 1.35,
                handleWindowResize: true,
                windowResize: function (arg) {
                    if (window.innerWidth < 768) {
                        calendar.changeView('listWeek');
                        calendar.setOption('headerToolbar', {
                            left: 'prev,next',
                            center: 'title',
                            right: 'listWeek,timeGridDay'
                        });
                    } else {
                        calendar.changeView('dayGridMonth');
                        calendar.setOption('headerToolbar', {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,listMonth'
                        });
                    }
                },
                events: '{{ route('admin.bookings.calendar.data') }}',
                eventClick: function (info) {
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                themeSystem: 'standard',
                height: 'auto',
                firstDay: 1, // Start week on Monday
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                },
                // Premium Styling Overrides
                eventClassNames: function (arg) {
                    return ['rounded-lg', 'border-none', 'shadow-sm', 'px-2', 'py-1', 'text-xs', 'font-bold'];
                },
                dayHeaderClassNames: ['text-[10px]', 'text-slate-400', 'uppercase', 'tracking-widest', 'font-bold', 'py-4', 'border-b', 'border-slate-100'],
                viewDidMount: function () {
                    // Inject custom styles into the calendar for a 100X look
                    const style = document.createElement('style');
                    style.innerHTML = `
                                    .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 800 !important; color: #0f172a !important; letter-spacing: -0.025em !important; }
                                    .fc-button { background: #fff !important; border: 1px solid #e2e8f0 !important; color: #64748b !important; font-size: 0.75rem !important; font-weight: 700 !important; border-radius: 0.75rem !important; padding: 0.625rem 1rem !important; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important; transition: all 0.2s !important; }
                                    .fc-button:hover { background: #f8fafc !important; color: #0f172a !important; }
                                    .fc-button-active { background: #0f172a !important; color: #fff !important; border-color: #0f172a !important; }
                                    .fc-today-button { text-transform: uppercase !important; letter-spacing: 0.05em !important; }
                                    .fc-daygrid-day-number { font-size: 0.75rem !important; font-weight: 700 !important; color: #94a3b8 !important; padding: 10px !important; }
                                    .fc-day-today { background: #f1f5f9 !important; }
                                    .fc-event-title { white-space: normal !important; }
                                `;
                    document.head.appendChild(style);
                }
            });
            calendar.render();
        });
    </script>

@endsection