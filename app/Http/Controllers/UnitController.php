<?php

namespace App\Http\Controllers;

use App\Unit;
use Illuminate\Http\Request;

class UnitController extends ApiController
{
    public function get(Request $request, Unit $unit)
    {
        $user = $request->user();
        $unit->loadContent();
        $unit->loadProgress($user);
        
        if ($user->desired_language) {
            $unit->loadTranslations($user->desired_language);
        }
        
        return response()->json([
            'unit' => $unit
        ]);
    }
    
    public function getPronounciation(Request $request, Unit $unit)
    {
        $user = $request->user();
        $unit->loadStrings();
        $unit->loadListening();

        return response()->json([
            'unit' => $unit
        ]);
    }

    public function getGuidedSpeech(Request $request, Unit $unit)
    {
        $user = $request->user();
        $unit->loadContentForGuidedSpeech();
        $unit->loadProgress($user);
        
        return response()->json([
            'unit' => $unit
        ]);
    }
}