# Type 9: TranslationAI (`ai`)

Automated AI translation engine powered by Google Gemini, OpenAI, Claude, or DeepL. Can translate text on-the-fly, translate batches, or auto-fill missing fields in Eloquent models.

---

## ⚡ Key Capabilities
- **Model Auto-Translate**: Automatically detects missing language fields in any model and fills them using AI.
- **Static Keys Auto-Sync**: Finds all untranslated keys in `translator_statics` and syncs them in background jobs.
- **Provider Switching**: Easily switch between `gemini`, `openai`, or `deepl` via `.env`.

---

## 🛠️ Configuration & Setup

In your `.env`:
```dotenv
TRANSLATOR_AI_PROVIDER=gemini
GEMINI_API_KEY=your-gemini-api-key
GEMINI_TRANSLATION_MODEL=gemini-1.5-flash
```

---

## 🚀 Usage

### 1. On-Demand Text Translation in PHP
```php
use Ataurbdx\Translator\Facades\Translator;

$bangla = Translator::ai()->translate('Welcome to our application', 'bn', 'en');
$spanish = Translator::ai()->translate('Welcome to our application', 'es', 'en');
```

### 2. Auto-Translate an Eloquent Model
```php
$post = Post::find(1);

// Auto-translates missing title & content into Bengali using Gemini:
Translator::ai()->translateModel($post, ['title', 'content'], 'bn');
```

### 3. Bulk CLI Sync Command
```bash
# Auto-translates all missing static UI keys from English to Bengali:
php artisan translator:ai-sync --from=en --to=bn

# Auto-translates specific group:
php artisan translator:ai-sync --from=en --to=bn --group=button
```
