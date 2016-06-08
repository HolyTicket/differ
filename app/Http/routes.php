<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/', function () {
        return view('welcome');
    });

    Route::post('diff/load', 'DiffController@load');
    Route::resource('diff', 'DiffController');
    Route::post('sync/sql', 'SyncController@sql');
    Route::post('sync/execute', 'SyncController@execute');
    Route::post('sync/confirm', 'SyncController@confirm');
    Route::resource('databases', 'ConnectionsController');
    Route::resource('faq', 'QuestionsController');
    Route::get('/home', 'HomeController@index');
    Route::get('/profile/{id}', 'UsersController@showProfile');
//    Route::get('/login', array('as' => 'login', 'uses' => 'Auth\AuthController@getLogin'));
});