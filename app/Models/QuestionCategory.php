<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Auth;
use Crypt;

class QuestionCategory extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'question', 'answer', 'sort'
    ];

    public function questions() {
        return $this->hasMany('App\Models\Question', 'category_id');
    }

}
