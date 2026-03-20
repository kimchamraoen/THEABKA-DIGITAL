<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    public function switch(string $locale): RedirectResponse
    {
        if (! in_array($locale, Language::getActiveLocales(), true)) {
            abort(400);
        }

        session(['locale' => $locale]);

        return redirect()->back();
    }
}
