<?php

namespace Mkhodroo\Cities\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mkhodroo\Cities\Models\City;

class CityViewController extends Controller
{
    public function index()
    {
        return view('CitiesViews::index');
    }

    public function create(Request $request)
    {
        $city = City::create([
            'city' => $request->city,
            'province' => $request->province,
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ]);
        return $city;
    }

    public function list()
    {
        $cities = City::all();
        return [
            'data' => $cities
        ];
    }

    public function edit(Request $request)
    {

        $city = City::whereId($request->id)->first();
        return view('CitiesViews::edit', compact('city'));
    }

    public function update(Request $request)
    {
        $city = City::whereId($request->id)->first();
        $city->city = $request->city;
        $city->province = $request->province;
        $city->longitude = $request->longitude;
        $city->latitude = $request->latitude;
        $city->save();
        return response(trans("update ok"));
    }

}
