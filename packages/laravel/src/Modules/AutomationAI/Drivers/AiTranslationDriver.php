<?php

namespace Ataurbdx\TranslatorEngine\Modules\AutomationAI\Drivers;

use Ataurbdx\TranslatorEngine\Core\Contracts\AiTranslatorInterface;
use Ataurbdx\TranslatorEngine\Modules\Settings\Models\TranslatorEngineSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class AiTranslationDriver implements AiTranslatorInterface
{
    protected string $provider;

    public function __construct(?string $provider = null)
    {
        $this->provider = $provider 
            ?? TranslatorEngineSetting::get('ai_provider') 
            ?? config('translator_engine.ai.default_provider', 'gemini');
    }

    public function translate(string $text, string $to, ?string $from = null, array $context = []): string
    {
        if (trim($text) === '') {
            return '';
        }

        $from = $from ?? config('translator_engine.default_locale', 'en');

        return match ($this->provider) {
            'openai' => $this->translateWithOpenAi($text, $to, $from, $context),
            'deepl'  => $this->translateWithDeepL($text, $to, $from),
            default  => $this->translateWithGemini($text, $to, $from, $context),
        };
    }

    public function translateBatch(array $items, string $to, ?string $from = null, array $context = []): array
    {
        $results = [];
        foreach ($items as $k => $text) {
            $results[$k] = $this->translate($text, $to, $from, $context);
        }
        return $results;
    }

    /**
     * Auto-translate translatable fields of an Eloquent Model into a target locale
     */
    public function translateModel(Model $model, array $fields, string $to, ?string $from = null): bool
    {
        $from = $from ?? config('translator_engine.default_locale', 'en');

        foreach ($fields as $field) {
            $sourceText = $model->translate($field, $from) ?? $model->getRawOriginal($field);
            if (!empty($sourceText) && is_string($sourceText)) {
                $translatedText = $this->translate($sourceText, $to, $from);
                $model->setTranslation($field, $to, $translatedText);
            }
        }

        return true;
    }

    protected function translateWithGemini(string $text, string $to, string $from, array $context): string
    {
        $apiKey = TranslatorEngineSetting::get('gemini_api_key') 
            ?? config('translator_engine.ai.providers.gemini.api_key');

        if (empty($apiKey)) {
            return $text; // Return original if key missing
        }

        $model = config('translator_engine.ai.providers.gemini.model', 'gemini-1.5-flash');
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $prompt = "Translate the following text accurately from {$from} to {$to}. Return ONLY the translated string with no explanations or quotes:\n\n{$text}";

        try {
            $response = Http::timeout(15)->post($endpoint, [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return trim($data['candidates'][0]['content']['parts'][0]['text'] ?? $text);
            }
        } catch (\Throwable $e) {
            // Log or fallback
        }

        return $text;
    }

    protected function translateWithOpenAi(string $text, string $to, string $from, array $context): string
    {
        $apiKey = TranslatorEngineSetting::get('openai_api_key') 
            ?? config('translator_engine.ai.providers.openai.api_key');

        if (empty($apiKey)) {
            return $text;
        }

        $model = config('translator_engine.ai.providers.openai.model', 'gpt-4o-mini');

        try {
            $response = Http::withToken($apiKey)->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => "You are a professional translator. Translate from {$from} to {$to}. Respond only with the translated string."],
                    ['role' => 'user', 'content' => $text]
                ],
                'temperature' => 0.2,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return trim($data['choices'][0]['message']['content'] ?? $text);
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return $text;
    }

    protected function translateWithDeepL(string $text, string $to, string $from): string
    {
        $apiKey = TranslatorEngineSetting::get('deepl_api_key') 
            ?? config('translator_engine.ai.providers.deepl.api_key');

        if (empty($apiKey)) return $text;

        try {
            $response = Http::withHeaders(['Authorization' => "DeepL-Auth-Key {$apiKey}"])
                ->post('https://api-free.deepl.com/v2/translate', [
                    'text' => [$text],
                    'target_lang' => strtoupper($to),
                    'source_lang' => strtoupper($from),
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['translations'][0]['text'] ?? $text;
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return $text;
    }
}
