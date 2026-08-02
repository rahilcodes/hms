<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $categories = MenuCategory::with('items')->ordered()->get();
        return view('admin.dining.menu.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        MenuCategory::create($request->only('name'));
        return back()->with('success', 'Category created');
    }

    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'menu_category_id' => 'required|exists:menu_categories,id',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048' // 2MB Max
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu', 'public');
        }

        MenuItem::create($validated);
        return back()->with('success', 'Menu item added');
    }

    public function updateItem(Request $request, MenuItem $item)
    {
        $validated = $request->validate([
            'menu_category_id' => 'sometimes|exists:menu_categories,id',
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu', 'public');
        }

        $item->update($validated);
        return back()->with('success', 'Menu item updated');
    }

    public function toggleItem(MenuItem $item)
    {
        $item->update(['is_available' => !$item->is_available]);
        return back()->with('success', 'Item status updated');
    }

    public function deleteItem(MenuItem $item)
    {
        $item->delete();
        return back()->with('success', 'Item deleted');
    }
}
