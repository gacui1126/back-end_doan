<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use Illuminate\Routing\Route as RoutingRoute;

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
    Route::get('/get-all','Api\UserController@getUserA');
    Route::post('/create','Api\UserController@CreateUser');
    Route::get('/info','Api\UserController@info');
    Route::post('/edit', 'Api\UserController@editUser');
    Route::post('/update', 'Api\UserController@updateUser');
    Route::post('/delete', 'Api\UserController@deleteUser');
    Route::post('/view', 'Api\UserController@view');
    Route::post('/img/upload','Api\UserController@uploadImg');
    Route::get('/get-avatar','Api\UserController@getAvatar');
    Route::post('/info/update', 'Api\UserController@updateUserInfo');
    Route::post('/info/reset-pass','Api\UserController@resetPass');
    Route::get('/all','Api\UserController@all');
    Route::post('/check-per','Api\UserController@checkPer');

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
    Route::get('/all-project','Api\ProjectController@all');
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
    Route::post('/get/my-project','Api\ProjectController@getMyProject');
    Route::get('/switch-pro','Api\ProjectController@switchPro');

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
    Route::post('/deadline/set','Api\TaskDetailController@setDeadline');
    Route::post('/deadline/get','Api\TaskDetailController@getDeadline');
    Route::post('/deadline/delete','Api\TaskDetailController@deleteDeadline');
    Route::post('/completed','Api\TaskDetailController@completed');
    Route::post('/completed/get','Api\TaskDetailController@getCompleted');
    Route::post('/delete','Api\TaskDetailController@delete');
    Route::post('/task-for-me','Api\TaskDetailController@taskForMe');
    Route::post('complete-confirmation','Api\TaskDetailController@CompleteConfi');
    Route::post('get-request-complete','Api\TaskDetailController@getReComplete');
    Route::post('get-request-complete/detroy','Api\TaskDetailController@detroyRE');
    Route::post('/manager/complete-confirm','Api\TaskDetailController@managerCompleteConfir');
    Route::post('get-all-data','Api\TaskDetailController@getAllData');

});
Route::post('task-detail/history-change','Api\TaskDetailController@historyChange');

Route::prefix('tag')->group(function(){
    Route::post('/create','Api\TagController@create');
    Route::post('/get-tag-user','Api\TagController@getTagUser');
    Route::post('/add-tag-task','Api\TagController@addTagTask');
    Route::post('/get-tag-taskdetail','Api\TagController@getTagTaskDetail');
    Route::post('/edit','Api\TagController@editTag');
    Route::post('/update','Api\TagController@update');

});

Route::prefix('job')->group(function(){
    Route::post('/create','Api\JobController@create');
    Route::post('/get','Api\JobController@get');
    Route::post('/delete','Api\JobController@delete');
    Route::post('/job-detail/create','Api\JobController@createJobDetail');
    Route::post('/job-detail/get','Api\JobController@getJobDetail');
    Route::post('/job-detail/delete','Api\JobController@deleteJobDetail');
    Route::post('/job-detail/check','Api\JobController@checkJobDetail');

});

Route::prefix('comment')->group(function(){
    Route::post('/create','Api\CommentController@create');
    Route::post('/get','Api\CommentController@get');
    Route::post('/delete','Api\CommentController@delete');
    Route::post('/update','Api\CommentController@update');
    Route::post('/repply','Api\CommentController@repply');
    Route::post('/reply/delete','Api\CommentController@deleteReply');
    Route::post('/reply/update','Api\CommentController@updateReply');

});

Route::prefix('role')->group(function(){
    Route::get('/get','Api\RoleController@get');
    Route::post('/create','Api\RoleController@create');
    Route::post('/edit','Api\RoleController@edit');
    Route::post('/update','Api\RoleController@update');
    Route::post('/delete','Api\RoleController@delete');
    Route::get('/all','Api\RoleController@all');
    Route::post('user-per','Api\RoleController@userPer');
    Route::post('delete-role-user','Api\RoleController@deleteRoleUser');
    Route::post('check-role','Api\RoleController@checkRole');


});

Route::prefix('permission')->group(function(){
    Route::get('/get','Api\PermissionController@get');
    Route::post('/create','Api\PermissionController@create');
    Route::get('/{permissionName}', 'Api\PermissionController@check');
    Route::post('delete-per-user', 'Api\PermissionController@deletePerUser');
    // Route::get('/get-user', 'Api\PermissionController@getUser');
});


Route::prefix('chat')->group(function(){
    Route::get('/user', 'Api\ChatsController@user');
    Route::get('/messages/{user}', 'Api\ChatsController@fetchMessages');
    Route::post('/messages/{user}', 'Api\ChatsController@sendMessage');
    Route::post('/create-group', 'Api\ChatsController@createGroup');
    Route::get('/all-group', 'Api\ChatsController@allGroup');
    Route::post('/group/select', 'Api\ChatsController@groupSe');
    Route::get('/get-mess', 'Api\ChatsController@getMess');
    Route::post('/select-mess', 'Api\ChatsController@selectMess');
});

Route::prefix('notications')->group(function(){
    Route::post('/get-noti','Api\NoticationController@getNoti');
    Route::post('select-noti','Api\NoticationController@selectNoti');
});

Route::prefix('file')->group(function(){
    Route::post('/upload','Api\FileController@upload');
    Route::post('/get-file','Api\FileController@GetFile');
    Route::get('/download/{id}','Api\FileController@downloadFile');
    Route::post('/delete','Api\FileController@delete');
    Route::post('/get-all','Api\FileController@getAll');
    Route::post('/update','Api\FileController@update');
});

Route::prefix('overview')->group(function(){
    Route::get('/get/data/project','Api\OverviewController@getProject');
    Route::get('/get/data/user','Api\OverviewController@getUser');
    Route::post('/chart-project-data','Api\OverviewController@chartProjectData');

});
Route::post('/broadcasting/auth', 'BroadCastingController')->middleware('auth:api');













