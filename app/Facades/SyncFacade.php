<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
class SyncFacade extends Facade {

    protected static function getFacadeAccessor() {
        return 'sync';
    }
}