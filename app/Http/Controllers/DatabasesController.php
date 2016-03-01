<?php

namespace App\Http\Controllers;

use App\Database;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DatabasesController extends Controller
{
    public function index() {
        $databases = Database::all();
        return view('databases.index', compact('databases'));
    }

    public function create(Request $request) {
        return view('databases.edit');
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $data = $request->all();
        Database::find($id)->update($data);
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $data = $request->all();
        Database::create($data);
    }

    public function edit($id)
    {
        $database = Database::findOrFail($id);
        return view('databases.edit')->withDatabase($database);
    }

    public function destroy($id) {
        $database = Database::find($id);
        $database->delete();
    }

}