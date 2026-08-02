<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('is_system', 'desc')->orderBy('title')->get();
        return view('admin.pages.index', compact('pages'));
    }

    public function create()
    {
        return view('admin.pages.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'meta_description' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['title']);

        // Ensure slug uniqueness
        if (Page::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] .= '-' . time();
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['is_system'] = false;
        $validated['content'] = []; // Initialize empty content array

        $page = Page::create($validated);

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Page created. Now start building!');
    }

    public function edit(Page $page)
    {
        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'meta_description' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Handle Content Builder
        if ($request->has('content_json')) {
            $validated['content'] = json_decode($request->input('content_json'), true);
        }

        // Prevent changing slug of system pages
        if ($page->is_system) {
            unset($validated['slug']);
        }

        $page->update($validated);

        return back()->with('success', 'Page saved successfully.');
    }

    public function destroy(Page $page)
    {
        if ($page->is_system) {
            return back()->with('error', 'System pages cannot be deleted.');
        }

        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully.');
    }
}
