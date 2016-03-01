<?php
namespace App\Services;

use App\Services\BaseService;

class DiffService extends BaseService
{
    public function connect($name, $db) {
        \Config::set(sprintf('database.connections.%s.host', $name), $db->host);
        \Config::set(sprintf('database.connections.%s.username', $name), $db->username);
        \Config::set(sprintf('database.connections.%s.password', $name), $db->password);
        \Config::set(sprintf('database.connections.%s.database', $name), $db->database_name);

        \Config::set('database.default', $name);

        \DB::reconnect($name);
    }

    public function purge($name) {
        \DB::purge($name);
    }

    public function diff($database_one, $database_two) {
        $mapping_one = [];
        $mapping_two = [];

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
                $differences[$table_name][] = [
                    'type' => 'table_removed',
                ];
            } else {
                foreach($info['columns'] as $column_name => $column_info) {
                    if(!isset($mapping_one[$table_name]['columns'][$column_name])) {
                        $differences[$table_name][$column_name] = [
                            'type' => 'column_removed',
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
                            'column_name' => $column_name
                        ];
                    }
                }
            } else {
                // gehele tabel bestaat niet in dest. tabel helemaal aanmaken.
                $differences[$table_name][] = [
                    'type' => 'missing_table',
                ];
            }
        }


        \Config::set('database.default', 'mysql');

        \DB::reconnect('mysql');

        return compact('differences');
    }
}