<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Task_details;
use App\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Teams;
use App\User;

class TaskDetailController extends Controller
{
    public function create(Request $req){
        if($req->cardName == ''){
            return response() -> json([
                'message' => 'Vui lòng điền tên card'
            ],422);
        }
        $task = Task_details::create([
            'name' => $req->cardName,
            'user_create_id' => $req->userId,
            'task_id' => $req->taskId,
            'project_id' => $req->project_id
        ]);
        return response() -> json([
            'message' => 'Taọ thẻ thành công',
            'data' => $task
        ],200);
    }
    public function get(Request $req){
        $taskDetail = Task_details::where('project_id', $req->id)->get();
        $taskDetail->users;
        return response() -> json([
            'data' => $taskDetail
        ],200);
    }

    public function getTaskCard(Request $req){
        $taskDetail = Task_details::where('id',$req->id)->first();
        return response()->json([
            'data'=>$taskDetail
        ],200);
    }
    public function addUser(Request $req){
        $user = $req->user;
        $task = Task_details::where('id',$req->id)->first();
        if(!$req->user){
            return response()->json([
                'message' => 'Vui lòng chọn thành viên muốn thêm vào'
            ],422);
        }else{
            foreach($user as $u){
                $checkUser = DB::table('task_detail_user')->whereTask_detail_idAndUser_id($req->id,$u['id'])->first();
                if($checkUser){
                    return response()->json([
                        'message' => 'Tồn tại thành viên đã có trong thẻ'
                    ],400);
                }
                DB::table('task_detail_user')->insert([
                    'task_detail_id' => $req->id,
                    'user_id' => $u['id']
                ]);
            }
        }
        return response()->json([
            'message' => 'Thêm thành viên thành công',
            'data' => $user
        ],200);
    }
    public function getUser(Request $req){
        $taskDetail = Task_details::where('id',$req->id)->first();
        $user = $taskDetail->users;
        return response()->json([
            'data' => $taskDetail
        ],200);
    }
    public function userOfTeam(Request $req){
        $teams = $req->team;
        $user = array();
        foreach($teams as $t){
            $te = Teams::where('id',$t['id'])->first();
            $user[] = $te->users;
        }
        return response()->json([
            'data' => $user
        ],200);

    }
    public function deleteUser(Request $req){
        $user = DB::table('task_detail_user')->whereTask_detail_idAndUser_id($req->taskId,$req->userId)->delete();
        return response()->json([
            'message' => 'Xoá user khỏi thẻ thành công'
        ],200);
    }
}