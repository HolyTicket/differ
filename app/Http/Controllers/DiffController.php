<?php

namespace App\Http\Controllers;

use App\Models\Virtual\Database;
use App\Services\SqlGenerationService;
use App\User;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;

use App\Models\Connection;
use App\Models\Deploy;
use App\Models\Change;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Diff;

class DiffController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'database_one' => 'required',
            'database_two' => 'required',
        ]);

    }

    public function sync(Request $request)
    {

    }

    public function create()
    {
        $databases = Connection::where('user_id', Auth::id())->lists('name', 'id');

        return view('diff.select_database', compact('databases'));
    }

    public function load(Request $request)
    {
        $this->validate($request, [
            'database_one' => 'required|integer',
            'database_two' => 'required|integer|different:database_one',
        ]);

        $data = $request->all();

        $connection_one = Connection::findOrFail($data['database_one']);
        $connection_two = Connection::findOrFail($data['database_two']);

        $db_one = new Database($connection_one, 'db_one');
        $db_two = new Database($connection_two, 'db_two');

        $deployment = $db_one->diff($db_two);

        $changes_by_entity = [];

        foreach($deployment->changes()->get() as $change) {
            if($change->entity == 'table') {
                $changes_by_entity['table'][$change->name] = $change->type;
            } else if($change->entity == 'column') {
                $changes_by_entity['column'][$change->name][$change->parent->name] = $change->type;
            }
        }

        $view = $deployment->changes()->count() ? 'diff.diff' : 'diff.same';

        return view($view, compact('deployment',  'changes_by_entity', 'db_one', 'deployment_id', 'db_two', 'connection_one', 'connection_two'));
    }
}