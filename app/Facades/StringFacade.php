<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
class StringFacade extends Facade {

    protected static function getFacadeAccessor() {
        return 'stringHelper';
    }
}