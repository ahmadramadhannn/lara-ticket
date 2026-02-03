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
        // Validate locale
        if (!in_array($locale, ['en', 'id'])) {
            $locale = config('app.locale');
        }
        
        $request->session()->put('locale', $locale);
        
        return redirect()->back();
    }
}
