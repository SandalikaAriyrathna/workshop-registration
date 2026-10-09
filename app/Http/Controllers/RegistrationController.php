<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Workshop $workshop)
    {
        if (
            $workshop->status !== 'scheduled' ||
            $workshop->starts_at->isPast()
        ) {
            return redirect()
                ->route('workshops.show', $workshop)
                ->with('error', 'Registration is closed for this workshop.');
        }

        $activeCount = $workshop->activeRegistrations()->count();

        if ($activeCount >= $workshop->capacity) {
            return redirect()
                ->route('workshops.show', $workshop)
                ->with('error', 'This workshop is fully booked.');
        }

        return view('registrations.create', compact('workshop'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'workshop_id' => 'required|exists:workshops,id',
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
        ]);

        DB::transaction(function () use ($validated) {

            // Lock the workshop row until this transaction finishes
            $workshop = Workshop::lockForCapacity((int) $validated['workshop_id']);

            // Only scheduled future workshops accept registrations
            if (
                $workshop->status !== 'scheduled' ||
                $workshop->starts_at->isPast()
            ) {
                throw ValidationException::withMessages([
                    'workshop_id' => 'This workshop is not open for registration.',
                ]);
            }

            // Count currently active registrations
            $activeCount = $workshop->activeRegistrations()->count();

            // Reject registration when capacity is reached
            if ($activeCount >= $workshop->capacity) {
                throw ValidationException::withMessages([
                    'workshop_id' => 'This workshop is fully booked.',
                ]);
            }

            // Create the registration
            Registration::create([
                'workshop_id' => $workshop->id,
                'attendee_name' => $validated['attendee_name'],
                'attendee_email' => $validated['attendee_email'],
                'status' => 'active',
                'registered_by' => auth()->id(),
                'registered_at' => now(),
            ]);

        }, 3);

        return redirect()
            ->route('workshops.index')
            ->with('success', 'Attendee registered successfully!');
    }


    public function cancel(Registration $registration)
    {
        DB::transaction(function () use ($registration) {

            Workshop::lockForCapacity($registration->workshop_id);

            $registration = Registration::whereKey($registration->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($registration->status !== 'active') {
                throw ValidationException::withMessages([
                    'registration' => 'This registration is already cancelled.',
                ]);
            }

            $registration->update([
                'status' => 'cancelled',
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now(),
            ]);

        }, 3);

        return redirect()
            ->route('workshops.show', $registration->workshop_id)
            ->with('success', 'Registration cancelled successfully.');
    }
}
