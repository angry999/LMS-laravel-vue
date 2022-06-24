<?php

namespace App\Http\Controllers;

use App\Unit;
use Illuminate\Http\Request;

class TranslationController extends ApiController
{
    public function select(Request $request)
    {
        $lang = $request->input('lang');
        $unitId = $request->input('unitId');
        $user = $request->user();
        $translations = null;
        
        $user->desired_language = $lang;
        $user->save();
        
        if ($unitId && $lang) {
            $unit = new Unit($unitId);
            $unit->loadTranslations($lang);
            $translations = $unit->translations;
        }
        
        return response()->json([
            'lang' => $lang,
            'translations' => $translations,
        ]);
    }
}