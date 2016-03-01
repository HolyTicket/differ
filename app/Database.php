<?php
namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Database extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'host', 'username', 'password', 'database_name'
    ];

}
