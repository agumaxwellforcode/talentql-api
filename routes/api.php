<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Application  (Api) routes

/* Todo Status route (index, show, update, destroy),
   This part is to give the user the ability to set his/her metrics of mesuring tasks,
   therefore a todo (task) can asume the status of only defined status(in the db)
*/

Route::resource('todostatus', 'App\Http\Controllers\TodoStatusController', ['except' => ['create', 'edit']]);


// Todo route (index, show, update, destroy)
Route::resource('todos', 'App\Http\Controllers\TodoController', ['except' => ['create', 'edit']]);
