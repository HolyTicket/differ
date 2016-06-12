<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Auth;
use Crypt;

/**
 * Question: a FAQ question
 * @package App\Models
 */
class Question extends Eloquent
{
    /**
     * @var array $fillable Contains the database attributes that are mass assignable
     */
    protected $fillable = [
        'question', 'answer', 'sort'
    ];

    /**
     * Get the category belonging to this question
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\QuestionCategory', 'category_id');
    }
}
