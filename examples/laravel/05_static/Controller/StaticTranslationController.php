<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ataurbdx\Translator\Facades\Translator;
use Ataurbdx\Translator\Modules\StaticUI\Models\TranslatorStatic;

class StaticTranslationController extends Controller
{
    public function index()
    {
        $statics = TranslatorStatic::all();
        return view('05_static.Views.static_demo', compact('statics'));
    }

    public function store(Request $request)
    {
        // Add or update a static UI key in database via fluent Facade:
        Translator::static()->set(
            key: $request->input('key'),
            name: $request->input('name'), // Default fallback text
            values: $request->input('values'), // ['en' => '...', 'bn' => '...', 'es' => '...']
            group: $request->input('group', 'general')
        );

        return redirect()->back()->with('success', 'Static translation key saved in DB!');
    }
}
