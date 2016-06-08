<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Deploy extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    public function changes() {
        return $this->hasMany('App\Models\Change');
    }

}
