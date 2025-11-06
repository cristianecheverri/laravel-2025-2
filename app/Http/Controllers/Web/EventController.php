<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Events/Index', [
            'events' => Event::with('venue')->get(),
            'venues' => Venue::all(),
            'message' => session('message')
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_is_virtual' => 'boolean',
            'event_speaker_name' => 'required|string|max:255',
            'fk_venue_event' => 'nullable|exists:venues,id',
            'event_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('event_image')) {
            $validated['event_image'] = $request->file('event_image')->store('events', 'public');
        }

        Event::create($validated);

        return redirect()->route('events.index')
            ->with('message', 'Event created successfully.');
    }

    public function create()
    {
        return Inertia::render('Events/Create', [
            'venues' => Venue::all(),
            'selectedVenue' => request('venue_id')
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return Inertia::render('Events/Show', [
            'event' => $event->load('venue')
        ]);
    }

    public function edit(Event $event)
    {
        return Inertia::render('Events/Edit', [
            'event' => $event,
            'venues' => Venue::all()
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_is_virtual' => 'boolean',
            'event_speaker_name' => 'required|string|max:255',
            'fk_venue_event' => 'nullable|exists:venues,id',
            'event_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('event_image')) {
            // Eliminar imagen anterior si existe
            if ($event->event_image && Storage::disk('public')->exists($event->event_image)) {
                Storage::disk('public')->delete($event->event_image);
            }
            // Guardar nueva imagen
            $validated['event_image'] = $request->file('event_image')->store('events', 'public');
        }

        $event->update($validated);

        return redirect()->route('events.index')
            ->with('message', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        // Eliminar imagen si existe
        if ($event->event_image && Storage::disk('public')->exists($event->event_image)) {
            Storage::disk('public')->delete($event->event_image);
        }

        $event->delete();

        return redirect()->route('events.index')
            ->with('message', 'Event deleted successfully.');
    }
}
