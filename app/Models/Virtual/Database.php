<?php
namespace App\Models\Virtual;

use App\Services\DiffService;
use App\Services\SqlGenerationService;

use App\Models\Deploy;
use App\Models\Change;

use \Tree\Node\Node;

use Auth;
use Diff;
use Connect;
use Sql;

class Database
{
    public $name;
    public $tables = [];

    public function parse(\App\Models\Connection $database, $connectionName) {

        $this->name = $database->database_name;

        $schema = \DB::getDoctrineSchemaManager();

        $schema->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $tables = $schema->listTables();

        foreach($tables as $table) {
            $table_name = $table->getName();

            $status = \DB::connection($connectionName)->
                            select(sprintf('SHOW TABLE STATUS WHERE Name = "%s"', $table_name))[0];
            $status->charset = explode('_', $status->Collation)[0];

            $this->tables[$table_name] = new Table($table_name, $status);

            foreach($table->getIndexes() as $index) {
                $this->tables[$table_name]->addIndex($index);
            }


            $columns = $table->getColumns();

            foreach($columns as $column) {
                // Name of column
                    $name = $column->getName();
                // Length
                    $length = $column->getLength();
                    if($length == null) {
                        $length = 11;
                    }
                $precision = $column->getPrecision();
                $scale = $column->getScale();
                $unsigned = $column->getUnsigned();
                $notnull = $column->getNotnull();
                $default = $column->getDefault();
                $autoincrement = $column->getAutoincrement();
                $comment = $column->getComment();

                $type = \DB::connection($connectionName)->select(sprintf('SHOW FIELDS FROM `%s` WHERE Field = "%s"', $table_name, $name));
                $type = $type[0]->Type;

                $this->tables[$table_name]->addColumn($name, $type, $notnull, $default, $autoincrement, $comment);

            }

//            $indices = $table->getIndexes();

//            foreach($indices as $index) {
//                if($index->isPrimary()) continue;
//                $cols = [];
//                foreach($index->getColumns() as $col) {
//                    $cols[] = $col;
//                }
//
//                if($index->isUnique()) {
//                    $this->tables[$table_name]->addUniqueKey($index->getName(), $cols);
//                } else {
//                    $this->tables[$table_name]->addIndex($index->getName(), $cols);
//                }
//            }
        }
    }

    public function diff(Database $destination_db) {
        $source_db = $this;

        $sql = '';

        Connect::reset();

        $differences = [];
        $differences['children'] = [];

        Deploy::truncate();
        Change::truncate();

        $deployment = new Deploy();

        $deployment->user_id = Auth::id();
        $deployment->save();

        $deployment_id = $deployment->id;

        Change::saving(function($change) use ($deployment_id) {
            $change->deploy_id = $deployment_id;
        });

        $parent_change = new Change();
        $parent_change->type = 'database_altered';
        $parent_change->save();

        // IF: table exists in source, but not in destination
        // THEN: generate create statement

        foreach($source_db->tables as $table_name => $table) {
            if(!isset($destination_db->tables[$table_name])) {
                $change = new Change();
                $change->type = 'table_added';
                $change->name = $table_name;
                $change->entity = 'table';
                $change->sql = Sql::createTable($table);
                $parent_change->children()->save($change);
            }
        }

        // IF: table exists in destination, but not in source.
        // THEN: generate drop statement

        foreach($destination_db->tables as $table_name => $table) {
            if(!isset($source_db->tables[$table_name])) {
                $change = new Change();
                $change->type = 'table_removed';
                $change->name = $table_name;
                $change->entity = 'table';
                $change->sql = Sql::dropTable($table);
                $parent_change->children()->save($change);
            }
        }

        // IF: table exists in both databases
        // THEN: diff the table using the diff() function of the table

            foreach($source_db->tables as $table_name => $table) {
                if(isset($destination_db->tables[$table_name])) {
                    $table->diff($destination_db->tables[$table_name], $parent_change);
                }
            }

        if(!$parent_change->children()->count()) {
            $parent_change->delete();
        }

        return Deploy::findOrFail($deployment_id);
    }

    public function __construct(\App\Models\Connection $database_one, $name)
    {
        Connect::connect($name, $database_one);
        $this->parse($database_one, $name);
    }
}
