@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create New Page</h1>
                <p class="text-sm text-gray-500">Add a new page to your guest website.</p>
            </div>
            <a href="{{ route('admin.pages.index') }}"
                class="px-4 py-2 border border-gray-200 rounded-lg text-sm font-bold text-gray-600 hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>

        <form action="{{ route('admin.pages.store') }}" method="POST"
            class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">Page Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full rounded-lg border-gray-300 focus:ring-slate-900 focus:border-slate-900 shadow-sm placeholder-gray-300"
                        placeholder="e.g., Spa Services">
                    @error('title') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-bold text-gray-700">Meta Description (SEO)</label>
                    <input type="text" name="meta_description" value="{{ old('meta_description') }}"
                        class="w-full rounded-lg border-gray-300 focus:ring-slate-900 focus:border-slate-900 shadow-sm placeholder-gray-300"
                        placeholder="Short description for search engines">
                </div>
            </div>

            <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
                <p class="text-sm text-blue-800">
                    <strong>Tip:</strong> You will be able to add sections (Hero, Gallery, Features, etc.) after creating
                    the page.
                </p>
            </div>

            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg border border-gray-100">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-slate-900 focus:ring-slate-900 w-5 h-5">
                <label for="is_active" class="text-sm font-bold text-gray-700">Publish Immediately</label>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-100">
                <button type="submit"
                    class="px-8 py-3 bg-slate-900 text-white rounded-xl text-sm font-bold shadow-lg shadow-slate-900/20 hover:scale-[1.02] active:scale-[0.98] transition">
                    Create Page
                </button>
            </div>
        </form>
    </div>
@endsection