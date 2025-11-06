<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Event::with('venue')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $event = new Event();
        $event->event_name = $request->input('event_name');
        $event->event_date = $request->input('event_date');
        $event->event_max_capacity = $request->input('event_max_capacity');
        $event->event_is_virtual = $request->input('event_is_virtual');

        if ($request->hasFile('event_image')) {
            $imagePath = $request->file('event_image')->store('events', 'public');
            $event->event_image = $imagePath;
        }

        $event->save();

        return $event;
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return $event->load('venue');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        if ($request->hasFile('event_image')) {
            // Eliminar imagen anterior si existe
            if ($event->event_image && Storage::disk('public')->exists($event->event_image)) {
                Storage::disk('public')->delete($event->event_image);
            }
            // Guardar nueva imagen
            $imagePath = $request->file('event_image')->store('events', 'public');
            $request->merge(['event_image' => $imagePath]);
        }

        if ($event->update($request->all())) {
            return response()->json(['success' => true, 'event' => $event]);
        }
        return response()->json(['success' => false]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Eliminar imagen si existe
        if ($event->event_image && Storage::disk('public')->exists($event->event_image)) {
            Storage::disk('public')->delete($event->event_image);
        }

        if ($event->delete()) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
