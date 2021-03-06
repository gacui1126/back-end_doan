<?php

namespace App\Http\Controllers\Api;

use App\Events\TagEvent;
use App\Events\TaskDetailEvent;
use App\Http\Controllers\Controller;
use App\Tags;
use App\Task_detail_user;
use App\Task_details;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function create(Request $req){
        $tag = Tags::create([
            'color' => $req->tagColor
        ]);
        DB::table('tag_user')->insert([
            'tag_id' => $tag->id,
            'user_id' => $req->userId
        ]);
        $tag->tagColorF = true;
        $tag->check = false;
        return response()->json([
            'message' => 'Tạo task thành công',
            'data' => $tag
        ],200);
    }
    public function getTagUser(Request $req){
        $user = User::where('id',$req->userId)->first();
        $tagUser = $user->tags;
        foreach($tagUser as $t){
            $t->tagColorF = true;
            $t->check = false;
        }
        return response()->json([
            'data' => $tagUser
        ],200);
    }
    public function addTagTask(Request $req){
        $userId = auth('api')->id();
        $taskDetail = Task_details::where('id',$req->taskDetailId)->first();
        $userInCard = $taskDetail->users;
        $checkTag = DB::table('tag_task_detail')->whereTag_idAndTask_detail_id($req->tagId,$req->taskDetailId)->first();
        $tag = Tags::where('id',$req->tagId)->first();
        foreach($userInCard as $u){
            if($u->id == $userId){
                if($checkTag){
                    DB::table('tag_task_detail')->whereTag_idAndTask_detail_id($req->tagId,$req->taskDetailId)->delete();
                    broadcast(new TagEvent($taskDetail,$tag))->toOthers();
                    return response()->json([
                        'message' => 'Xoá tag khỏi thẻ'
                    ],200);
                }else{
                    DB::table('tag_task_detail')->insert([
                        'tag_id' => $req->tagId,
                        'task_detail_id' => $req->taskDetailId
                    ]);
                    broadcast(new TagEvent($taskDetail,$tag))->toOthers();
                    return response()->json([
                        'message' => 'Add tag vào thẻ thành công'
                    ], 200);
                }
            }
        }
        return response()->json([
            'message' => 'Bạn không thuộc thẻ này!!! không thể thực hiện thao tác này'
        ],422);
    }
    public function getTagTaskDetail(Request $req){
        $taskDetail = Task_details::where('id',$req->taskDetailId)->first();
        return response()->json([
            'data' => $taskDetail->tags
        ],200);
    }
    public function editTag(Request $req){
        $tags = Tags::where('id',$req->id)->first();
        return response()->json([
            'data'=>$tags
        ],200);
    }

    public function update(Request $req){
        $tag = Tags::where('id',$req->id)->first();
        $tag->color = $req->color;
        $tag->update();
        return response()->json([
            'message' => 'Update nhãn thành công'
        ]);
    }
}
