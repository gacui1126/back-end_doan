<?php

namespace App\Http\Controllers\Api;

use App\Events\jobEvent;
use App\Events\JobListEvent;
use App\Http\Controllers\Controller;
use App\Job_detail;
use App\Jobs;
use App\Task_details;
use App\TaskDetailHistoryChange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function create(Request $req){
        $taskDetail = Task_details::where('id',$req->taskDetailId)->first();
        $userInCard = $taskDetail->users;
        foreach($userInCard as $u){
            if($u->id == auth('api')->id()){
                $job = new Jobs;
                $job->name = $req->name;
                $job->task_detail_id = $req->taskDetailId;
                $job->save();
                $job->job_details = [];
                $job->addJobFormShow = true;
                $job->addJobForm = false;

                TaskDetailHistoryChange::create([
                    'task_detail_id' => $req->taskDetailId,
                    'user_change_id' => auth('api')->id(),
                    'content' => 'Đã tạo 1 bảng công việc : '.''.$job->name,
                ]);
                $event = 'createJob';
                broadcast(new jobEvent($taskDetail,$job,$event))->toOthers();
                return response()->json([
                    'message' => 'Tạo danh sách thành công',
                    'data' => $job
                ],200);
            }
        }

        return response()->json([
            'data' => auth('api')->id(),
            'message' => 'Bạn không thuộc thẻ này',
        ],422);
    }
    public function get(Request $req){
        $job = Jobs::where('task_detail_id',$req->taskDetailId)->get();
        foreach($job as $j){
            $j->job_details;
            $j->addJobFormShow = true;
            $j->addJobForm = false;
        }
        return response()->json([
            'data' => $job
        ],200);
    }
    public function delete(Request $req){
        $job = Jobs::where('id',$req->id)->first();
        $taskDetail = Task_details::where('id',$job->task_detail_id)->first();
        $userInCard = $taskDetail->users;
        foreach($userInCard as $u){
            if($u->id == auth('api')->id()){
                $event = 'deleteJob';
                broadcast(new jobEvent($taskDetail,$job,$event))->toOthers();
                $job->delete();
                $jobD = Job_detail::where('job_id',$req->id)->delete();
                TaskDetailHistoryChange::create([
                    'task_detail_id' => $taskDetail->id,
                    'user_change_id' => auth('api')->id(),
                    'content' => 'Đã xoá bảng công việc : ' .''.$job->name,
                ]);
                return response()->json([
                    'message' => 'Xoá danh sách thành công'
                ],200);
            }
        }
        return response()->json([
            'data' => $taskDetail,
            'message' => 'Bạn không thuộc thẻ này!!!'
        ],422);
    }
    public function createJobDetail(Request $req){
        if(!$req->name){
            return response()->json([
                'message' => 'Vui lòng nhập tên công việc'
            ],422);
        }
        $job = Jobs::where('id',$req->jobId)->first();
        $taskDetail = Task_details::where('id',$job->task_detail_id)->first();
        $userInCard = $taskDetail->users;
        foreach($userInCard as $u){
            if ($u->id == auth('api')->id()) {
                $jobD = new Job_detail();
                $jobD->name = $req->name;
                $jobD->job_id = $req->jobId;
                $jobD->save();

                TaskDetailHistoryChange::create([
                    'task_detail_id' => $taskDetail->id,
                    'user_change_id' => auth('api')->id(),
                    'content' => 'Đã tạo 1 công việc ' .''.'"'.$jobD->name.'"'.''.''.' Trong bảng công việc '.''.$job->name,
                ]);
                $event = 'createJobDetail';
                broadcast(new JobListEvent($taskDetail,$jobD,$event))->toOthers();
                return response()->json([
                    'message' => 'Tạo công việc thành công',
                    'data' => $jobD
                ], 200);
            }
        }
        return response()->json([
            'message' => 'Bạn không thuộc thẻ này!!!'
        ],422);
    }

    public function getJobDetail(Request $req){
        $jobD = Job_detail::where('job_id',$req->jobId)->get();
        return response()->json([
            'data' => $jobD
        ],200);
    }
    public function deleteJobDetail(Request $req){
        $jobD = Job_detail::where('id',$req->id)->first();
        $job = Jobs::where('id',$jobD->job_id)->first();
        $taskDetail = Task_details::where('id',$job->task_detail_id)->first();
        $userInCard = $taskDetail->users;
        foreach($userInCard as $u){
            if ($u->id == auth('api')->id()) {
                $event = 'deleteJobDetail';
                broadcast(new JobListEvent($taskDetail,$jobD,$event))->toOthers();
                $jobD->delete();
                TaskDetailHistoryChange::create([
                    'task_detail_id' => $taskDetail->id,
                    'user_change_id' => auth('api')->id(),
                    'content' => 'Đã xoá công việc ' .''.'"'.$jobD->name.'"'.''.''.' Trong bảng công việc '.''.$job->name,
                ]);
                return response()->json([
                    'message' => 'xoá công việc thành công'
                ], 200);
            }
        }
        return response()->json([
            'message' => 'Bạn không thuộc thẻ này!!!'
        ],422);
    }

    public function checkJobDetail(Request $req){
        DB::table('job_details')->where('id',$req->id)->update([
            'check' => $req->check
        ]);
        return response()->json([
            'data' => $req->check
        ],200);
    }
}
