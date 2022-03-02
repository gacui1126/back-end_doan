<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Projects;
use App\Task_details;
use App\User;
use Illuminate\Http\Request;

class OverviewController extends Controller
{
    public function getProject(){
        $project = Projects::all();
        return response()->json([
            'data' => $project
        ]);
    }

    public function getUser(){
        $user = User::all();
        return response()->json([
            'data' => $user
        ]);
    }
    public function chartProjectData(Request $req){
        $month = 12;
        for($i = 1; $i <= $month; $i++){
            $projectM = Projects::whereYear('start_at',$req->date)->whereMonth('start_at',$i)->get();
            $project[$i] = $projectM;
        }
        foreach($project as $pro){
            foreach($pro as $p){
                $taskDetail = Task_details::where('project_id',$p->id)->get();
                $p->task_details = $taskDetail;
                $p->countComplete = 0;
                $p->countTaskDetail = 0;
                $p->projectComplete = 0;
                foreach($p->task_details as $t){
                    if($t->completed == 1){
                        $p->countComplete = $p->countComplete + 1;
                    }
                    $p->countTaskDetail = $p->countTaskDetail + 1;
                }
                if($p->countTaskDetail == $p->countComplete && $p->countTaskDetail == 1){
                    $p->projectComplete = 1;
                }else{
                    $p->projectComplete = 0;
                }
            }
        }
        return response()->json([
            'data' => $project
        ],200);
    }
}
