<?php

namespace App\Http\Controllers;

use App\Models\Connection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class ConnectionsController extends Controller
{
    public $validations = [
        'name' => 'required',
        'host' => 'required',
        'database_name' => 'required|connection',
        'username' => 'required',
    ];
    public function index() {
        $databases = Connection::where('user_id', Auth::id())->get();
        return view('databases.index', compact('databases'));
    }

    public function create(Request $request) {
        return view('databases.edit');
    }

    public function update(Request $request, $id) {
        $this->validate($request, $this->validations);
        $data = $request->all();
        Connection::find($id)->update($data);
    }

    public function store(Request $request) {
        $this->validate($request, $this->validations);
        $data = $request->all();
        Connection::create($data);
    }

    public function edit($id)
    {
        $database = Connection::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        return view('databases.edit')->withDatabase($database);
    }

    public function destroy($id) {
        $database = Connection::findOrFail($id);
        $database->delete();
    }

}