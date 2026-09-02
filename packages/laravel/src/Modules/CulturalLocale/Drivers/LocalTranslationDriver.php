<?php

namespace Ataurbdx\TranslatorEngine\Modules\CulturalLocale\Drivers;

use Ataurbdx\TranslatorEngine\Modules\CulturalLocale\Models\TranslatorEngineLocale;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class LocalTranslationDriver
{
    /**
     * Get locale configuration with multi-tier fallback (Memory -> DB -> Package JSON)
     */
    public function getConfig(string $locale): array
    {
        $cacheKey = "translator_engine_locale_rule_{$locale}";
        $ttl = config('translator_engine.cache.ttl', 86400);

        return Cache::remember($cacheKey, $ttl, function () use ($locale) {
            // 1. Try Database
            try {
                $record = TranslatorEngineLocale::where('code', $locale)->where('is_active', true)->first();
                if ($record) {
                    return [
                        'code'            => $record->code,
                        'name'            => $record->name,
                        'native_name'     => $record->native_name,
                        'direction'       => $record->direction,
                        'decimal_sep'     => $record->decimal_sep,
                        'thousand_sep'    => $record->thousand_sep,
                        'group_style'     => $record->group_style,
                        'currency_code'   => $record->currency_code,
                        'currency_symbol' => $record->currency_symbol,
                        'currency_suffix' => $record->currency_word_suffix,
                        'digits'          => $record->digits ?? [],
                        'months'          => $record->months ?? [],
                        'days'            => $record->days ?? [],
                    ];
                }
            } catch (\Throwable $e) {
                // DB not migrated yet or unavailable, proceed to file fallback
            }

            // 2. Try Local Exported File
            $exportedFile = resource_path("lang/locales/{$locale}.json");
            if (File::exists($exportedFile)) {
                $data = json_decode(File::get($exportedFile), true);
                if (is_array($data)) return $data;
            }

            // 3. Fallback to default hardcoded config
            return $this->getDefaultFallbackConfig($locale);
        });
    }

    /**
     * Translate Western digits (0-9) to native localized digits
     */
    public function digits(mixed $value, ?string $locale = null): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $locale = $locale ?? app()->getLocale();
        $config = $this->getConfig($locale);

        $stringVal = (string) $value;

        if (!empty($config['digits']) && is_array($config['digits']) && count($config['digits']) === 10) {
            $western = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            return str_replace($western, $config['digits'], $stringVal);
        }

        return $stringVal;
    }

    /**
     * Format a number with localized decimal, grouping (South Asian or Standard), and localized digits
     */
    public function number(mixed $value, int $decimals = 0, ?string $locale = null): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $locale = $locale ?? app()->getLocale();
        $config = $this->getConfig($locale);

        $num = (float) $value;
        $formattedNum = number_format(abs($num), $decimals, '.', '');
        $parts = explode('.', $formattedNum);
        $intPart = $parts[0];
        $decPart = isset($parts[1]) ? $parts[1] : '';

        // Apply grouping style
        $groupStyle = $config['group_style'] ?? 'standard';
        if ($groupStyle === 'south_asian') {
            $groupedInt = $this->formatSouthAsian($intPart);
        } else {
            $groupedInt = number_format((float) $intPart, 0, '.', $config['thousand_sep'] ?? ',');
        }

        $final = $groupedInt;
        if ($decimals > 0 && $decPart !== '') {
            $final .= ($config['decimal_sep'] ?? '.') . $decPart;
        }

        if ($num < 0) {
            $final = '-' . $final;
        }

        // Convert digits to native
        return $this->digits($final, $locale);
    }

    /**
     * Format South Asian numbering system (Lakh / Crore)
     */
    protected function formatSouthAsian(string $number): string
    {
        if (strlen($number) <= 3) {
            return $number;
        }

        $lastThree = substr($number, -3);
        $remaining = substr($number, 0, -3);

        $chunks = [];
        while (strlen($remaining) > 0) {
            if (strlen($remaining) >= 2) {
                array_unshift($chunks, substr($remaining, -2));
                $remaining = substr($remaining, 0, -2);
            } else {
                array_unshift($chunks, $remaining);
                $remaining = '';
            }
        }

        return implode(',', $chunks) . ',' . $lastThree;
    }

    /**
     * Format date with localized month names and digits
     */
    public function date(mixed $date, bool $withTime = false, ?string $locale = null): string
    {
        if (!$date) {
            return '';
        }

        $locale = $locale ?? app()->getLocale();
        $config = $this->getConfig($locale);

        $timestamp = is_numeric($date) ? (int)$date : strtotime((string)$date);
        if (!$timestamp) return (string)$date;

        $day = date('d', $timestamp);
        $monthNum = (int) date('m', $timestamp);
        $year = date('Y', $timestamp);

        $monthName = $config['months'][$monthNum] ?? date('F', $timestamp);

        $formattedDate = $this->digits($day, $locale) . ' ' . $monthName . ' ' . $this->digits($year, $locale);

        if ($withTime) {
            $time = date('h:i A', $timestamp);
            $formattedDate .= ', ' . $this->digits($time, $locale);
        }

        return $formattedDate;
    }

    /**
     * Convert number to words (Cheques, Invoices)
     */
    public function words(int|float $amount, ?string $currency = null, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $config = $this->getConfig($locale);

        $cur = $currency ?? ($config['currency_suffix'] ?? 'টাকা মাত্র');

        if ($locale === 'bn') {
            return $this->numberToWordsBengali((int) $amount) . ' ' . $cur;
        }

        // Standard English words fallback
        return $this->numberToWordsEnglish((int) $amount) . ($cur ? " {$cur}" : '');
    }

    protected function numberToWordsBengali(int $number): string
    {
        $ones = [
            0 => 'শূন্য', 1 => 'এক', 2 => 'দুই', 3 => 'তিন', 4 => 'চার', 5 => 'পাঁচ',
            6 => 'ছয়', 7 => 'সাত', 8 => 'আট', 9 => 'নয়', 10 => 'দশ', 11 => 'এগারো',
            12 => 'বারো', 13 => 'তেরো', 14 => 'চৌদ্দ', 15 => 'পনেরো', 16 => 'ষোলো',
            17 => 'সতেরো', 18 => 'আঠারো', 19 => 'উনিশ', 20 => 'বিশ', 21 => 'একুশ',
            22 => 'বাইশ', 23 => 'তেইশ', 24 => 'চব্বিশ', 25 => 'পঁচিশ', 26 => 'ছাব্বিশ',
            27 => 'সাতাশ', 28 => 'আটাশ', 29 => 'উনত্রিশ', 30 => 'ত্রিশ', 31 => 'একত্রিশ',
            32 => 'বত্রিশ', 33 => 'তেত্রিশ', 34 => 'চৌত্রিশ', 35 => 'পঁয়ত্রিশ', 36 => 'ছত্রিশ',
            37 => 'সাঁইত্রিশ', 38 => 'আটত্রিশ', 39 => 'উনচল্লিশ', 40 => 'চল্লিশ',
            41 => 'একচল্লিশ', 42 => 'বিয়াল্লিশ', 43 => 'তেতাল্লিশ', 44 => 'চুয়াল্লিশ',
            45 => 'পঁয়তাল্লিশ', 46 => 'ছেচল্লিশ', 47 => 'সাতচল্লিশ', 48 => 'আটচল্লিশ',
            49 => 'উনপঞ্চাশ', 50 => 'পঞ্চাশ', 51 => 'একান্ন', 52 => 'বায়ান্ন',
            53 => 'তিপ্পান্ন', 54 => 'চুয়ান্ন', 55 => 'পঞ্চান্ন', 56 => 'ছাপ্পান্ন',
            57 => 'সাতান্ন', 58 => 'আটান্ন', 59 => 'উনষাট', 60 => 'ষাট',
            61 => 'একষট্টি', 62 => 'বাষট্টি', 63 => 'তেষট্টি', 64 => 'চৌষট্টি',
            65 => 'পঁয়ষট্টি', 66 => 'ছেষট্টি', 67 => 'সাতষট্টি', 68 => 'আটষট্টি',
            69 => 'উনসত্তর', 70 => 'সত্তর', 71 => 'একাত্তর', 72 => 'বাহাত্তর',
            73 => 'তিয়াত্তর', 74 => 'চুয়াত্তর', 75 => 'পঁচাত্তর', 76 => 'ছিয়াত্তর',
            77 => 'সাতাত্তর', 78 => 'আটাত্তর', 79 => 'উনাশি', 80 => 'আশি',
            81 => 'একাশি', 82 => 'বিরাশি', 83 => 'তিরাশি', 84 => 'চুরাশি',
            85 => 'পঁচাশি', 86 => 'ছিয়াশি', 87 => 'সাতাশি', 88 => 'অষ্টাদশ',
            89 => 'ঊননব্বই', 90 => 'নব্বই', 91 => 'একানব্বই', 92 => 'বানব্বই',
            93 => 'তিরানব্বই', 94 => 'চুরানব্বই', 95 => 'পঁচানব্বই', 96 => 'ছিয়ানব্বই',
            97 => 'সাতানব্বই', 98 => 'আটানব্বই', 99 => 'নিরানব্বই'
        ];

        if ($number < 100) return $ones[$number] ?? (string)$number;

        $words = [];
        if ($number >= 10000000) {
            $crore = (int)($number / 10000000);
            $words[] = $this->numberToWordsBengali($crore) . ' কোটি';
            $number %= 10000000;
        }
        if ($number >= 100000) {
            $lakh = (int)($number / 100000);
            $words[] = $this->numberToWordsBengali($lakh) . ' লাখ';
            $number %= 100000;
        }
        if ($number >= 1000) {
            $thousand = (int)($number / 1000);
            $words[] = $this->numberToWordsBengali($thousand) . ' হাজার';
            $number %= 1000;
        }
        if ($number >= 100) {
            $hundred = (int)($number / 100);
            $words[] = $this->numberToWordsBengali($hundred) . ' শত';
            $number %= 100;
        }
        if ($number > 0) {
            $words[] = $ones[$number] ?? (string)$number;
        }

        return implode(' ', $words);
    }

    protected function numberToWordsEnglish(int $number): string
    {
        if ($number === 0) return 'Zero';
        $words = [];
        $units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        if ($number >= 1000000) {
            $words[] = $this->numberToWordsEnglish((int)($number / 1000000)) . ' Million';
            $number %= 1000000;
        }
        if ($number >= 1000) {
            $words[] = $this->numberToWordsEnglish((int)($number / 1000)) . ' Thousand';
            $number %= 1000;
        }
        if ($number >= 100) {
            $words[] = $this->numberToWordsEnglish((int)($number / 100)) . ' Hundred';
            $number %= 100;
        }
        if ($number > 0) {
            if ($number < 20) {
                $words[] = $units[$number];
            } else {
                $word = $tens[(int)($number / 10)];
                if ($number % 10 > 0) $word .= '-' . $units[$number % 10];
                $words[] = $word;
            }
        }
        return implode(' ', $words);
    }

    protected function getDefaultFallbackConfig(string $locale): array
    {
        if ($locale === 'bn') {
            return [
                'code' => 'bn',
                'name' => 'Bengali',
                'native_name' => 'বাংলা',
                'direction' => 'ltr',
                'decimal_sep' => '.',
                'thousand_sep' => ',',
                'group_style' => 'south_asian',
                'currency_code' => 'BDT',
                'currency_symbol' => '৳',
                'currency_suffix' => 'টাকা মাত্র',
                'digits' => ['০','১','২','৩','৪','৫','৬','৭','৮','৯'],
                'months' => [
                    1 => 'জানুয়ারি', 2 => 'ফেব্রুয়ারি', 3 => 'মার্চ', 4 => 'এপ্রিল',
                    5 => 'মে', 6 => 'জুন', 7 => 'জুলাই', 8 => 'আগস্ট',
                    9 => 'সেপ্টেম্বর', 10 => 'অক্টোবর', 11 => 'নভেম্বর', 12 => 'ডিসেম্বর'
                ],
                'days' => ['sat'=>'শনিবার', 'sun'=>'রবিবার', 'mon'=>'সোমবার', 'tue'=>'মঙ্গলবার', 'wed'=>'বুধবার', 'thu'=>'বৃহস্পতিবার', 'fri'=>'শুক্রবার'],
            ];
        }

        return [
            'code' => 'en',
            'name' => 'English',
            'native_name' => 'English',
            'direction' => 'ltr',
            'decimal_sep' => '.',
            'thousand_sep' => ',',
            'group_style' => 'standard',
            'currency_code' => 'USD',
            'currency_symbol' => '$',
            'currency_suffix' => 'USD Only',
            'digits' => ['0','1','2','3','4','5','6','7','8','9'],
            'months' => [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
            ],
            'days' => ['sat'=>'Saturday', 'sun'=>'Sunday', 'mon'=>'Monday', 'tue'=>'Tuesday', 'wed'=>'Wednesday', 'thu'=>'Thursday', 'fri'=>'Friday'],
        ];
    }
}
