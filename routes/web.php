<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', '\App\Http\Controllers\WelcomeController@index');
Route::get('/login', '\App\Http\Controllers\WelcomeController@index');
Route::post('/login', '\App\Http\Controllers\WelcomeController@login');
Route::get('/logout', '\App\Http\Controllers\WelcomeController@logout');
Route::get('/logsviewer/{logname?}', '\App\Http\Controllers\LogsViewerController@index');
Route::get('/deletelog/{logname}', '\App\Http\Controllers\LogsViewerController@deleteLog');
Route::get('/restartconsumer', '\App\Http\Controllers\LogsViewerController@restartConsumer');
Route::get('/testingtools', '\App\Http\Controllers\TestingToolsController@index');

Route::post('/testingtools/sendmessage', '\App\Http\Controllers\TestingToolsController@sendMessage');
Route::post('/testingtools/bustcache', '\App\Http\Controllers\TestingToolsController@bustCache');
Route::post('/testingtools/fakeweberorr', '\App\Http\Controllers\TestingToolsController@fakeWebsiteError');

// webhook calls used for testing and local development
Route::post('/relaymessage', '\App\Http\Controllers\MessageController@relayMessage');