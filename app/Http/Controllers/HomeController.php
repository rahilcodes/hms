<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 🚀 TITANIUM DATA FETCHING
        // 1. Featured Rooms (Limit 3, Eager Load relationships if any)
        $featuredRooms = RoomType::where('base_price', '>', 0) // Basic sanity check
            ->take(3)
            ->get();

        // 2. CMS Content (Simulated for now if table missing, or fetch if exists)
        // Ideally we would fetch from a 'cms_pages' table where slug='home'
        // $cms = CmsPage::where('slug', 'home')->first(); 

        // For now, we rely on the global 'site_settings' injected via AppServiceProvider for text.
        // But we DO need to ensure the view gets the rooms.

        return view('home.index', compact('featuredRooms'));
    }
}