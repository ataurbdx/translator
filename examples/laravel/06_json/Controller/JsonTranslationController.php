<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Ataurbdx\Translator\Facades\Translator;

class JsonTranslationController extends Controller
{
    public function index()
    {
        return view('06_json.Views.json_demo');
    }

    /**
     * Export all database static keys to Laravel's resources/lang/bn.json file!
     */
    public function exportToDisk()
    {
        $targetFile = resource_path('lang/bn.json');

        // Exports database translator_statics keys into flat JSON file using natural text as keys:
        Translator::json()->exportFromDatabase('bn', $targetFile);

        return redirect()->back()->with('success', 'Database translations exported to resources/lang/bn.json!');
    }
}
