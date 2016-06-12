<?php

namespace App\Providers;

use App\Helpers\String;
use Illuminate\Support\ServiceProvider;

/**
 * Class HelperServiceProvider: The Service Provider for the helpers
 * @package App\Providers
 */
class HelperServiceProvider extends ServiceProvider
{
    /**
     *
     */
    public function boot()
    {

    }

    /**
     *
     */
    public function register()
    {
        // Register the string helper
        $this->app->bind('stringHelper', function ($app) {
            $m = new String();
            return $m;
        });
    }

}