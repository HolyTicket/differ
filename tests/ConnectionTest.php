<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ConnectionTest extends TestCase
{
    public function testConnectToDatabase()
    {
        // Checkt of er verbinding kan worden gemaakt met een database
    }
    public function testResetDatabase()
    {
        // Checkt of de verbinding juist wordt gereset (teruggezet naar orginele databae)
    }
}