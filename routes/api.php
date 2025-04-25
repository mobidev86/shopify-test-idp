<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// OAuth Routes
Route::post('/oauth/token', '\Laravel\Passport\Http\Controllers\AccessTokenController@issueToken');
Route::get('/oauth/clients', '\Laravel\Passport\Http\Controllers\ClientController@forUser');
Route::post('/oauth/clients', '\Laravel\Passport\Http\Controllers\ClientController@store');
Route::put('/oauth/clients/{client_id}', '\Laravel\Passport\Http\Controllers\ClientController@update');
Route::delete('/oauth/clients/{client_id}', '\Laravel\Passport\Http\Controllers\ClientController@destroy'); 