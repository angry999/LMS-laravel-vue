<?php

namespace App\LMS\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use App\LMS\Client;

class LmsServiceProvider extends ServiceProvider
{
    /**
     * Register service
     * 
     * return void
     */ 
    public function register() 
    {
        App::bind('lms', function() {
            $config = config('services.lms');
            return new Client(
                $config['vendor_id'], 
                $config['app_id'], 
                $config['platform']
            );
        });
    }
}