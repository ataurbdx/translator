<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ListingController extends Controller
{
    public function index()
    {
        // Paginating 20 high-traffic listings with translations
        $listings = Listing::paginate(10);
        return view('03_external.Views.listing_demo', compact('listings'));
    }

    public function store(Request $request)
    {
        // 1. Create base listing
        $listing = Listing::create([
            'title'       => $request->input('title.en'),
            'description' => $request->input('description.en'),
            'address'     => $request->input('address.en'),
            'price'       => $request->input('price', 0),
        ]);

        // 2. Save translations to dedicated translator_listings table
        $listing->saveTranslations($request->only('title', 'description', 'address'));

        return redirect()->back()->with('success', 'Listing and dedicated translations saved!');
    }
}
