<?php

namespace App\Service;
use Illuminate\Support\Facades\{Cache, DB};
use App\Models\{Category, Product};

function transWord($word, $lang = null)
{
    if (! $lang) {
        $lang = app()->getLocale();
    }

    $translationsFile = 'translations.json';

    if (! file_exists($translationsFile)) {
        file_put_contents($translationsFile, json_encode([], JSON_PRETTY_PRINT));
    }

    $translations = json_decode(file_get_contents($translationsFile), true);

    if (isset($translations[$lang][$word])) {
        $translatedWord = $translations[$lang][$word];
    } else {
        $translateClient = new \Stichoza\GoogleTranslate\GoogleTranslate;
        $translateClient->setOptions(['verify' => 'C:\xampp\php\extras\ssl\cacert.pem']);

        $translatedWord = $translateClient
            ->setSource(null)
            ->setTarget($lang)
            ->translate($word);

        $translations[$lang][$word] = $translatedWord;
        file_put_contents($translationsFile, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    return $translatedWord;
}