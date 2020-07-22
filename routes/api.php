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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('auth')->group(function(){
	Route::post('/login','API\AuthController@login')->name('api_auth_login');
	Route::post('/register','API\AuthController@register')->name('api_auth_register');
	Route::get('/logout','API\AuthController@logout')->name('api_auth_logout');
});

Route::prefix('account')->middleware('auth:api')->group(function (){
    Route::post('/create','API\AccountController@createAccount')->name('api_acount_create');
    Route::post('/show','API\AccountController@showAccount')->name('api_acount_show');
    Route::post('/get/{id}','API\AccountController@getAccount')->name('api_acount_get');
    Route::post('/delete/{id}','API\AccountController@deleteAccount')->name('api_acount_delete');
    Route::post('/active/{id}','API\AccountController@activeAccount')->name('api_acount_active');
    Route::post('/passive/{id}','API\AccountController@passiveAccount')->name('api_acount_passive');
    Route::post('/deposit/{id}','API\AccountController@depositAccount')->name('api_acount_deposit');
    Route::post('/withdraw/{id}','API\AccountController@withdrawAccount')->name('api_acount_withdraw');
});

