<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Workshop::query()
            ->withCount('activeRegistrations');

        // Filter by start date
        if ($request->filled('date_from')) {
            $query->whereDate(
                'starts_at',
                '>=',
                $request->date_from
            );
        }

        // Filter by end date
        if ($request->filled('date_to')) {
            $query->whereDate(
                'starts_at',
                '<=',
                $request->date_to
            );
        }

        // Filter by workshop status
        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        // Filter workshops with available seats
        if ($request->boolean('available_only')) {
            $query->whereRaw(
                "capacity > (
                    SELECT COUNT(*)
                    FROM registrations
                    WHERE registrations.workshop_id = workshops.id
                    AND registrations.status = 'active'
                )"
            );
        }

        $workshops = $query
            ->orderBy('starts_at')
            ->get();

        return view('workshops.index', compact('workshops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('workshops.create');
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:workshops,code',
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'starts_at' => 'required|date|after:now',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:scheduled,cancelled,completed',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Workshop::create($validated);

        return redirect()
            ->route('workshops.index')
            ->with('success', 'Workshop created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Workshop $workshop)
    {
        $registrations = $workshop->registrations()
            ->with(['registeredBy', 'cancelledBy'])
            ->latest()
            ->get();

        $activeCount = $workshop->activeRegistrations()->count();

        $availableSeats = max(0, $workshop->capacity - $activeCount);

        return view('workshops.show', compact(
            'workshop',
            'registrations',
            'activeCount',
            'availableSeats'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
   public function edit(Workshop $workshop)
    {
        return view('workshops.edit', compact('workshop'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('workshops', 'code')->ignore($workshop->id),
            ],
            'title' => 'required|string|max:255',
            'instructor' => 'required|string|max:255',
            'starts_at' => 'required|date',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:scheduled,cancelled,completed',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $workshop->update($validated);

        return redirect()
            ->route('workshops.index')
            ->with('success', 'Workshop updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshop $workshop)
    {
        //
    }
}
