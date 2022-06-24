<?php

namespace App\Cdn\Facades;

use Illuminate\Support\Facades\Facade;

class Cdn extends Facade
{
    protected static function getFacadeAccessor() 
    {
        return 'sp_cdn';
    }
}