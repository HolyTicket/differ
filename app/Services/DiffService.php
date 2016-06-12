<?php

/**
 * THIS IS A OLD CLASS AND NOT USED ANYMORE (v1)
 */

namespace App\Services;

use App\Services\BaseService;
use App\Models\Deploy;
use App\Models\Change;

use Auth;

/**
 * Class DiffServiceOld
 * @package App\Services
 */
class DiffServiceOld extends BaseService
{
    /**
     * @param $name
     * @param $db
     */
    public function connect($name, $db) {
        \Config::set(sprintf('database.connections.%s.host', $name), $db->host);
        \Config::set(sprintf('database.connections.%s.username', $name), $db->username);
        \Config::set(sprintf('database.connections.%s.password', $name), $db->password);
        \Config::set(sprintf('database.connections.%s.database', $name), $db->database_name);

        \Config::set('database.default', $name);

        \DB::reconnect($name);
    }

    /**
     * @param $name
     */
    public function purge($name) {
        \DB::purge($name);
    }

    /**
     * @param $table_name
     * @param $column_name
     * @param $action
     * @return string
     */
    public function generateId($table_name, $column_name, $action) {
        return json_encode(compact('table_name', 'column_name', 'action'));
    }

    /**
     * @param $database_one
     * @param $database_two
     * @return array
     */
    public function diff($database_one, $database_two) {
        $mapping_one = [];
        $mapping_two = [];

        // Create a deployment in the database
            $deployment = new Deploy();

            $deployment->user_id = Auth::id();
            $deployment->save();

            $deployment_id = $deployment->id;



        $this->connect('db_one', $database_one);

        $schema = \DB::getDoctrineSchemaManager();


        // Doctrine doesnt support ENUM's, so parse them as strings
        // http://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/cookbook/mysql-enums.html

        $schema->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $tables = $schema->listTables();

        foreach ($tables as $table) {
            $table_name = $table->getName();
            $mapping_one[$table_name] = [];

            foreach ($table->getColumns() as $column) {
                dd($column);
//                dd($column->getType()->getTypesMap());
                $mapping_one[$table_name]['columns'][$column->getName()] = [
                    'type' => $column->getType()->getName(),
                    'length' => $column->getLength(),
                    'precision' => $column->getPrecision(),
                    'auto_increment' => $column->getAutoincrement()
                ];
            }
        }

        $this->purge('db_one');

        // Then do DATABASE TWO

        $this->connect('db_two', $database_two);

        $schema = \DB::getDoctrineSchemaManager();

        $schema->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

        $tables = $schema->listTables();

        foreach ($tables as $table) {
            $table_name = $table->getName();
            $mapping_two[$table_name] = [];

            foreach ($table->getColumns() as $column) {
                $mapping_two[$table_name]['columns'][$column->getName()] = [
                    'type' => $column->getType()->getName(),
                    'length' => $column->getLength(),
                    'precision' => $column->getPrecision(),
                    'auto_increment' => $column->getAutoincrement()
                ];
            }
        }

        $differences = [];

        foreach($mapping_two as $table_name => $info) {
            if(!isset($mapping_one[$table_name])) {
                // Table is removed

                $id = $this->generateId($table_name, false, 'table_removed');
                $differences[$table_name][] = [
                    'type' => 'table_removed',
                    'id' => $id
                ];
            } else {
                foreach($info['columns'] as $column_name => $column_info) {
                    if(!isset($mapping_one[$table_name]['columns'][$column_name])) {
                        $id = $this->generateId($table_name, $column_name, 'column_removed');
                        $differences[$table_name][$column_name] = [
                            'type' => 'column_removed',
                            'id' => $id
                        ];
                    }
                }
            }
        }

        foreach($mapping_one as $table_name => $info) {
            if(isset($mapping_two[$table_name])) {
                // tabel bestaat in dest.
                foreach($info['columns'] as $column_name => $column_info) {
                    if(isset($mapping_two[$table_name]['columns'][$column_name])) {
                        // column naam bestaat in dest
                        if(serialize($mapping_one[$table_name]['columns'][$column_name]) != serialize($mapping_two[$table_name]['columns'][$column_name])) {
                            foreach($mapping_two[$table_name]['columns'][$column_name] as $attribute_name => $value_name) {
                                if($value_name != $mapping_one[$table_name]['columns'][$column_name][$attribute_name]) {
                                    $differences[$table_name][$column_name]['type'] = 'altered_column';
                                    $differences[$table_name][$column_name]['id'] = $this->generateId($table_name, $column_name, 'altered_column');
                                    $differences[$table_name][$column_name]['changes'][] = [
                                        'type' => $attribute_name,
                                        'new' => $mapping_two[$table_name]['columns'][$column_name][$attribute_name],
                                        'old' => $mapping_one[$table_name]['columns'][$column_name][$attribute_name],
                                        'column_name' => $column_name
                                    ];
                                }
                            }
                        }
                    } else {

                        // gehele column bestaat niet in dest. column helemaal aanmaken
                        $differences[$table_name][$column_name] = [
                            'type' => 'missing_column',
                            'column_name' => $column_name,
                            'id' => $this->generateId($table_name, $column_name, 'missing_column')
                        ];
                    }
                }
            } else {
                // gehele tabel bestaat niet in dest. tabel helemaal aanmaken.
                $differences[$table_name][] = [
                    'type' => 'missing_table',
                    'id' => $this->generateId($table_name, false, 'missing_table')
                ];
            }
        }

        \Config::set('database.default', 'mysql');

        \DB::reconnect('mysql');



        foreach($differences as $table_name => $changes) {
            foreach($changes as $field_name => $change) {

                $ch = new Change();
                $ch->type = $change['type'];
                $ch->info = $change['id'];
                //$ch->changes = json_encode($change['changes']);
                $deployment->changes()->save($ch);

                $differences[$table_name][$field_name]['change_id'] = $ch->id;

                if($ch->type == 'altered_column') {
                    foreach($change['changes'] as $ni => $column_change) {
                        $chc = new Change();
                        $chc->type = $column_change['type'];
                        $chc->info = json_encode($column_change);
                        $chc->deploy_id = $deployment->id;
                        $ch->children()->save($chc);
                        $differences[$table_name][$field_name]['changes'][$ni]['change_id'] = $chc->id;
                    }
                }

            }
        }

        return compact('differences', 'deployment_id', 'mapping_one', 'mapping_two');
    }
}