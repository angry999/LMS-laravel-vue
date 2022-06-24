<?php

namespace App\Http\Controllers;

use LMS;
use App\LMS\LmsException;
use Exception;
use Auth;

class CatalogueController extends ApiController
{
    public function index()
    {
        try {
            $user = Auth::user();
            $catalogue = LMS::catalogue($user->session_id);
            return response()->json([
                'catalogue' => $catalogue->getCategories(),
                'progress' => $user->getOverallProgress(),
            ]);
        } catch (LmsException $e) {
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'reason' => self::REASON_SERVER_ERROR,
                'message' => $e->getMessage(),
            ]);
        }
    }
}