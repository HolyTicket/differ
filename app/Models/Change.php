<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Change: A database change
 * @package App\Models
 */
class Change extends Eloquent
{
    /**
     * Get the underlying children of this change.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('App\Models\Change', 'parent_id', 'id');
    }

    /**
     * Get the parent of this change
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('App\Models\Change', 'parent_id');
    }

    /**
     * Add a change to it's children
     * @param $name
     * @param $type
     * @param $entity
     * @param string $sql
     * @param null $dependency
     * @return Change
     */
    public function addChange($name, $type, $entity, $sql = '', $dependency = null) {
        $change = new Change();
        $change->type = $type;
        $change->name = $name;
        $change->entity = $entity;
        $change->sql = $sql;
        if($dependency != null) {
            $dependency = json_encode($dependency);
        }
        $change->dependency = $dependency;
        $this->children()->save($change);
        return $change;
    }
}