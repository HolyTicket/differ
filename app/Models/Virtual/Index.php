<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 09-05-16
 * Time: 22:04
 */

namespace App\Models\Virtual;

class Index
{
    public $name;
    public $columns = [];
    public $unique;
    public $primary;

    public function __construct($name, $unique, $primary, $columns = []) {
        $this->name = $name;
        $this->unique = $unique;
        $this->primary = $primary;
        $this->columns = $columns;
    }

}