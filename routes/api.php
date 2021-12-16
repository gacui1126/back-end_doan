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

Route::prefix('user')->group(function(){
    Route::get('/get-all','Api\UserController@getUser');
    Route::post('/create','Api\UserController@CreateUser')->name('create.user');
    Route::get('/info','Api\UserController@info')->name('user.info')->middleware('auth:api');
    Route::post('/edit', 'Api\UserController@editUser');
    Route::post('/update', 'Api\UserController@updateUser');
    Route::post('/delete', 'Api\UserController@deleteUser');
    Route::post('/view', 'Api\UserController@view');
    Route::post('/img/upload','Api\UserController@uploadImg');
    Route::get('/get-avatar','Api\UserController@getAvatar');
    Route::post('/info/update', 'Api\UserController@updateUserInfo');
    Route::post('/info/reset-pass','Api\UserController@resetPass');
});

Route::prefix('team')->group(function(){
    Route::post('/create','Api\TeamController@create');
    Route::get('/show','Api\TeamController@show');
    Route::post('/edit','Api\TeamController@edit');
    Route::post('/update','Api\TeamController@update');
    Route::post('/delete','Api\TeamController@delete');
    Route::post('/teamuser','Api\TeamController@teamUser');
    Route::post('/change-team','Api\TeamController@changeTeam');
    Route::get('/get-all-team','Api\TeamController@getAllTeam');
});

Route::prefix('project')->group(function(){
    Route::get('/get-all-data','Api\ProjectController@getAllData');
    Route::post('/create','Api\ProjectController@createProject');
    Route::post('/all','Api\ProjectController@getProject');
    Route::post('/edit','Api\ProjectController@edit');
    Route::post('/update','Api\ProjectController@update');
    Route::post('/delete','Api\ProjectController@delete');
    Route::post('/info','Api\ProjectController@info');
    Route::post('/team/detail','Api\ProjectController@teamDetail');
    Route::post('/go','Api\ProjectController@projectCV');
    Route::get('/all','Api\ProjectController@projectAll');
    Route::post('/add-team','Api\ProjectController@addTeam');
    Route::post('/add-user','Api\ProjectController@addUser');
    Route::post('/get-time','Api\ProjectController@getTime');

});

Route::prefix('task')->group(function(){
    Route::post('/create','Api\TaskController@create');
    Route::post('/get-all','Api\TaskController@getAll');
    Route::post('/update/name','Api\TaskController@updateName');
    Route::post('/edit-name','Api\TaskController@editName');
    Route::post('/delete','Api\TaskController@deleteTask');
    Route::post('/get','Api\TaskController@getTask');
    // Route::get('/parameter','Api\TaskController@parameter');

});

Route::prefix('task-detail')->group(function(){
    Route::post('/create','Api\TaskDetailController@create');
    Route::post('/get','Api\TaskDetailController@get');
    Route::post('/get/task-card','Api\TaskDetailController@getTaskCard');
    Route::post('/add/user','Api\TaskDetailController@addUser');
    Route::post('/get/user','Api\TaskDetailController@getUser');
    Route::post('/delete/user','Api\TaskDetailController@deleteUser');
    Route::post('/get/user-of-team','Api\TaskDetailController@userOfTeam');

});

Route::prefix('tag')->group(function(){
    Route::post('/create','Api\TagController@create');
    Route::post('/get-tag-user','Api\TagController@getTagUser');
    Route::post('/add-tag-task','Api\TagController@addTagTask');
    Route::post('/get-tag-taskdetail','Api\TagController@getTagTaskDetail');
    Route::post('/edit','Api\TagController@editTag');
    Route::post('/update','Api\TagController@update');

});














