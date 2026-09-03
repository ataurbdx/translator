<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return view('01_inline.Views.tag_demo', compact('tags'));
    }

    public function store(Request $request)
    {
        // 1. Create with raw localized payload:
        // ['name' => ['en' => 'Gadgets', 'bn' => 'গ্যাজেট', 'es' => 'Artilugios'], 'slug' => 'gadgets']
        $tag = Tag::create([
            'slug' => $request->input('slug'),
        ]);

        // 2. Save translations seamlessly
        $tag->saveTranslations($request->only('name'));

        return redirect()->back()->with('success', 'Tag created successfully!');
    }
}
