<?php
namespace App\Services;

use App\Models\Connection;
use App\Services\BaseService;
use App\Models\Deploy;
use App\Models\Change;

use Auth;

class ConnectService extends BaseService
{
    public static function reset() {
        \Config::set('database.default', 'mysql');

        \DB::reconnect('mysql');
    }
    public static function connect($name, $db) {
        if(!is_array($db)) {
            $host = $db->host;
            $username = $db->username;
            $password = $db->password;
            $database_name = $db->database_name;
        } else {
            $host = $db['host'];
            $username = $db['username'];
            $password = $db['password'];
            $database_name = $db['database_name'];
        }

        \Config::set(sprintf('database.connections.%s.host', $name), $host);
        \Config::set(sprintf('database.connections.%s.username', $name), $username);
        \Config::set(sprintf('database.connections.%s.password', $name), $password);
        \Config::set(sprintf('database.connections.%s.database', $name), $database_name);
        \Config::set(sprintf('database.connections.%s.driver', $name), 'mysql');

        \Config::set('database.default', $name);

        try {
            \DB::select('SHOW TABLES');
        } catch(\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }
    public static function getOtherDatabases(Connection $destination_connection) {
        self::connect('db_two', $destination_connection);
        $databases = \DB::select('SHOW DATABASES');
        $choices = [];
        foreach($databases as $db) {
            $choices[] = $db->Database;
        }
        return $choices;
    }
}