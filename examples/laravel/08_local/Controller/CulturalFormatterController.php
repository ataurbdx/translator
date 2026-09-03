<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Ataurbdx\Translator\Facades\Translator;

class CulturalFormatterController extends Controller
{
    public function index()
    {
        // 1. Localized Digits
        $yearDigits = translate('2026', type: 'digits', locale: 'bn'); // ২০২৬

        // 2. Localized Numbers (South Asian Lakh / Crore grouping)
        $amountBn = translate(1250000, type: 'number', locale: 'bn'); // ১২,৫০,০০০
        $amountEn = translate(1250000, type: 'number', locale: 'en'); // 1,250,000

        // 3. Localized Dates
        $dateBn = translate(now(), type: 'date', withTime: true, locale: 'bn'); //localized date in Bengali

        // 4. Financial Cheque / Invoice Number-To-Words
        $wordsBdt = translate(1500, type: 'words', currency: 'BDT', locale: 'bn'); // এক হাজার পাঁচশত টাকা মাত্র
        $wordsUsd = translate(1500, type: 'words', currency: 'USD', locale: 'en'); // One Thousand Five Hundred USD Only

        return view('08_local.Views.cultural_demo', compact(
            'yearDigits', 'amountBn', 'amountEn', 'dateBn', 'wordsBdt', 'wordsUsd'
        ));
    }
}
