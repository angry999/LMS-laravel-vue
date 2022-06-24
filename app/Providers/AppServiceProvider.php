<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('keys', function ($attribute, $value, $parameters, $validator) {
            return is_array($value) && array_every($value, function($item) use($parameters) {
                return is_array($item) && test_array_keys($item, $parameters);
            });
        });
        Validator::extend('strictkeys', function ($attribute, $value, $parameters, $validator) {
            return is_array($value) && array_every($value, function($item) use($parameters) {
                return is_array($item) && test_array_keys($item, $parameters, true);
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
