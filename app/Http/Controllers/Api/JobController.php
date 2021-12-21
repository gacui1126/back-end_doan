<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Job_detail;
use App\Jobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function create(Request $req){
        $job = new Jobs;
        $job->name = $req->name;
        $job->task_detail_id = $req->taskDetailId;
        $job->save();
        $job->job_details = [];
        $job->addJobFormShow = true;
        $job->addJobForm = false;

        return response()->json([
            'message' => 'Tạo danh sách thành công',
            'data' => $job
        ],200);
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
        $job->delete();
        $jobD = Job_detail::where('job_id',$req->id)->delete();
        return response()->json([
            'message' => 'Xoá danh sách thành công'
        ],200);
    }
    public function createJobDetail(Request $req){
        if(!$req->name){
            return response()->json([
                'message' => 'Vui lòng nhập tên công việc'
            ],422);
        }
        $jobD = new Job_detail();
        $jobD->name = $req->name;
        $jobD->job_id = $req->jobId;
        $jobD->save();
        return response()->json([
            'message' => 'Tạo công việc thành công',
            'data' => $jobD
        ],200);
    }

    public function getJobDetail(Request $req){
        $jobD = Job_detail::where('job_id',$req->jobId)->get();
        return response()->json([
            'data' => $jobD
        ],200);
    }
    public function deleteJobDetail(Request $req){
        $jobD = Job_detail::where('id',$req->id)->first();
        $jobD->delete();
        return response()->json([
            'message' => 'xoá công việc thành công'
        ],200);
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
