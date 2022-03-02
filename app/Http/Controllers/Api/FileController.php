<?php

namespace App\Http\Controllers\Api;

use App\Events\FileEvent;
use App\Events\TaskDetailEvent;
use App\File;
use App\Http\Controllers\Controller;
use App\Task_details;
use App\TaskDetailHistoryChange;
use Illuminate\Http\Request;


class FileController extends Controller
{
    public function upload(Request $request){
        if(!$request->name){
            return response()->json([
                'message' => 'vui lòng nhập tên tệp'
            ],422);
        }
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,csv,txt,xlx,xls,pdf,docx,sql,dmg|max:2048'
        ]);

        $taskDetail = Task_details::where('id',$request->task_detail_id)->first();
        $userInCard = $taskDetail->users;
        foreach ($userInCard as $u) {
            if ($u->id == auth('api')->id()) {
                $fileUpload = new File();

                if ($request->file()) {
                    $file_name = time().'_'.$request->file->getClientOriginalName();
                    $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');

                    $fileUpload->auth_name = time().'_'.$request->file->getClientOriginalName();
                    $fileUpload->path = '/storage/' . $file_path;
                    $fileUpload->task_detail_id = $request->task_detail_id;
                    $fileUpload->name = $request->name;
                    $fileUpload->save();

                    TaskDetailHistoryChange::create([
                        'task_detail_id' => $taskDetail->id,
                        'user_change_id' => auth('api')->id(),
                        'content' => 'Đã thêm tệp '.''.'"'.$fileUpload->name.'"'.' vào thẻ',
                    ]);
                    $event = 'create';
                    broadcast(new FileEvent($taskDetail,$fileUpload,$event))->toOthers();

                    return response()->json([
                        'message'=>'Ghim tệp thành công.',
                        'data' => $fileUpload
                    ]);
                }
            }
        }
        return response()->json([
            'message' => 'Bạn không thuộc thẻ này',
        ],422);
    }
    public function GetFile(Request $req){
        $file = File::where('task_detail_id',$req->id)->get();
        return response()->json([
                'data' => $file
        ],200);
    }
    public function downloadFile(Request $req){
        // $file = public_path() . "/storage/uploads/1643899453_2016-120160261-NguyenTruongSon.pdf";
        $file = File::where('id',$req->id)->first();
        $filePath = public_path() . $file->path;

        $headers = [
            'Content-Type' => 'application/pdf',
        ];
        return response()->download($filePath, $file->name, $headers);
    }
    public function delete(Request $req){
        $file = File::where('id',$req->id)->first();
        $taskDetail = Task_details::where('id',$file->task_detail_id)->first();
        $userInCard = $taskDetail->users;
        foreach ($userInCard as $u) {
            if ($u->id == auth('api')->id()) {
                $event = 'delete';
                broadcast(new FileEvent($taskDetail,$file,$event))->toOthers();
                $file->delete();
                TaskDetailHistoryChange::create([
                    'task_detail_id' => $taskDetail->id,
                    'user_change_id' => auth('api')->id(),
                    'content' => 'Đã Xoá tệp '.''.'"'.$file->name.'"'.' khỏi thẻ',
                ]);

                return response()->json([
                    'msg' => 'Xoá file thành công'
                ], 200);
            }
        }
        return response()->json([
            'message' => 'Bạn không thuộc thẻ này!!!'
        ],422);
    }
    public function getAll(Request $req){
        $file = File::where('task_detail_id',$req->id)->get;
        return response()->json([
            'data' => $file
        ],200);
    }
    public function update(Request $req){
        $req->validate([
            'name' => 'required',
            'file' => 'required|mimes:jpg,jpeg,png,csv,txt,xlx,xls,pdf,docx,sql,dmg|max:2048'
        ],[
            'name.required' => 'Vui lòng nhập tên tài liệu bạn muốn ghim lên'
        ]);
        $fileUpload = File::where('id',$req->id)->first();
        $taskDetail = Task_details::where('id',$req->task_detail_id)->first();
        if($req->file()) {
            $file_name = time().'_'.$req->file->getClientOriginalName();
            $file_path = $req->file('file')->storeAs('uploads', $file_name, 'public');

            $fileUpload->auth_name = time().'_'.$req->file->getClientOriginalName();
            $fileUpload->path = '/storage/' . $file_path;
            $fileUpload->name = $req->name;
            $fileUpload->update();

            TaskDetailHistoryChange::create([
                'task_detail_id' => $fileUpload->task_detail_id,
                'user_change_id' => auth('api')->id(),
                'content' => 'Đã sửa tệp '.''.$fileUpload->name,
            ]);
            return response()->json([
                'success'=>'File uploaded successfully.',
                'data' => $fileUpload
            ]);
        }
    }
}
