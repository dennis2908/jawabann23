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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

*/
Route::group(['middleware' => ['cors']], function () {

    Route::POST('/login', 'API\APIController@login');

	Route::POST('/createUser', 'API\APIController@createUser');

	Route::group(['middleware' => ['cekTokenAPI']], function () {

		Route::get('articles', 'ArticleController@index');
		
		Route::get('article/{id}', 'ArticleController@show');

		Route::post('article', 'ArticleController@store');

		Route::put('article', 'ArticleController@store');

		Route::delete('article/{id}', 'ArticleController@destroy');

		
	});	

});

//Route::get('articles', 'ArticleController@index');
