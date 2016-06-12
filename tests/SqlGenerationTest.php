<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SqlGenerationTest extends TestCase
{
    private $sqlService;

    public function __construct() {
        $this->sqlService = new \App\Services\SqlGenerationService();
    }
    public function testCreateTable_ShouldReturn_ValidSQL()
    {
        // Test of de CREATE TABLE queries correct zijn.
    }
    public function testDropTable_ValidSQL()
    {
        // Test of de CREATE TABLE queries correct zijn.
    }
    public function testaddColumn_ValidSQL() {
        // Test of de ADD COLUMN queries correct zijn.
    }
    public function testAlterColumn_ValidSQL() {
        // Test of de ALTER COLUMN queries correct zijn.
    }
    public function testAddIndex_ValidSQL() {
        // Test of de ADD INDEX queries correct zijn. Hieronder vallen de queries voor de primary keys, unique keys en reguliere keys.
    }
}