@extends('layouts.admin')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pricing Rules</h1>
            <p class="text-sm text-gray-500">Set custom prices for weekends or specific seasons.</p>
        </div>
        <a href="{{ route('admin.pricing-rules.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-sm transition">
            + Add New Rule
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- 1. WEEKEND RATES --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Recurring Weekend Rates</h3>
                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">Fri & Sat Nights</span>
            </div>

            <table class="min-w-full divide-y divide-gray-100">
                <tbody class="divide-y divide-gray-50">
                    @forelse($rules->where('type', 'weekend') as $rule)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-4 py-3 text-sm text-gray-700 font-medium">
                                {{ $rule->roomType->name ?? 'Unknown Room' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-bold text-green-600">₹{{ number_format($rule->price) }}</span>
                                <span class="text-xs text-gray-400 block">/night</span>
                            </td>
                            <td class="px-4 py-3 text-right w-10">
                                <form action="{{ route('admin.pricing-rules.destroy', $rule) }}" method="POST"
                                    onsubmit="return confirm('Remove this weekend rate?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition">
                                        &times;
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400 text-sm">
                                No weekend rates set.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- 2. SEASONAL RATES --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Seasonal Overrides</h3>
                <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded border">Specific Dates</span>
            </div>

            <table class="min-w-full divide-y divide-gray-100">
                <tbody class="divide-y divide-gray-50">
                    @forelse($rules->where('type', 'season') as $rule)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <div class="font-medium">{{ $rule->roomType->name ?? 'Unknown Room' }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ $rule->start_date->format('M d') }} - {{ $rule->end_date->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="font-bold text-blue-600">₹{{ number_format($rule->price) }}</span>
                                <span class="text-xs text-gray-400 block">/night</span>
                            </td>
                            <td class="px-4 py-3 text-right w-10">
                                <form action="{{ route('admin.pricing-rules.destroy', $rule) }}" method="POST"
                                    onsubmit="return confirm('Remove this seasonal rate?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition">
                                        &times;
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-400 text-sm">
                                No seasonal overrides found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection