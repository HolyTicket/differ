<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class SqlFacade
 * The SQL facade for creating SQL statements
 * @package App\Facades
 */
class SqlFacade extends Facade {

    /**
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'sql';
    }
}