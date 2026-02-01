<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingGuest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GuestRegistrationController extends Controller
{
    public function show($uuid)
    {
        $booking = Booking::where('uuid', $uuid)->with('guests', 'roomType', 'hotel')->firstOrFail();

        // Check if already completed? Maybe allow editing until check-in?
        // For now, allow viewing/editing always.

        // Get primary guest
        $guest = $booking->guests->first();
        if (!$guest) {
            // Should not happen for valid bookings, but handle edge case
            $guest = new BookingGuest(['booking_id' => $booking->id]);
        }

        return view('guest.registration.show', compact('booking', 'guest'));
    }

    public function update(Request $request, $uuid)
    {
        $booking = Booking::where('uuid', $uuid)->firstOrFail();
        $guest = $booking->guests->first();

        $validated = $request->validate([
            'email' => 'nullable|email',
            'nationality' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'purpose_of_visit' => 'nullable|string|max:200',
            'id_proof' => 'nullable|image|max:10240', // 10MB
            'signature' => 'nullable|string', // Base64 data URI
        ]);

        // Handle ID Proof Upload
        if ($request->hasFile('id_proof')) {
            $path = $request->file('id_proof')->store('guest-ids', 'public');
            $guest->id_proof_path = $path;
        }

        // Handle Signature (Base64)
        if (!empty($validated['signature'])) {
            $sigData = $validated['signature'];
            // Data URI format: "data:image/png;base64,iVBOR..."
            if (preg_match('/^data:image\/(\w+);base64,/', $sigData, $type)) {
                $sigData = substr($sigData, strpos($sigData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    // Invalid image type
                }
                $sigData = base64_decode($sigData);

                $filename = 'signatures/' . Str::random(20) . '.' . $type;
                Storage::disk('public')->put($filename, $sigData);

                $guest->signature_path = $filename;
            }
        }

        $guest->update([
            'email' => $validated['email'],
            'nationality' => $validated['nationality'],
            'address' => $validated['address'],
            'purpose_of_visit' => $validated['purpose_of_visit'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Registration details saved successfully!');
    }
}
