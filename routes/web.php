<?php

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

Route::get('/', 'IndexController@index');

Route::get('/{server}/{summonerOrPage}', 'IndexController@index')
    ->where('server', '(?:br|eune|euw|jp|kr|lan|las|na|oce|pbe|ru|tr|page)')
    ->where('summonerOrPage', '[^/]+');
