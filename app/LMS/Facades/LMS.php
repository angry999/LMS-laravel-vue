<?php

namespace App\LMS\Facades;

use Illuminate\Support\Facades\Facade;

class LMS extends Facade
{
    protected static function getFacadeAccessor() 
    {
        return 'lms';
    }
}