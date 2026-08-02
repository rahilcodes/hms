@extends('layouts.admin')

@section('content')
    <div class="space-y-6" x-data="pageBuilder()">

        {{-- Header --}}
        <div class="flex items-center justify-between py-6 mb-6 border-b border-gray-200">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Editing: {{ $page->title }}</h1>
                <p class="text-sm text-gray-500">Drag and drop sections to build your page.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ url($page->slug == 'home' ? '/' : 'page/' . $page->slug) }}" target="_blank"
                    class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-600 hover:bg-gray-50 transition shadow-sm">
                    View Live
                </a>
                <button form="page-form" type="submit"
                    class="px-6 py-2 bg-slate-900 text-white rounded-lg text-sm font-bold shadow-lg shadow-slate-900/20 hover:scale-[1.02] active:scale-[0.98] transition">
                    Save Changes
                </button>
            </div>
        </div>

        <form id="page-form" action="{{ route('admin.pages.update', $page) }}" method="POST" class="space-y-8"
            @submit="prepareSubmit">
            @csrf
            @method('PUT')
            <input type="hidden" name="content_json" :value="JSON.stringify(sections)">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT: BUILDER CANVAS --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- No Sections State --}}
                    <div x-show="sections.length === 0"
                        class="text-center py-20 border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <p class="text-gray-500 font-medium">This page is empty.</p>
                        <p class="text-sm text-gray-400">Add a section from the right to get started.</p>
                    </div>

                    {{-- Sections List --}}
                    <template x-for="(section, index) in sections" :key="index">
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden group transition-all duration-200 hover:border-blue-300 hover:ring-2 hover:ring-blue-100 hover:shadow-lg relative"
                            draggable="true" @dragstart="dragStart($event, index)" @dragover.prevent
                            @drop="drop($event, index)">

                            {{-- Section Header --}}
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center cursor-move"
                                title="Drag to reorder">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="bg-slate-200 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider"
                                        x-text="section.type.replace('_', ' ')"></span>
                                    <span class="text-sm font-bold text-gray-700" x-text="getSectionTitle(section)"></span>
                                </div>
                                <div class="flex items-center gap-2 opacity-50 group-hover:opacity-100 transition">
                                    <button type="button" @click="moveUp(index)" class="p-1 hover:bg-gray-200 rounded"
                                        :disabled="index === 0"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7"></path>
                                        </svg></button>
                                    <button type="button" @click="moveDown(index)" class="p-1 hover:bg-gray-200 rounded"
                                        :disabled="index === sections.length - 1"><svg class="w-4 h-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg></button>
                                    <button type="button" @click="removeSection(index)"
                                        class="p-1 hover:bg-red-100 text-red-500 rounded ml-2"><svg class="w-4 h-4"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg></button>
                                </div>
                            </div>

                            {{-- Section Content Form --}}
                            <div class="p-6">

                                {{-- HERO --}}
                                <template x-if="section.type === 'hero'">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="col-span-2">
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase mb-1">Heading</label>
                                            <input type="text" x-model="section.data.heading"
                                                class="w-full p-2 border border-gray-300 rounded font-bold">
                                        </div>
                                        <div class="col-span-1">
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Badge
                                                (Optional)</label>
                                            <input type="text" x-model="section.data.badge"
                                                class="w-full p-2 border border-gray-300 rounded text-sm">
                                        </div>
                                        <div class="col-span-1">
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Background
                                                Image URL</label>
                                            <input type="text" x-model="section.data.image"
                                                class="w-full p-2 border border-gray-300 rounded text-sm placeholder-gray-300"
                                                placeholder="https://...">
                                        </div>
                                        <div class="col-span-2">
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase mb-1">Subheading</label>
                                            <textarea x-model="section.data.subheading" rows="2"
                                                class="w-full p-2 border border-gray-300 rounded text-sm"></textarea>
                                        </div>
                                    </div>
                                </template>

                                {{-- TEXT IMAGE --}}
                                <template x-if="section.type === 'text_image'">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase mb-1">Title</label>
                                                <input type="text" x-model="section.data.title"
                                                    class="w-full p-2 border border-gray-300 rounded font-bold">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Image
                                                    URL</label>
                                                <input type="text" x-model="section.data.image"
                                                    class="w-full p-2 border border-gray-300 rounded text-sm">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Content
                                                (HTML allowed)</label>
                                            <textarea x-model="section.data.content" rows="4"
                                                class="w-full p-2 border border-gray-300 rounded text-sm font-mono"></textarea>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Image
                                                Alignment</label>
                                            <select x-model="section.data.align"
                                                class="p-2 border border-gray-300 rounded text-sm">
                                                <option value="left">Left</option>
                                                <option value="right">Right</option>
                                            </select>
                                        </div>
                                    </div>
                                </template>

                                {{-- FEATURES --}}
                                <template x-if="section.type === 'features'">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Section
                                                Title</label>
                                            <input type="text" x-model="section.data.title"
                                                class="w-full p-2 border border-gray-300 rounded font-bold">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Feature
                                                Items</label>
                                            <template x-for="(item, i) in section.data.items" :key="i">
                                                <div
                                                    class="flex gap-2 items-start bg-gray-50 p-2 rounded border border-gray-200">
                                                    <div class="flex-1 grid gap-2">
                                                        <input type="text" x-model="item.title"
                                                            class="w-full p-1.5 border border-gray-300 rounded text-sm font-bold"
                                                            placeholder="Feature Title">
                                                        <input type="text" x-model="item.desc"
                                                            class="w-full p-1.5 border border-gray-300 rounded text-xs"
                                                            placeholder="Description">
                                                    </div>
                                                    <button type="button" @click="section.data.items.splice(i, 1)"
                                                        class="text-red-400 hover:text-red-600 p-1"><svg class="w-4 h-4"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg></button>
                                                </div>
                                            </template>
                                            <button type="button" @click="section.data.items.push({title:'', desc:''})"
                                                class="text-xs font-bold text-blue-600 hover:underline">+ Add
                                                Feature</button>
                                        </div>
                                    </div>
                                </template>

                                {{-- STATS --}}
                                <template x-if="section.type === 'stats'">
                                    <div class="space-y-4">
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Stat
                                                Items</label>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                                <template x-for="(item, i) in section.data.items" :key="i">
                                                    <div
                                                        class="bg-gray-50 p-2 rounded border border-gray-200 relative group">
                                                        <input type="text" x-model="item.value"
                                                            class="w-full p-1.5 border border-gray-300 rounded text-sm font-bold mb-1"
                                                            placeholder="Value (e.g. 10k)">
                                                        <input type="text" x-model="item.label"
                                                            class="w-full p-1.5 border border-gray-300 rounded text-xs"
                                                            placeholder="Label">
                                                        <button type="button" @click="section.data.items.splice(i, 1)"
                                                            class="absolute -top-1 -right-1 bg-red-100 text-red-500 rounded-full p-0.5 opacity-0 group-hover:opacity-100 transition"><svg
                                                                class="w-3 h-3" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg></button>
                                                    </div>
                                                </template>
                                                <button type="button" @click="section.data.items.push({value:'', label:''})"
                                                    class="border-2 border-dashed border-gray-200 rounded flex items-center justify-center text-gray-400 hover:border-blue-300 hover:text-blue-500 transition text-xs font-bold py-4">
                                                    + Add Stat
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- GALLERY --}}
                                <template x-if="section.type === 'gallery'">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Section
                                                Title</label>
                                            <input type="text" x-model="section.data.title"
                                                class="w-full p-2 border border-gray-300 rounded font-bold">
                                        </div>
                                        <div class="space-y-2">
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Images</label>
                                            <template x-for="(img, i) in section.data.images" :key="i">
                                                <div class="flex gap-2 items-center mb-2">
                                                    <div class="w-10 h-8 bg-gray-100 rounded border border-gray-200 flex-shrink-0 bg-cover bg-center"
                                                        :style="'background-image: url(' + img + ')'"></div>
                                                    <input type="text" x-model="section.data.images[i]"
                                                        class="w-full p-1.5 border border-gray-300 rounded text-sm"
                                                        placeholder="Image URL">
                                                    <button type="button" @click="section.data.images.splice(i, 1)"
                                                        class="text-red-400 hover:text-red-600"><svg class="w-4 h-4"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg></button>
                                                </div>
                                            </template>
                                            <button type="button" @click="section.data.images.push('')"
                                                class="text-xs font-bold text-blue-600 hover:underline">+ Add Image
                                                URL</button>
                                        </div>
                                    </div>
                                </template>

                                {{-- FAQ --}}
                                <template x-if="section.type === 'faq'">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Section
                                                Title</label>
                                            <input type="text" x-model="section.data.title"
                                                class="w-full p-2 border border-gray-300 rounded font-bold">
                                        </div>
                                        <div class="space-y-3">
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Q&A Items</label>
                                            <template x-for="(item, i) in section.data.items" :key="i">
                                                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                                    <div class="flex justify-between items-start mb-2">
                                                        <span class="text-[10px] uppercase font-bold text-gray-400"
                                                            x-text="'Question ' + (i+1)"></span>
                                                        <button type="button" @click="section.data.items.splice(i, 1)"
                                                            class="text-red-400 hover:text-red-600"><svg class="w-3 h-3"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg></button>
                                                    </div>
                                                    <input type="text" x-model="item.question"
                                                        class="w-full p-2 border border-gray-300 rounded text-sm font-bold mb-2 outline-none focus:ring-1 focus:ring-blue-500"
                                                        placeholder="Question">
                                                    <textarea x-model="item.answer" rows="2"
                                                        class="w-full p-2 border border-gray-300 rounded text-xs outline-none focus:ring-1 focus:ring-blue-500"
                                                        placeholder="Answer"></textarea>
                                                </div>
                                            </template>
                                            <button type="button" @click="section.data.items.push({question:'', answer:''})"
                                                class="text-xs font-bold text-blue-600 hover:underline">+ Add
                                                Question</button>
                                        </div>
                                    </div>
                                </template>

                                {{-- MAP --}}
                                <template x-if="section.type === 'map'">
                                    <div class="space-y-4">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase mb-1">Title</label>
                                                <input type="text" x-model="section.data.title"
                                                    class="w-full p-2 border border-gray-300 rounded font-bold">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Address
                                                    Display</label>
                                                <input type="text" x-model="section.data.address"
                                                    class="w-full p-2 border border-gray-300 rounded text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                {{-- TESTIMONIALS --}}
                                <template x-if="section.type === 'testimonials'">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Section
                                                Title</label>
                                            <input type="text" x-model="section.data.title"
                                                class="w-full p-2 border border-gray-300 rounded font-bold">
                                        </div>
                                        <div class="space-y-3">
                                            <label class="block text-xs font-bold text-gray-500 uppercase">Reviews</label>
                                            <template x-for="(item, i) in section.data.items" :key="i">
                                                <div class="bg-gray-50 p-3 rounded border border-gray-200">
                                                    <div class="grid grid-cols-2 gap-2 mb-2">
                                                        <input type="text" x-model="item.name"
                                                            class="w-full p-1.5 border border-gray-300 rounded text-sm font-bold"
                                                            placeholder="Guest Name">
                                                        <input type="text" x-model="item.location"
                                                            class="w-full p-1.5 border border-gray-300 rounded text-xs"
                                                            placeholder="Location">
                                                    </div>
                                                    <textarea x-model="item.text" rows="2"
                                                        class="w-full p-2 border border-gray-300 rounded text-xs"
                                                        placeholder="Review Text"></textarea>
                                                    <button type="button" @click="section.data.items.splice(i, 1)"
                                                        class="text-xs text-red-500 hover:underline mt-1">Remove</button>
                                                </div>
                                            </template>
                                            <button type="button"
                                                @click="section.data.items.push({name:'', location:'', text:''})"
                                                class="text-xs font-bold text-blue-600 hover:underline">+ Add
                                                Testimonial</button>
                                        </div>
                                    </div>
                                </template>

                                {{-- VIDEO --}}
                                <template x-if="section.type === 'video'">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Section
                                                Title</label>
                                            <input type="text" x-model="section.data.title"
                                                class="w-full p-2 border border-gray-300 rounded font-bold">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Video
                                                    URL</label>
                                                <input type="text" x-model="section.data.video"
                                                    class="w-full p-2 border border-gray-300 rounded text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Poster
                                                    URL</label>
                                                <input type="text" x-model="section.data.poster"
                                                    class="w-full p-2 border border-gray-300 rounded text-sm">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                            </div>
                        </div>
                    </template>
                </div>

                {{-- RIGHT: SETTINGS --}}
                            <div class="space-y-6">

                                {{-- Page Settings --}}
                                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                                    <h3 class="font-bold text-gray-800 mb-4">Page Settings</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Title</label>
                                            <input type="text" name="title" value="{{ old('title', $page->title) }}"
                                                class="w-full p-2 border border-gray-300 rounded">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Slug</label>
                                            <input type="text" name="slug" value="{{ old('slug', $page->slug) }}"
                                                class="w-full p-2 border border-gray-300 rounded bg-gray-50" {{ $page->is_system ? 'readonly' : '' }}>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">SEO
                                                Description</label>
                                            <textarea name="meta_description" rows="3"
                                                class="w-full p-2 border border-gray-300 rounded text-sm">{{ old('meta_description', $page->meta_description) }}</textarea>
                                        </div>
                                        <div class="flex items-center gap-2 pt-2">
                                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-slate-900 focus:ring-slate-900">
                                            <label for="is_active" class="text-sm font-bold text-gray-700">Published</label>
                                        </div>
                                    </div>
                                </div>

                                {{-- Add Section --}}
                                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm sticky top-10">
                                    <h3 class="font-bold text-gray-800 mb-4">Add Section</h3>
                                    <div class="grid grid-cols-2 gap-2">
                                        <button type="button" @click="addSection('hero')"
                                            class="p-3 text-left border border-gray-200 hover:border-blue-400 rounded-lg hover:bg-blue-50 transition group">
                                            <span
                                                class="block text-xs font-bold text-gray-500 group-hover:text-blue-600 uppercase">Hero</span>
                                        </button>
                                        <button type="button" @click="addSection('text_image')"
                                            class="p-3 text-left border border-gray-200 hover:border-blue-400 rounded-lg hover:bg-blue-50 transition group">
                                            <span
                                                class="block text-xs font-bold text-gray-500 group-hover:text-blue-600 uppercase">Text
                                                + Img</span>
                                        </button>
                                        <button type="button" @click="addSection('features')"
                                            class="p-3 text-left border border-gray-200 hover:border-blue-400 rounded-lg hover:bg-blue-50 transition group">
                                            <span
                                                class="block text-xs font-bold text-gray-500 group-hover:text-blue-600 uppercase">Features</span>
                                        </button>
                                        <button type="button" @click="addSection('stats')"
                                            class="p-3 text-left border border-gray-200 hover:border-blue-400 rounded-lg hover:bg-blue-50 transition group">
                                            <span
                                                class="block text-xs font-bold text-gray-500 group-hover:text-blue-600 uppercase">Stats</span>
                                        </button>
                                        <button type="button" @click="addSection('gallery')"
                                            class="p-3 text-left border border-gray-200 hover:border-blue-400 rounded-lg hover:bg-blue-50 transition group">
                                            <span
                                                class="block text-xs font-bold text-gray-500 group-hover:text-blue-600 uppercase">Gallery</span>
                                        </button>
                                        <button type="button" @click="addSection('faq')"
                                            class="p-3 text-left border border-gray-200 hover:border-blue-400 rounded-lg hover:bg-blue-50 transition group">
                                            <span
                                                class="block text-xs font-bold text-gray-500 group-hover:text-blue-600 uppercase">FAQ</span>
                                        </button>
                                        <button type="button" @click="addSection('map')"
                                            class="p-3 text-left border border-gray-200 hover:border-blue-400 rounded-lg hover:bg-blue-50 transition group">
                                            <span
                                                class="block text-xs font-bold text-gray-500 group-hover:text-blue-600 uppercase">Map</span>
                                        </button>
                                        <button type="button" @click="addSection('testimonials')"
                                            class="p-3 text-left border border-gray-200 hover:border-blue-400 rounded-lg hover:bg-blue-50 transition group">
                                            <span
                                                class="block text-xs font-bold text-gray-500 group-hover:text-blue-600 uppercase">Reviews</span>
                                        </button>
                                        <button type="button" @click="addSection('video')"
                                            class="p-3 text-left border border-gray-200 hover:border-blue-400 rounded-lg hover:bg-blue-50 transition group">
                                            <span
                                                class="block text-xs font-bold text-gray-500 group-hover:text-blue-600 uppercase">Video</span>
                                        </button>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
        </form>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pageBuilder', () => ({
                    sections: @json(is_string($page->content) ? [] : ($page->content ?? [])),
                    draggingIndex: null,

                    addSection(type) {
                        let newSection = { type: type, data: {} };

                        // Defaults
                        if (type === 'hero') newSection.data = { heading: 'New Hero', badge: 'Welcome', subheading: 'Add your subtitle here.' };
                        if (type === 'text_image') newSection.data = { title: 'Our Story', content: '<p>Content goes here...</p>', align: 'left' };
                        if (type === 'features') newSection.data = { title: 'Features', items: [{ title: 'Feature 1', desc: 'Description' }] };
                        if (type === 'stats') newSection.data = { items: [{ value: '100', label: 'Items' }] };
                        if (type === 'gallery') newSection.data = { title: 'Gallery', images: [''] };
                        if (type === 'faq') newSection.data = { title: 'FAQ', items: [{ question: 'Q?', answer: 'A.' }] };
                        if (type === 'map') newSection.data = { title: 'Our Location', address: '123 Street' };
                        if (type === 'testimonials') newSection.data = { title: 'What Guests Say', items: [{ name: 'John', location: 'USA', text: 'Amazing stay!' }] };
                        if (type === 'video') newSection.data = { title: 'Experience It', video: '', poster: '' };

                        this.sections.push(newSection);
                    },

                    removeSection(index) {
                        if (confirm('Remove this section?')) {
                            this.sections.splice(index, 1);
                        }
                    },

                    moveUp(index) {
                        if (index > 0) {
                            let temp = this.sections[index];
                            this.sections[index] = this.sections[index - 1];
                            this.sections[index - 1] = temp;
                        }
                    },

                    moveDown(index) {
                        if (index < this.sections.length - 1) {
                            let temp = this.sections[index];
                            this.sections[index] = this.sections[index + 1];
                            this.sections[index + 1] = temp;
                        }
                    },

                    getSectionTitle(section) {
                        if (section.data.heading) return section.data.heading;
                        if (section.data.title) return section.data.title;
                        return section.type;
                    },

                    // Drag and Drop
                    dragStart(e, index) {
                        this.draggingIndex = index;
                        e.dataTransfer.effectAllowed = 'move';
                    },
                    drop(e, targetIndex) {
                        if (this.draggingIndex === null) return;

                        // Simple swap or move logic
                        const item = this.sections.splice(this.draggingIndex, 1)[0];
                        this.sections.splice(targetIndex, 0, item);
                        this.draggingIndex = null;
                    }
                }));
            });
        </script>
    </div>
@endsection