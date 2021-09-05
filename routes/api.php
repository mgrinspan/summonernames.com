<?php

use App\Http\Controllers\ApiController;
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

Route::get('summoner/{server}/{summonerName}', [ApiController::class, 'eta'])
	->where('server', '(?:br|eune|euw|jp|kr|lan|las|na|oce|pbe|ru|tr)')
	->where('summonerName', '[^/]+');

Route::get('recent', [ApiController::class, 'recent']);

Route::post('feedback', [ApiController::class, 'feedback']);
