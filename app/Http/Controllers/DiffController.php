<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;

use App\Database;

use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use App\Services\DiffService;

class DiffController extends Controller
{

    protected $diffService;

    public function __construct(DiffService $diffService)
    {
        $this->diffService = $diffService;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'database_one' => 'required',
            'database_two' => 'required',
        ]);

    }

    public function create() {
        $databases = Database::lists('name', 'id');

        return view('diff.select_database', compact('databases'));
    }

    public function load(Request $request)
    {

        $data = $request->all();

        $database_one = Database::findOrFail($data['database_one']);
        $database_two = Database::findOrFail($data['database_two']);

        $diff = $this->diffService->diff($database_one, $database_two);

        $differences = $diff['differences'];

        return view('diff.diff', compact('mapping_one', 'mapping_two', 'database_one', 'differences', 'database_two'));
    }
}