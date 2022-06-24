<?php

namespace App\Cdn\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use App\Cdn\Client;

class CdnServiceProvider extends ServiceProvider
{
    /**
     * Register service
     * 
     * return void
     */ 
    public function register() 
    {
        App::bind('sp_cdn', function() {
            return new Client();
        });
    }
}