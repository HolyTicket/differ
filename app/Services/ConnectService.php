<?php
namespace App\Services;

use App\Models\Connection;
use App\Services\BaseService;
use App\Models\Deploy;
use App\Models\Change;

use Auth;

/**
 * Class ConnectService
 * @package App\Services
 */
class ConnectService extends BaseService
{

    /**
     * reset the connection and reconnect to the default DifferDB application database
     */
    public static function reset() {
        // Reset the connection
        \Config::set('database.default', 'mysql');

        // Reconnect to the default DifferDB database
        \DB::reconnect('mysql');
    }

    /**
     * Connect to a custom database
     * @param $name
     * @param $db
     * @return bool|string string when error occured, true when successful
     */
    public static function connect($name, $db) {
        // Sometimes $db is array, handle this
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

        // Write the config parameters
        \Config::set(sprintf('database.connections.%s.host', $name), $host);
        \Config::set(sprintf('database.connections.%s.username', $name), $username);
        \Config::set(sprintf('database.connections.%s.password', $name), $password);
        \Config::set(sprintf('database.connections.%s.database', $name), $database_name);
        \Config::set(sprintf('database.connections.%s.driver', $name), 'mysql');

        // Set the default database (temporary)
        \Config::set('database.default', $name);

        // If this query succeeds, the connection has been successfully established. otherwise, return the error (string)
        try {
            \DB::select('SHOW TABLES');
        } catch(\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Get the other databases for this connection, so we can deploy to other databases as well.
     * @param Connection $destination_connection
     * @return array
     */
    public static function getOtherDatabases(Connection $destination_connection) {
        // Connect to the database
        self::connect('db_two', $destination_connection);

        // Get the databases by this query
        $databases = \DB::select('SHOW DATABASES');

        // Add the database names to a array $choices
        $choices = [];
        foreach($databases as $db) {
            $choices[] = $db->Database;
        }

        // Return the list with databases
        return $choices;
    }
}