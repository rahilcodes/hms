<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    public function edit()
    {
        $settings = SiteSetting::pluck('value', 'key')->toArray();
        return view('admin.content.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Content updated successfully');
    }
}
