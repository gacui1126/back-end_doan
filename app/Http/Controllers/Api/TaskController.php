<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Projects;
use App\Tasks;
use App\Task_details;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use LengthException;

class TaskController extends Controller
{
    public function create(Request $req){
        Tasks::create([
            'name' => $req->name,
            'project_id' => $req->project_id,
            'user_id' => $req->user_id,
        ]);
        return response()->json([
            'message' => 'Tạo task thành công'
        ],200);
    }
    public function getAll(Request $req){
        $task = Tasks::where('project_id',$req->id)->get();
        foreach($task as $ta){
            $taskDetail = $ta->task_details;
            $taskDetail = Task_details::where('task_id',$ta->id)->get();
            foreach($ta->task_details as $t){
                $t->count_comment = 0;
                $tag = $t->tags;
                $comment = $t->comments;
                foreach($t->comments as $co){
                    $co->replies;
                }
                $t->jobs;
                $t->count_job = 0;
                $t->count_job_completed = 0;
                $t->count_user = 0;
                $t->users;
                foreach($t->jobs as $j){
                    $j->job_details;
                }
            }
            $ta->show = false;
        }
        return response()->json([
            'data' => $task,
        ],200);

    }

    public function editName(Request $req){
        $task = Tasks::where('id',$req->id)->first();
        return response()->json([
            'data' => $task
        ],200);
    }

    public function updateName(Request $req){
        $task = Tasks::where('id', $req->id)->first();
        if(!$req->name){
            return response()->json([
                'message' => 'Vui lòng nhập tên task'
            ],422);
        }
        if(strlen($req->name) >= 30){
            return response()->json([
                'message' => 'Tên task không quá 30 kí tự'
            ],422);
        }
        $task->name = $req->name;
        $task->update();
        return response()->json([
            'message' => 'update thành công'
        ],200);
    }

    public function deleteTask(Request $req){
        $task = Tasks::where('id',$req->id)->first();
        $task->delete();
        return response()->json([
            'message' => 'Xoá task thành công'
        ],200);
    }
    public function getTask(Request $req){
        $task = Tasks::where('id',$req->id)->first();
        return response()->json([
            'data' => $task
        ],200);
    }
    // public function parameter(){
    //     $taskAll = Tasks::all();

    //     $taskAll->task_details;
    //     return response()->json([
    //         'data' => $taskAll
    //     ],200);
    // }
}
