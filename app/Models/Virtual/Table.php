<?php
namespace App\Models\Virtual;

use App\Difference;
use App\Models\Deploy;
use App\Services\SqlGenerationService;
use SqlGeneration;

use App\Models\Change;

class Table
{
    public $name;
    public $engine;
    public $row_format;
    public $collation;
    public $auto_increment;
    public $avg_row_length;

    public $differences = [];

    public $indices = [];
    private $columns = [];

    public function __construct($table_name, $status) {
        $this->name = $table_name;
        $this->engine = $status->Engine;
        $this->row_format = $status->Row_format;
        $this->collation = $status->Collation;
        $this->auto_increment = $status->Auto_increment;
        $this->avg_row_length = $status->Avg_row_length;
        $this->comment = $status->Comment;
    }

    public function addDifference($type, $name) {
        $difference = new \App\Models\Difference();
        $difference->type = $type;
        $difference->name = $name;
        $this->differences[] = $difference;
        return $difference;
    }

    public function addIndex(\Doctrine\DBAL\Schema\Index $index) {
        $name = $index->getName();

        $this->indices[$name] = new Index($name, $index->isUnique(), $index->isPrimary(), $index->getColumns());
    }

    public function addColumn($name, $type, $notnull, $default, $autoincrement, $comment) {
        $this->columns[$name] = new Column($name, $type, $notnull, $default, $autoincrement, $comment, $this);
    }

    public function getColumns() {
        return $this->columns;
    }

    public function getIndices() {
        return $this->indices;
    }

    public function diff(Table $destination_table, Change $database_change) {
        $source_table = &$this;

        $sqlGenerationService = new SqlGenerationService();

        $parent_change = new Change();
        $parent_change->type = 'table_altered';
        $parent_change->entity = 'table';
        $parent_change->name = $destination_table->name;
        $database_change->children()->save($parent_change);

        $option_changes = [];

        $options = ['collation', 'comment', 'row_format', 'engine'];

        foreach($options as $option_name) {
            if($destination_table->{$option_name} != $this->{$option_name}) {
                $change = new Change();
                $change->type = 'option_altered';
                $change->name = $option_name;
                $change->entity = 'option';
                $change->sql = $sqlGenerationService->alterOption($destination_table, $option_name, $this->{$option_name});
                $parent_change->children()->save($change);
            }
        }

        // IF: index exists in source, but not in destination
        // THEN: generate add index statement

        foreach($source_table->indices as $index_name => $index) {
            if(!isset($destination_table->indices[$index_name])) {
                $change = new Change();
                $change->type = 'index_added';
                $change->name = $index_name;
                $change->entity = 'index';
                $change->sql = $sqlGenerationService->addIndex($destination_table, $index);
                $parent_change->children()->save($change);
            }
        }

        // IF: column exists in source, but not in destination
        // THEN: generate add column statement

        foreach($source_table->columns as $column_name => $column) {
            if(!isset($destination_table->columns[$column_name])) {
                $change = new Change();
                $change->type = 'column_added';
                $change->name = $column_name;
                $change->entity = 'column';
                $change->sql = $sqlGenerationService->addColumn($destination_table, $column);
                $parent_change->children()->save($change);
            }
        }

        // IF: column exists in destination, but not in source
        // THEN: generate drop column statement

        foreach($destination_table->columns as $column_name => $column) {
            if(!isset($source_table->columns[$column_name])) {
                $change = new Change();
                $change->type = 'column_removed';
                $change->name = $column_name;
                $change->entity = 'column';
                $change->sql = $sqlGenerationService->dropColumn($destination_table, $column);
                $parent_change->children()->save($change);
            }
        }

        // IF: column exists in both source and destination
        // THEN: diff the table using the diff() function of the table

        foreach($source_table->columns as $column_name => $column) {
            if(isset($destination_table->columns[$column_name])) {
                $column->diff($destination_table->columns[$column_name], $parent_change);
            }
        }

        if(!$parent_change->children()->count()) {
            $parent_change->delete();
        }
    }
}