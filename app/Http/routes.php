<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::resource('/', 'ImporterController');
Route::resource('file', 'ImporterController');
Route::get('columns', 'ImporterController@columns');
Route::post('columns/put', 'ImporterController@cposts');