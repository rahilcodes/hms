@extends('layouts.app')

@section('content')
    @php
        $sections = $page->content ?? [];
        if (is_string($sections)) {
            // Fallback if it's still a string (legacy/seed issues)
            $sections = json_decode($sections, true);
        }
        // If it's still not an array (e.g. empty string or invalid json), treat as empty array
        if (!is_array($sections)) {
            // If it looks like HTML, render it as legacy text_image content or raw
            if (is_string($page->content) && strlen($page->content) > 0) {
                // Check if it's the old HTML format
                echo '<div class="pt-24 prose max-w-none mx-auto px-4">' . $page->content . '</div>';
                $sections = [];
            } else {
                $sections = [];
            }
        }
    @endphp

    @foreach($sections as $section)
        @if(isset($section['type']))
            @if($section['type'] === 'hero')
                <x-sections.hero :data="$section['data']" />
            @elseif($section['type'] === 'text_image')
                <x-sections.text_image :data="$section['data']" />
            @elseif($section['type'] === 'features')
                <x-sections.features :data="$section['data']" />
            @elseif($section['type'] === 'stats')
                <x-sections.stats :data="$section['data']" />
            @elseif($section['type'] === 'gallery')
                <x-sections.gallery :data="$section['data']" />
            @elseif($section['type'] === 'faq')
                <x-sections.faq :data="$section['data']" />
            @elseif($section['type'] === 'map')
                <x-sections.map :data="$section['data']" />
            @elseif($section['type'] === 'testimonials')
                <x-sections.testimonials :data="$section['data']" />
            @elseif($section['type'] === 'video')
                <x-sections.video :data="$section['data']" />
            @endif
        @endif
    @endforeach
@endsection