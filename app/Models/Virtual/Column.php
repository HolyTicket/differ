<?php
namespace App\Models\Virtual;

use App\Services\SqlGenerationService;

use App\Models\Change;

class Column
{
    public $name, $type, $notnull, $default, $auto_increment, $comment;
    public $table;
    public function __construct($column_name, $data_type, $notnull, $default, $auto_increment, $comment, $table)
    {
        $this->name = $column_name;
        $this->type = $data_type;
        $this->notnull = $notnull;
        $this->default = $default;
        $this->auto_increment = $auto_increment;
        $this->comment = $comment;

        $this->table = $table;
    }

    public function getTable() {
        return $this->table;
    }

    public function isPrimaryKey() {
        foreach($this->getTable()->getIndices() as $index) {
            if($index->primary && in_array($this->name, $index->columns))
                return true;
        }
        return false;
    }

    public function diff(Column $destination_column, Change $table_change)
    {
        $source_column = &$this;
        $differences = [];

        $parent_change = new Change();
        $parent_change->type = 'column_altered';
        $parent_change->entity = 'column';
        $parent_change->name = $destination_column->name;
        $table_change->children()->save($parent_change);

        // Get all attributes/properties of this object
            $source_attributes = get_object_vars($this);
            $destination_attributes = get_object_vars($destination_column);

        // The name is at this point always the same, so no need to check it
            unset($source_attributes['name']);

        $sqlGenerationService = new SqlGenerationService();

        $changes = [];

        foreach($source_attributes as $attribute_name => $attribute_value) {
            if($attribute_name != 'table' && $attribute_value != $destination_column->{$attribute_name}) {
                $type_of_change = $attribute_name;
                $new_value = $attribute_value;
                $old_value = $destination_column->{$attribute_name};

                $change = new Change();
                $change->type = 'attribute_altered';
                $change->name = $attribute_name;
                $change->entity = 'attribute';
                $change->sql = $sqlGenerationService->alterColumn($destination_column, $type_of_change, $new_value, $old_value, $destination_attributes, $table_change->name);
                $parent_change->children()->save($change);
            }
        }

        if(!$parent_change->children()->count()) {
            $parent_change->delete();
        }

        return $differences;
    }
}