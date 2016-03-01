<?php
namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Table extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

}
