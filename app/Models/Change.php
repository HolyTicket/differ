<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Change extends Eloquent
{
    public function children()
    {
        return $this->hasMany('App\Models\Change', 'parent_id', 'id');
    }
    public function parent()
    {
        return $this->belongsTo('App\Models\Change', 'parent_id');
    }
}
