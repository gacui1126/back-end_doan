<?php

namespace App\Http\Controllers\Api;

use App\Events\NoticationEvent;
use App\Http\Controllers\Controller;
use App\Notications;
use App\Projects;
use App\Task_details;
use Illuminate\Http\Request;
use App\Teams;
use App\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use SebastianBergmann\CodeCoverage\Report\Xml\Project;

class ProjectController extends Controller
{

    public function __construct()
    {
    //    $this->middleware('auth:api')->except('login');
       $this->middleware('permission:list project')->only('all');
       $this->middleware('permission:add project')->only('createProject');
       $this->middleware('permission:edit project')->only('update');
       $this->middleware('permission:delete project')->only('delete');
    }

    public function getAllData(){
        $teams = Teams::all();
        $users = User::all();
        return response()->json([
            'teams' => $teams,
            'users' => $users
        ],200);
    }
    public function createProject(Request $req){
        $data = $req->value;
        $id = $req->id;
        $user = User::where('id', $id)->first();

        $checkName = Projects::where('name',$data['name'])->first();
        if($checkName){
            return response()->json([
                'message' => 'Tên đã tồn tại'
            ],422);
        }

        if(!($data['name'] == '')){
            $name = $data['name'];
            $date_start = date('Y-m-d H:i:s', strtotime($data['date_start']));
            $date_end = date('Y-m-d H:i:s', strtotime($data['date_end']));

            if($date_start >= $date_end){
                return response()->json([
                    'message' => 'Thời gian bắt đầu và kết thúc không hợp lệ'
                ],422);
            }

            $project = Projects::create([
                'name' => $name,
                'start_at' => $date_start,
                'end_at' => $date_end,
                'user_create_id' => $user->id
            ]);

            //thêm người tạo vào
            DB::table('project_user')->insert([
                'project_id' => $project->id,
                'user_id' => $user->id
            ]);

            $notication = Notications::create([
                'content' => 'Bạn đã được thêm vào dự án : ' .''. $project->name,
            ]);

            //Tạo thông báo cho người tạo
            DB::table('noti_user')->insert([
                'user_id' => $user->id,
                'noti_id' => $notication->id,
            ]);
            broadcast(new NoticationEvent($notication,$user));
            $team = $data['team'];
            foreach ($team as $teams) {
                DB::table('project_team')->insert([
                'project_id' => $project->id,
                'team_id' => $teams['id']
            ]);
        }
            $user = $data['user'];
            foreach($user as $users){
                DB::table('project_user')->insert([
                    'project_id' => $project->id,
                    'user_id' => $users['id']
                ]);
                DB::table('noti_user')->insert([
                    'user_id' => $users['id'],
                    'noti_id' => $notication->id,
                ]);
                $userChannel = User::where('id',$users['id'])->first();
            broadcast(new NoticationEvent($notication,$userChannel));
            }

            return response()->json([
                'message' => 'Tạo project thành công',
                'data' => $project
            ],200);

        }else{
            return response()->json([
                'message' => 'Hãy nhập tên dự án',
                'data' => $data['name']
            ],422);
        }

    }

    public function all(Request $req){
        $project = Projects::orderBy('id','desc')->paginate($req->total);
        foreach($project as $pro){
            $now = date('Y-m-d H:i:s', strtotime(Carbon::now('Asia/Ho_Chi_Minh')));
            $taskDetail = Task_details::where('project_id',$pro->id)->get();
            $date = Carbon::parse($pro->end_at);
            if($date > $now){
                $diff = $date->diffInDays($now);
                $pro->deadline = $diff;
            }else{
                $pro->deadline = 0;
            }
            $pro->task_details = $taskDetail;
            $pro->countComplete = 0;
            $pro->countTaskDetail = 0;
            foreach($pro->task_details as $t){
                if($t->completed == 1){
                    $pro->countComplete = $pro->countComplete + 1;
                }
                $pro->countTaskDetail = $pro->countTaskDetail + 1;
            }
        }
        return response()->json([
            'data' => $project,
        ],200);
    }

    public function edit(Request $req){
        $project = Projects::where('id', $req->id)->first();
        $projectName = $project->name;
        $date_start = $project->start_at;
        $date_end = $project->end_at;
        $team = $project->teams;
        $user = $project->users;

        return response()->json([
            'id' => $project->id,
            'projectName' => $projectName,
            'team' => $team,
            'user' => $user,
            'dateStart' => $date_start,
            'dateEnd' => $date_end
        ],200);
    }
    public function update(Request $req){
        $project = Projects::where('id',$req->id)->first();
        $data = $req->value;
        $checkName = Projects::where('name',$data['projectName'])->first();

        if($data['projectName'] != $project->name && $checkName){
            return response()->json([
                'message' => 'Tên dự án đã tồn tại!! vui lòng chọn tên khác'
            ],422);
        }
        if (!($data['projectName'] == '')) {
            if($data['dateStart'] >= $data['dateEnd']){
                return response()->json([
                    'message' => 'Thời gian bắt đầu và kết thúc không hợp lệ'
                ],422);
            }
            $project->update([
                'name' => $data['projectName'],
                'start_at' => $data['dateStart'],
                'end_at' => $data['dateEnd']
            ]);

            DB::table('project_team')->where('project_id',$req->id)->delete();
            $team = $data['team'];
                foreach($team as $teams){
                    DB::table('project_team')->insert([
                        'project_id' => $project->id,
                        'team_id' => $teams['id']
                ]);
            }

            DB::table('project_user')->where('project_id',$req->id)->delete();
            $user = $data['user'];
                foreach($user as $users){
                    DB::table('project_user')->insert([
                        'project_id' => $project->id,
                        'user_id' => $users['id']
                ]);
            }

            return response()->json([
                'message' => 'Update thành công',
                // 'data' => $team
            ],200);
        }else{
            return response()->json([
                'message' => 'Vui lòng không để trống tên dự án',
                'data' => $data['projectName']
            ],422);
        }

    }
    public function delete(Request $req){
        $project = Projects::find($req->id);
        $project->delete();
        DB::table('project_user')->where('project_id',$req->id)->delete();
        DB::table('project_team')->where('project_id',$req->id)->delete();
        return response()->json([
            'message' => 'Xoá dự án thành công'
        ],200);
    }

    public function info(Request $req){
        $project = Projects::where('id',$req->id)->first();
        $userCreate = User::where('id',$project->user_create_id)->first();
        $teams = $project->teams;
        foreach($teams as $t){
            $t->users;
        }
        $users = $project->users;
        return response()->json([
            'project' => $project,
            'userCreate' => $userCreate,
            'teams' => $teams,
            'users' => $users,
        ],200);
    }

    public function teamDetail(Request $req){
        $team = Teams::where('id',$req->id)->first();
        $user = $team->users;
        return response()->json([
            'data' => $user
        ],200);
    }

    public function projectCV(Request $req){
        $project = Projects::where('id',$req->id)->first();
        $date_end = Carbon::parse($project->end_at)->isoFormat('MMMM D YYYY');
        return response()->json([
            'data' => $project,
            'date_end' => $date_end
        ],200);
    }

    public function getTime(Request $req){
        $project = Projects::where('id',$req->id)->first();
        $date_end = Carbon::parse($project->end_at)->isoFormat('MMMM D YYYY');
        return response()->json([
            'data' => $date_end
        ],200);
    }

    public function addTeam(Request $req){
        $project = Projects::where('id',$req->id)->first();
        $team = $req->team;
        foreach ($team as $teams) {
            $checkTeam = DB::table('project_team')->whereProject_idAndTeam_id($project->id,$teams['id'])->first();
            if(!$checkTeam){
                DB::table('project_team')->insert([
                    'project_id' => $project->id,
                    'team_id' => $teams['id']
                ]);

            }else{
                return response()->json([
                    'message' => 'Tồn tại team đã có trong dự án'
                ],422);
            }

        }
        return response()->json([
            'message' => 'Thêm team thành công',
            'data' => $teams
        ],200);
    }

    public function addUser(Request $req){
        $project = Projects::where('id',$req->id)->first();
        $user = $req->user;
        foreach ($user as $users) {
            $checkUser = DB::table('project_user')->whereProject_idAndUser_id($project->id,$users['id'])->first();

            if(!$checkUser){
                DB::table('project_user')->insert([
                    'project_id' => $project->id,
                    'user_id' => $users['id']
                ]);

            }else{
                return response()->json([
                    'message' => 'Tồn tại thành viên đã có trong dự án'
                ],422);
            }

        }
        return response()->json([
            'message' => 'Thêm thành viên thành công',
            'data' => $users
        ],200);
    }

    public function getMyProject(Request $req){
        $user = auth('api')->user();
        $project = $user->projects;
        foreach($project as $pro){
            $taskDetail = Task_details::where('project_id',$pro->id)->get();
            $now = date('Y-m-d H:i:s', strtotime(Carbon::now('Asia/Ho_Chi_Minh')));
            $date = Carbon::parse($pro->end_at);
            if($date > $now){
                $diff = $date->diffInDays($now);
                $pro->deadline = $diff;
            }else{
                $pro->deadline = 0;
            }
            $pro->task_details = $taskDetail;
            $pro->countComplete = 0;
            $pro->countTaskDetail = 0;
            foreach($pro->task_details as $t){
                if($t->completed == 1){
                    $pro->countComplete = $pro->countComplete + 1;
                }
                $pro->countTaskDetail = $pro->countTaskDetail + 1;
            }
        }
        return response()->json([
            'data' => $project
        ],200);
    }
    public function switchPro(){
        $user = auth('api')->user();
        if($user->hasRole(['Admin','Manager'])){
            $project = Projects::all();
            return response()->json([
                'data' => $project
            ],200);
        }else{
            $project = $user->projects;
            return response()->json([
                'data' => $project
            ],200);
        }
    }
}
