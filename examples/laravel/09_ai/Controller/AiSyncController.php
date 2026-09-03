<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Ataurbdx\Translator\Facades\Translator;
use App\Models\Post;

class AiSyncController extends Controller
{
    public function index()
    {
        return view('09_ai.Views.ai_sync_demo');
    }

    /**
     * 1. Translate a raw single sentence on-demand
     */
    public function translateText(Request $request)
    {
        $text = $request->input('text', 'Welcome to our platform');
        $from = $request->input('from', 'en');
        $to   = $request->input('to', 'bn');

        $translated = Translator::ai()->translate($text, $to, $from);

        return response()->json([
            'original'   => $text,
            'translated' => $translated,
            'provider'   => config('translator.ai.default_provider', 'gemini'),
        ]);
    }

    /**
     * 2. Auto-translate missing fields of an Eloquent Model via AI
     */
    public function translatePost(Request $request, int $postId)
    {
        $post = Post::findOrFail($postId);

        // Uses Gemini / OpenAI to translate title and content to Spanish or Bengali:
        Translator::ai()->translateModel($post, ['title', 'content'], 'bn');

        return redirect()->back()->with('success', 'Post automatically translated via AI!');
    }
}
