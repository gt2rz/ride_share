<?php

namespace App\Http\Controllers;

use App\Events\TripAccepted;
use App\Events\TripCreated;
use App\Events\TripEnded;
use App\Events\TripLocationUpdated;
use App\Events\TripStarted;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'origin' => 'bail|required',
            'destination' => 'bail|required',
            'destination_name' => 'bail|required|string',
        ]);
        
        $trip = $request->user()->trips()->create($request->only([
            'origin', 'destination', 'destination_name'
        ]));

        TripCreated::dispatch($trip, $request->user());       

        return $trip;
    }

    public function show(Request $request, Trip $trip){
        // Is the trip associated with the user?
        if($request->user()->id === $trip->user->id){
            return $trip;
        }   

        // Is the trip associated with the driver?
        if($request->user()->driver && $trip->driver){
            if($request->user()->driver->id === $trip->driver->id){
                return $trip;
            }
        }

        return response()->json(['message' => 'Cannot find this trip.'], 404);
    }

    public function accept(Request $request, Trip $trip){

        $request->validate([
            'driver_location' => 'bail|required'
        ]);

        $trip->update([
            'driver_id' => $request->user()->driver->id,
            'driver_location' => $request->user()->driver->location
        ]);

        $trip->load('driver.user');

        TripAccepted::dispatch($trip, $trip->user);

        return $trip;
    }

    public function start(Request $request, Trip $trip){
        $trip->update([
            'is_started' => true
        ]);

        $trip->load('driver.user');

        TripStarted::dispatch($trip, $request->user());

        return $trip;
    }

    public function end(Request $request, Trip $trip){
        $trip->update([
            'is_completed' => true
        ]);

        $trip->load('driver.user');

        TripEnded::dispatch($trip, $request->user());

        return $trip;
    }

    public function location(Request $request, Trip $trip){
        $request->validate([
            'driver_location' => 'bail|required'
        ]);

        $trip->update([
            'driver_location' => $request->driver_location
        ]);

        $trip->load('driver.user');

        TripLocationUpdated::dispatch($trip, $request->user());

        return $trip;
    }
    
}
