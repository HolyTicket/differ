<?php
namespace App\Services;

use App\Models\Virtual\Column;
use App\Models\Virtual\Index;
use App\Models\Virtual\Table;
use App\Services\BaseService;

use StringHelper;

class SqlGenerationService extends BaseService
{
    private $sql = '';
    public $all = '';

    public function clear() {
        $this->sql = '';
    }

    public function addComma() {
        $this->sql .= ',';
    }

    public function addSemicolon() {
        $this->sql .= ';';
    }

    public function append($line) {
        $this->sql .= $line;
    }

    private function removeLastChar() {
        $this->sql = substr($this->sql, 0, -1);
    }

    private function newLine() {
        $this->sql .= "\n";
    }

    private function tableClose(Table $table) {
        $charset = explode('_', $table->collation)[0];
        return sprintf(' CHARACTER SET %s COLLATE %s ROW_FORMAT=%s  ENGINE=%s;', $charset, $table->collation, $table->row_format, $table->engine) . "\n";
    }

    public function createTable(Table $table) {
        $this->clear();

        $this->append(sprintf('CREATE TABLE IF NOT EXISTS `%s` (', $table->name));

        foreach($table->getColumns() as $column_name => $column) {
            $this->newLine();
            $this->append($this->column($column, [], false, $table->getIndices()));
            $this->addComma();
        }

        foreach($table->getIndices() as $name => $index) {
            $this->newLine();
            $this->append($this->addIndexImplicit($index));
            $this->addComma();
        }

        $this->removeLastChar();
        $this->newLine();
        $this->append(')');

        $this->append($this->tableClose(($table)));

        $this->newLine();
        $this->newLine();
        $this->newLine();

        $this->all .= $this->sql;

        return $this->sql;
    }

    public function dropTable(Table $table) {
        $this->clear();

        $this->append(sprintf('DROP TABLE `%s`', $table->name));
        $this->addSemicolon();
        $this->newLine();
        return $this->sql;
    }

    public function addColumn(Table $table, Column $column) {
        $sql = sprintf('ALTER TABLE `%s` ADD %s;', $table->name, $this->column($column)) . "\n";
        return $sql;
    }

    public function dropColumn(Table $table, Column $column) {
        $sql = sprintf('ALTER TABLE `%s` DROP `%s`;', $table->name, $column->name) . "\n";
        return $sql;
    }

    public function column(Column $column, array $attributes = [], $table_name = false) {
        // $column is destination column.

        if(empty($attributes)) {
            $attributes = (array) get_object_vars($column);
        }

        $defs = [];
        $defs['name'] = $attributes['name'];
        $defs['table_name'] = $table_name;
        $defs['type'] = $attributes['type'];
        $defs['null'] = $attributes['notnull'] ? 'NOT NULL' : 'NULL';
        $defs['auto_increment'] = $attributes['auto_increment'] ? 'AUTO_INCREMENT' : '';
        $defs['comment'] = ($attributes['comment'] != null) ? sprintf("COMMENT '%s'", $attributes['comment']) : '';

        if($attributes['default'] == 'CURRENT_TIMESTAMP') {
            $defs['default'] = ' DEFAULT CURRENT_TIMESTAMP';
        } else if($attributes['default'] !== "" && $attributes['default'] != null) {
            $defs['default'] = sprintf("DEFAULT '%s'", $attributes['default']);
        } else if($attributes['default'] == null && !$column->notnull) {
            $defs['default'] = ' DEFAULT NULL';
        } else {
            $defs['default'] = '';
        }

        $sql = StringHelper::named('`%(name)s` %(type)s %(null)s %(auto_increment)s %(default)s %(comment)s', $defs);

        if($table_name) {
            $prepend = StringHelper::named('ALTER TABLE `%(table_name)s` CHANGE `%(name)s`', $defs);
            $sql = $prepend . ' ' . $sql;
        }

        return $sql;
    }

    public function alterColumn(Column $column, $type_of_change, $new_value, $old_value, $attributes, $table_name) {
        $attributes = (array) $attributes;
        $attributes[$type_of_change] = $new_value;

        $column_def = $this->column($column, (array) $attributes, $table_name)  . "; \n";

        return $column_def;
    }

    public function alterOption(Table $table, $option, $new_value) {
        $sql = '';
        switch($option) {
            case 'collation':
                $collation = $new_value;
                $character_set = explode('_', $new_value)[0];
                $sql = sprintf('ALTER TABLE `%s` CONVERT TO CHARACTER SET %s COLLATE %s;', $table->name, $character_set, $collation);
                break;
            case 'row_format':
                $sql = sprintf('ALTER TABLE `%s` ROW_FORMAT=%s;', $table->name, $new_value);
                break;
        }
        $sql .= "\n";

        return $sql;
    }

    public function addIndex(Table $table, Index $index) {
        $columns = "(`".implode("`,`", $index->columns)."`)";

        if($index->primary) {
            $sql = sprintf('ALTER TABLE `%s` ADD PRIMARY KEY %s;', $table->name, $columns);
        } elseif($index->unique) {
            $sql = sprintf('ALTER TABLE `%s` ADD UNIQUE KEY `%s` %s;', $table->name, $index->name, $columns);
        } else {
            $sql = sprintf('ALTER TABLE `%s` ADD KEY `%s` %s;', $table->name, $index->name, $columns);
        }
        $sql .= "\n";

        return $sql;
    }

    public function addIndexImplicit(Index $index) {
        $columns = "(`".implode("`,`", $index->columns)."`)";

        if($index->primary) {
            $sql = sprintf('PRIMARY KEY %s', $columns);
        } elseif($index->unique) {
            $sql = sprintf('UNIQUE KEY `%s` %s', $index->name, $columns);
        } else {
            $sql = sprintf('KEY `%s` %s', $index->name, $columns);
        }

        return $sql;
    }

}