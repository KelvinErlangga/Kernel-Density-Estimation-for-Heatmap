<?php

namespace App\Helpers;

use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateHelper
{
    public static function t($text)
    {
        $locale = app()->getLocale(); // bahasa aktif
        if ($locale === config('app.locale')) {
            return $text; // kalau sama dengan default, tidak perlu translate
        }

        try {
            $tr = new GoogleTranslate($locale);
            return $tr->translate($text);
        } catch (\Exception $e) {
            return $text; // fallback kalau gagal
        }
    }
}
