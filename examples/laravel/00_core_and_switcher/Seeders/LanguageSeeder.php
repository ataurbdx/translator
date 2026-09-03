<?php

namespace App\Seeders;

use Illuminate\Database\Seeder;
use Ataurbdx\Translator\Modules\Languages\Models\TranslatorLanguage;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * The system is completely dynamic — you can add ANY language worldwide!
     */
    public function run(): void
    {
        $languages = [
            [
                'code'            => 'en',
                'name'            => 'English',
                'native'          => 'English',
                'direction'       => 'ltr',
                'currency'        => 'USD',
                'currency_symbol' => '$',
                'flag'            => '🇺🇸',
                'is_default'      => true,
                'status'          => true,
                'sort_order'      => 1,
            ],
            [
                'code'            => 'bn',
                'name'            => 'Bengali',
                'native'          => 'বাংলা',
                'direction'       => 'ltr',
                'currency'        => 'BDT',
                'currency_symbol' => '৳',
                'flag'            => '🇧🇩',
                'is_default'      => false,
                'status'          => true,
                'sort_order'      => 2,
            ],
            [
                'code'            => 'ar',
                'name'            => 'Arabic',
                'native'          => 'العربية',
                'direction'       => 'rtl',
                'currency'        => 'AED',
                'currency_symbol' => 'د.إ',
                'flag'            => '🇸🇦',
                'is_default'      => false,
                'status'          => true,
                'sort_order'      => 3,
            ],
            [
                'code'            => 'es',
                'name'            => 'Spanish',
                'native'          => 'Español',
                'direction'       => 'ltr',
                'currency'        => 'EUR',
                'currency_symbol' => '€',
                'flag'            => '🇪🇸',
                'is_default'      => false,
                'status'          => true,
                'sort_order'      => 4,
            ],
            [
                'code'            => 'fr',
                'name'            => 'French',
                'native'          => 'Français',
                'direction'       => 'ltr',
                'currency'        => 'EUR',
                'currency_symbol' => '€',
                'flag'            => '🇫🇷',
                'is_default'      => false,
                'status'          => true,
                'sort_order'      => 5,
            ],
            [
                'code'            => 'hi',
                'name'            => 'Hindi',
                'native'          => 'हिन्दी',
                'direction'       => 'ltr',
                'currency'        => 'INR',
                'currency_symbol' => '₹',
                'flag'            => '🇮🇳',
                'is_default'      => false,
                'status'          => true,
                'sort_order'      => 6,
            ],
            [
                'code'            => 'de',
                'name'            => 'German',
                'native'          => 'Deutsch',
                'direction'       => 'ltr',
                'currency'        => 'EUR',
                'currency_symbol' => '€',
                'flag'            => '🇩🇪',
                'is_default'      => false,
                'status'          => true,
                'sort_order'      => 7,
            ],
        ];

        foreach ($languages as $lang) {
            TranslatorLanguage::updateOrCreate(['code' => $lang['code']], $lang);
        }
    }
}
