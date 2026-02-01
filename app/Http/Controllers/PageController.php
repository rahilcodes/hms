<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $page = \App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('pages.show', compact('page'));
    }

    public function about()
    {
        return $this->show('about');
    }

    public function contact()
    {
        return $this->show('contact');
    }

    public function dining()
    {
        return $this->show('dining');
    }

    public function gallery()
    {
        return $this->show('gallery');
    }
}
