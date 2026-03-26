<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Switch the application language.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $supportedLocales = ['en', 'km'];

        if (!in_array($locale, $supportedLocales, true)) {
            abort(404);
        }

        session(['locale' => $locale]);

        return redirect()->back()->withCookie(cookie()->forever('locale', $locale));
    }
}
