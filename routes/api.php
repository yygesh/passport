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

Route::group(['middleware' => 'auth:api'], function () {
	
   Route::post('/oauth/clients', array('as'=>'clients', 'uses'=> '\Laravel\Passport\Http\Controllers\ClientController@store'));
   Route::post('/oauth/personal-access-tokens', array('as'=>'clients', 'uses'=> '\Laravel\Passport\Http\Controllers\PersonalAccessTokenController@store'));  
	
   });
Route::group(['middleware' => 'auth:api','prefix' => 'v1'], function () {
	Route::get('/user', array('as' => 'user', 'uses' => 'LoginController@user'));
   	Route::get('/getUsers', array('as' => 'users', 'uses' => 'UserController@getUsers'));
   	Route::post('/createUserClient', array('uses' => 'UserController@createUserClient'));
   });

Route::group(['prefix' => 'v1'], function() {
	
	
	//Route::post('/clientCreate', array('uses' => 'LoginController@clientCreate'));
    Route::POST('/login', array('as' => 'login', 'uses' => 'LoginController@login'));
	Route::post('/register', array('uses' => 'UserController@register'));

});
