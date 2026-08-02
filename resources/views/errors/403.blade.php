@extends('layouts.admin')

@section('header_title', 'Access Denied')

@section('content')
    <div class="flex flex-col items-center justify-center py-20">
        <div
            class="bg-white p-12 rounded-[40px] border border-slate-200 shadow-2xl shadow-slate-100 max-w-lg w-full text-center">
            <div
                class="w-24 h-24 bg-rose-50 rounded-3xl flex items-center justify-center text-rose-500 mx-auto mb-8 animate-bounce">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m0 0v2m0-2h2m-2 0H10m4-11a3 3 0 013 3v1h1a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2h1V7a3 3 0 013-3h4z" />
                </svg>
            </div>

            <h3 class="text-2xl font-bold text-slate-900 mb-4 tracking-tight">Restricted Operation</h3>
            <p class="text-slate-500 font-medium mb-8 leading-relaxed">
                {{ $message ?? 'You do not have the necessary permissions to access this administrative module. Please contact your system administrator if you believe this is an error.' }}
            </p>

            <div class="space-y-4">
                <a href="{{ url()->previous() }}"
                    class="block w-full py-4 bg-slate-900 text-white rounded-2xl font-bold text-sm shadow-xl shadow-slate-200 hover:bg-black transition-all transform active:scale-95">
                    Return to Previous Page
                </a>
                <a href="{{ route('admin.dashboard') }}"
                    class="block w-full py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold text-sm hover:bg-slate-200 transition-all">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <p class="mt-8 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Security Protocol Active</p>
    </div>
@endsection