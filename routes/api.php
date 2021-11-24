<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register','Api\UserController@register')->name('register');
Route::post('/login','Api\UserController@login')->name('login');
Route::post('/send','Api\SendMailController@send_mail');

Route::get('user/get-all','Api\UserController@getUser');
Route::post('/user/create','Api\UserController@CreateUser')->name('create.user');
Route::get('/user/info','Api\UserController@info')->name('user.info')->middleware('auth:api');
Route::post('user/edit', 'Api\UserController@editUser');
Route::post('user/update', 'Api\UserController@updateUser');
Route::post('user/delete', 'Api\UserController@deleteUser');
Route::post('user/view', 'Api\UserController@view');
Route::post('user/img/upload','Api\UserController@uploadImg');
Route::get('user/get-avatar','Api\UserController@getAvatar');
Route::post('user/info/update', 'Api\UserController@updateUserInfo');
Route::post('user/info/reset-pass','Api\UserController@resetPass');


Route::post('/team/create','Api\TeamController@create');
Route::get('/team/show','Api\TeamController@show');
Route::post('/team/edit','Api\TeamController@edit');
Route::post('/team/update','Api\TeamController@update');
Route::post('/team/delete','Api\TeamController@delete');
Route::post('/team/teamuser','Api\TeamController@teamUser');
Route::post('/team/change-team','Api\TeamController@changeTeam');

