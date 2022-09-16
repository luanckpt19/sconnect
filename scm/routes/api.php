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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    Route::match(['get', 'post'], '/youtube/v3-get-video', 'YoutubeController@apiV3GetVideo');
    Route::match(['get', 'post'], '/youtube/v3-get-channel-info', 'YoutubeController@apiV3GetChannelInfo');
    Route::match(['get', 'post'], '/youtube/v3-get-video-info', 'YoutubeController@apiV3GetVideoDetail');
});