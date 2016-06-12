<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 09-05-16
 * Time: 22:04
 */

namespace App\Models\Virtual;

use App\Models\Change;
use Sql;

/**
 * Index representation
 * @package App\Models\Virtual
 */
class Index
{
    /**
     * @var $name name of the index
     */
    private $name;
    /**
     * @var array $columns the columns of this index
     */
    private $columns = [];
    /**
     * @var bool is this a unique index?
     */
    private $unique;
    /**
     * @var bool is this a primary index?
     */
    private $primary;

    /**
     * Get the name of this index
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name of this index
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get the columns of this index
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Set the columns of this index
     * @param array $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * Returns true if the index is unique
     * @return mixed
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * Set this index as a unique index
     * @param mixed $unique
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;
    }

    /**
     * Returns true if the index is a primary key
     * @return mixed
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * Sets primary key yes/no
     * @param mixed $primary
     */
    public function setPrimary($primary)
    {
        $this->primary = $primary;
    }

    /**
     * Create the index and set the attributes
     * @param $name
     * @param $unique
     * @param $primary
     * @param array $columns
     */
    public function __construct($name, $unique, $primary, $columns = []) {
        $this->name = $name;
        $this->unique = $unique;
        $this->primary = $primary;
        $this->columns = $columns;
    }

    /**
     * Diff the index
     * @param Index $destination_index
     * @param Change $table_change
     * @throws \Exception
     */
    public function diff(Index $destination_index, Change $table_change)
    {
        // Create a source alias
        $source_index = &$this;

        // Create a parent change (because the column is altered)
        $parent_change = $table_change->addChange($destination_index->name, 'index_altered', 'index', '');

        // Get all attributes/properties of this object
        $source_attributes = get_object_vars($this);

        // The name is at this point always the same, so no need to check it
        unset($source_attributes['name']);

        // Loop through the source attributes
        foreach($source_attributes as $attribute_name => $attribute_value) {
            // If the attributes are not the same
            if($attribute_value != $destination_index->{$attribute_name}) {
                // Create the SQL and add the change
                $parent_change->addChange($attribute_name, 'attribute_altered', 'attribute', Sql::alterIndex($this, $destination_index, $table_change->name));
            }
        }

        // If there are not changes, remove the parent change
        if(!$parent_change->children()->count()) {
            $parent_change->delete();
        }
    }

}