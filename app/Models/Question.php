<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Auth;
use Crypt;

class Question extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question', 'answer', 'sort'
    ];

    public function category()
    {
        return $this->belongsTo('App\Models\QuestionCategory', 'category_id');
    }

}
