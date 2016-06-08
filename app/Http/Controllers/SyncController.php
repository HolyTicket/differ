<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;

use App\Models\Connection;
use App\Models\Deploy;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Sync;
use Connect;

class SyncController extends Controller
{

    public function sql(Request $request) {
        $data = $request->all();
        $changes = array_values($data['change']);

        $sql = Sync::generateSql($changes);

        return view('sync.sql', compact('sql'));
    }

    public function execute(Request $request) {
        $data = $request->all();

        $data['original_data'] = json_decode($data['original_data'], true);
        $changes = array_values($data['original_data']['change']);

        $sql = Sync::generateSql($changes);
        $destination_connection = Connection::findOrFail($data['original_data']['database_two']);

        if(empty($data['databases'])) {
            $data['databases'] = [];
        }

        $results = Sync::executeMysql($sql, $destination_connection, $data['databases']);
        return view('sync.results', compact('results'));
    }

    public function confirm(Request $request) {
        $data = $request->all();

        $destination_connection_id = (int) $data['database_two'];
        $destination_connection = Connection::findOrFail($destination_connection_id);

        $all_databases = Connect::getOtherDatabases($destination_connection);
        return view('sync.confirm', compact('all_databases', 'destination_connection', 'data'));
    }

}