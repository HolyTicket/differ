<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use Auth;

/**
 * Class UserScope
 * A scope that makes sure you only see your own connection records (by logged in user id)
 * @package App\Scopes
 */
class UserScope implements Scope {
    /**
     * Apply the user_id scope
     * @param Builder $builder
     * @param Model $model
     */
    public function apply(Builder $builder, Model $model)
    {
        // Add a where connection to the query builder: user_id = logged in user_id
        $builder->where('user_id', '=', Auth::id());
    }
}