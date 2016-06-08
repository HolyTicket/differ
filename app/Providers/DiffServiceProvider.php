<?php

namespace App\Providers;

use App\Services\SqlGenerationService;
use App\Services\SyncService;
use Illuminate\Support\ServiceProvider;

class DiffServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $this->app->bind('sql', function ($app) {
            $m = new SqlGenerationService();
            return $m;
        });
        $this->app->bind('sync', function ($app) {
            $m = new SyncService();
            return $m;
        });
    }

}