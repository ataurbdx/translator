<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class LocationController extends Controller
{
    public function index()
    {
        // Querying both models — both read their translations from translator_worlds seamlessly!
        $countries = Country::with('cities')->get();
        return view('04_hybrid.Views.location_demo', compact('countries'));
    }

    public function store(Request $request)
    {
        // 1. Create Country and save to translator_worlds
        $country = Country::create([
            'name' => $request->input('country_name.en'),
            'iso2' => strtoupper($request->input('iso2')),
        ]);
        $country->saveTranslations(['name' => $request->input('country_name')]);

        // 2. Create City and save to translator_worlds
        if ($request->filled('city_name.en')) {
            $city = City::create([
                'country_id' => $country->id,
                'name'       => $request->input('city_name.en'),
            ]);
            $city->saveTranslations(['name' => $request->input('city_name')]);
        }

        return redirect()->back()->with('success', 'Location cluster created in translator_worlds!');
    }
}
