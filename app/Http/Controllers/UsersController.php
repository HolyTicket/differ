<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Event;
use App\Events\UserWasRegistered;

class UsersController extends Controller
{
    public function showProfile($id)
    {
        $user = User::findOrFail($id);
        Event::fire(new UserWasRegistered($user));
        return view('user.profile', ['user' => User::findOrFail($id)]);
    }
}