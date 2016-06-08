<?php

namespace App\Providers;

use App\Helpers\String;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('stringHelper', function ($app) {
            $m = new String();
            return $m;
        });
    }

}