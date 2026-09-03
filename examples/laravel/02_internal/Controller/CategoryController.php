<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CategoryController extends Controller
{
    public function index()
    {
        // withTranslations() eager-loads translator_dynamics, eliminating N+1 query problems!
        $categories = Category::withTranslations()->get();
        return view('02_internal.Views.category_demo', compact('categories'));
    }

    public function store(Request $request)
    {
        // 1. Create base model
        $category = Category::create([
            'name'        => $request->input('name.en'),
            'description' => $request->input('description.en'),
            'slug'        => $request->input('slug'),
        ]);

        // 2. Save polymorphic translations: ['name' => ['en' => '...', 'bn' => '...', 'es' => '...']]
        $category->saveTranslations($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Category created with polymorphic translations!');
    }
}
