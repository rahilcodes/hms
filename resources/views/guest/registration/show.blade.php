@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8" x-data="{ 
                 step: 1, 
                 signaturePad: null,
                 idPreview: null,
                 initPad() {
                     const canvas = document.getElementById('signature-pad');
                     if(!canvas) return;

                     // Simple canvas signature implementation
                     const ctx = canvas.getContext('2d');
                     let writing = false;

                     // Scale for retina
                     const ratio = Math.max(window.devicePixelRatio || 1, 1);
                     canvas.width = canvas.offsetWidth * ratio;
                     canvas.height = canvas.offsetHeight * ratio;
                     ctx.scale(ratio, ratio);
                     ctx.lineWidth = 2;
                     ctx.lineCap = 'round';
                     ctx.strokeStyle = '#0f172a'; // slate-900

                     const getPos = (e) => {
                         const rect = canvas.getBoundingClientRect();
                         const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                         const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                         return { x: clientX - rect.left, y: clientY - rect.top };
                     }

                     const start = (e) => {
                         e.preventDefault();
                         writing = true;
                         const pos = getPos(e);
                         ctx.beginPath();
                         ctx.moveTo(pos.x, pos.y);
                     }

                     const move = (e) => {
                         if(!writing) return;
                         e.preventDefault();
                         const pos = getPos(e);
                         ctx.lineTo(pos.x, pos.y);
                         ctx.stroke();
                     }

                     const end = () => {
                         writing = false;
                         document.getElementById('signature-input').value = canvas.toDataURL();
                     }

                     canvas.addEventListener('mousedown', start);
                     canvas.addEventListener('mousemove', move);
                     canvas.addEventListener('mouseup', end);
                     canvas.addEventListener('touchstart', start);
                     canvas.addEventListener('touchmove', move);
                     canvas.addEventListener('touchend', end);

                     // Clear btn
                     window.clearPad = () => {
                         ctx.clearRect(0, 0, canvas.width, canvas.height);
                         document.getElementById('signature-input').value = '';
                     }
                 }
             }">

        <div class="max-w-3xl mx-auto">
            {{-- HEADER --}}
            <div class="text-center mb-10">
                <h1 class="text-3xl font-black text-slate-900 mb-2">Welcome to {{ $booking->hotel->name ?? 'LuxeStay' }}
                </h1>
                <p class="text-slate-500 font-medium">Please complete your contactless registration before arrival.</p>
            </div>

            {{-- PROGRESS --}}
            <div
                class="mb-8 flex items-center justify-center gap-4 text-xs font-bold uppercase tracking-widest text-slate-400">
                <span :class="{'text-blue-600': step >= 1}">1. Details</span>
                <span class="w-8 h-px bg-slate-200"></span>
                <span :class="{'text-blue-600': step >= 2}">2. ID Proof</span>
                <span class="w-8 h-px bg-slate-200"></span>
                <span :class="{'text-blue-600': step >= 3}">3. Sign</span>
            </div>

            <form action="{{ route('guest.registration.update', $booking->uuid) }}" method="POST"
                enctype="multipart/form-data"
                class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden relative">
                @csrf

                {{-- STEP 1: DETAILS --}}
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    class="p-8 space-y-6">

                    <h2 class="text-xl font-bold text-slate-900">Guest Information</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Full Name</label>
                            <input type="text" value="{{ $guest->name }}" disabled
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-500 cursor-not-allowed font-medium">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phone Number</label>
                            <input type="text" value="{{ $guest->phone }}" disabled
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-500 cursor-not-allowed font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email Adddress</label>
                        <input type="email" name="email" value="{{ old('email', $guest->email) }}" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium text-slate-900 placeholder:text-slate-300"
                            placeholder="your@email.com">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nationality</label>
                            <input type="text" name="nationality" value="{{ old('nationality', $guest->nationality) }}"
                                required
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium text-slate-900 placeholder:text-slate-300"
                                placeholder="e.g. American">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Purpose of Visit</label>
                            <select name="purpose_of_visit"
                                class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium text-slate-900">
                                <option value="Leisure">Leisure / Vacation</option>
                                <option value="Business">Business</option>
                                <option value="Family">Visiting Family</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Residential Address</label>
                        <textarea name="address" rows="3" required
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 font-medium text-slate-900 placeholder:text-slate-300"
                            placeholder="Street, City, Postcode, Country">{{ old('address', $guest->address) }}</textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="button" @click="step++"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold transition flex items-center gap-2">
                            Next Step
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- STEP 2: ID PROOF --}}
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300 transform" x-cloak
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    class="p-8 space-y-6">

                    <h2 class="text-xl font-bold text-slate-900">Valid ID Proof</h2>
                    <p class="text-slate-500 text-sm">Please upload a clear photo of your Passport, Driver's License, or
                        National ID.</p>

                    <div
                        class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center hover:bg-slate-50 transition relative">
                        <input type="file" name="id_proof" accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                            @change="idPreview = URL.createObjectURL($event.target.files[0])">

                        <div x-show="!idPreview">
                            <div
                                class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="font-bold text-slate-900">Click to Upload or Capture</p>
                            <p class="text-xs text-slate-400 mt-1">JPG, PNG up to 10MB</p>
                        </div>

                        <div x-show="idPreview" class="relative">
                            <img :src="idPreview" class="max-h-64 mx-auto rounded-lg shadow-sm">
                            <button type="button" @click.prevent="idPreview = null"
                                class="absolute top-2 right-2 bg-white/90 p-1 rounded-full text-rose-500 shadow-sm hover:bg-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-between pt-4">
                        <button type="button" @click="step--"
                            class="text-slate-500 hover:text-slate-800 font-bold px-4 py-3 transition">
                            Back
                        </button>
                        <button type="button" @click="step++"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-bold transition flex items-center gap-2">
                            Next: Sign
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- STEP 3: SIGNATURE --}}
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-300 transform" x-cloak
                    x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                    x-init="$watch('step', value => { if(value === 3) $nextTick(() => initPad()) })" class="p-8 space-y-6">

                    <h2 class="text-xl font-bold text-slate-900">Digital Signature</h2>
                    <p class="text-slate-500 text-sm">Please sign in the box below to confirm your details and acceptance of
                        hotel terms.</p>

                    <div class="border border-slate-200 rounded-2xl bg-white shadow-inner relative overflow-hidden"
                        style="touch-action: none;"> {{-- Prevent scrolling while signing --}}
                        <canvas id="signature-pad" class="w-full h-48 cursor-crosshair"></canvas>
                        <input type="hidden" name="signature" id="signature-input">

                        <button type="button" onclick="clearPad()"
                            class="absolute top-2 right-2 text-xs font-bold text-slate-400 hover:text-rose-500 bg-white/80 px-2 py-1 rounded-lg border border-slate-100 shadow-sm">
                            Clear
                        </button>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl flex gap-3 text-xs text-blue-800 leading-relaxed font-medium">
                        <svg class="w-5 h-5 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        By clicking Confirm Registration, I acknowledge that all details provided are accurate and I agree
                        to the hotel's terms and conditions.
                    </div>

                    <div class="flex justify-between pt-4" x-data="{ submitting: false }">
                        <button type="button" @click="step--" :disabled="submitting"
                            class="text-slate-500 hover:text-slate-800 font-bold px-4 py-3 transition disabled:opacity-30">
                            Back
                        </button>
                        <button type="submit"
                            @click="if(!document.getElementById('signature-input').value) { alert('Please provide your signature'); return false; } submitting = true;"
                            :disabled="submitting"
                            class="bg-emerald-500 hover:bg-emerald-600 text-white px-8 py-3 rounded-xl font-bold transition flex items-center gap-2 shadow-lg shadow-emerald-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="submitting" class="animate-spin h-4 w-4 text-white" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span x-text="submitting ? 'Saving...' : 'Confirm Registration'"></span>
                        </button>
                    </div>
                </div>

            </form>

            <div class="text-center mt-8 space-y-2">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Questions?</p>
                <a href="#" class="text-blue-600 font-bold text-sm hover:underline">Contact Front Desk</a>
            </div>
        </div>
    </div>
@endsection