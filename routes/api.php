<?php

use Illuminate\Http\Request;

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

Route::get('summoner/{server}/{summoner}', 'ApiController@eta')
    ->where('server', '(?:br|eune|euw|jp|kr|lan|las|na|oce|pbe|ru|tr)')
    ->where('summoner', '[^/]+');

Route::get('recent', 'ApiController@recent');

Route::post('feedback', 'ApiController@feedback');
