<?php

namespace Ataurbdx\TranslatorEngine\Examples\Laravel;

use Ataurbdx\TranslatorEngine\Facades\TranslatorEngine;
use Illuminate\Routing\Controller;

class LaravelExampleUsageController extends Controller
{
    public function demo()
    {
        // 1. Eloquent Model Translations (Types 1-4)
        // 2. Static UI Translation (Type 5) via translate()
        $addToCart = translate('button.add_to_cart');

        // 3. Cultural Local Formatting (Type 8) via translate(type: ...)
        $digits = translate('2026', type: 'digits', locale: 'bn');                     // ২০২৬
        $number = translate(1250000, type: 'number', decimals: 0, locale: 'bn');       // ১২,৫০,০০০
        $date   = translate(now(), type: 'date', withTime: true, locale: 'bn');        // localized date
        $words  = translate(1500, type: 'words', currency: 'BDT', locale: 'bn');       // এক হাজার পাঁচশত টাকা মাত্র
        $flag   = translate('bn', type: 'flag');                                       // flag markup

        return response()->json([
            'platform'      => 'Laravel',
            'category'      => $categoryTitle,
            'static_button' => $addToCart,
            'cultural'      => [
                'digits' => $digits,
                'number' => $number,
                'date'   => $date,
                'words'  => $words,
            ],
        ]);
    }
}
