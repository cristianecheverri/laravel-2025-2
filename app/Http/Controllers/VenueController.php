<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVenueRequest;
use App\Http\Requests\UpdateVenueRequest;
use App\Models\Venue;

class VenueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Venue::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVenueRequest $request)
    {
        $venue = new Venue($request->validated());
        $venue->save();
        return response()->json(['success' => true, 'venue' => $venue]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Venue $venue)
    {
        return response()->json(['success' => true, 'venue' => $venue]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVenueRequest $request, Venue $venue)
    {
        if ($venue->update($request->validated())) {
            return response()->json(['success' => true, 'venue' => $venue]);
        }
        return response()->json(['success' => false, 'venue' => $venue]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venue $venue)
    {
        if ($venue->delete()) {
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
}
