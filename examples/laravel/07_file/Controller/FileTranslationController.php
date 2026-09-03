<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Ataurbdx\Translator\Facades\Translator;

class FileTranslationController extends Controller
{
    public function index()
    {
        return view('07_file.Views.file_demo');
    }

    /**
     * Import an existing legacy PHP language file into Translator database
     */
    public function importPhpToDb()
    {
        $filePath = resource_path('lang/bn/messages.php');
        
        // Imports all nested array keys from PHP file into translator_statics table:
        Translator::file()->importFromPhpFile($filePath, 'bn', 'messages');

        return redirect()->back()->with('success', 'PHP language file imported into Database!');
    }
}
