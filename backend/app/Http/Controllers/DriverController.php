<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request){
        // return back the user and associated driver
        $user = $request->user();
        $user->load('driver');

        return $user;

    }


    public function update(Request $request){
        $request->validate([
            'year' => 'bail|required|numeric|between:2010,2024',
            'make' => 'bail|required|string',
            'model' => 'bail|required|string',
            'color' => 'bail|required|alpha',
            'license_plate' => 'bail|required|string',
            'name' => 'bail|required|string',
        ]);

        $user = $request->user();

        $user->update($request->only('name'));

        // Create or update a driver associated with this user
        $user->driver()->updateOrCreate($request->only([
            'year', 'make', 'model', 'color', 'license_plate'
        ]));

        $user->load('driver');

        return $user;
    }
}
