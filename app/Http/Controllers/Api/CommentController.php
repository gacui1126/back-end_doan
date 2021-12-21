<?php

namespace App\Http\Controllers\Api;

use App\Comments;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function create(Request $req){
        if($req->comment){
            $comment = new Comments;
            $comment->user_id = $req->userId;
            $comment->task_detail_id = $req->taskDetailId;
            $comment->content = $req->comment;
            // $comment->parent_id = 1;
            $comment->save();
            $comment->user;
            $comment->editComment = false;
            $comment->showComment = true;
            $comment->showRepply = false;
            $re = $comment->replies;
            // $re->Edit = false;
            // $re->OEdit = true;
            return response()->json([
                'message' => 'Thêm bình luận thành công',
                'data' => $comment
            ],200);
        }else{
            return response()->json([
                'message' => 'Vui lòng nhập bình luận'
            ],422);
        }
    }
    public function get(Request $req){
        $comment = Comments::where('task_detail_id',$req->id)->get();
        foreach($comment as $c){
            $c->user;
            $c->editComment = false;
            $c->showComment = true;
            $c->showRepply = false;
            $reply = $c->replies;
            foreach($reply as $re){
                $user = User::where('id',$re->user_id)->first();
                $re->user = $user;
                $re->Edit = false;
                $re->OEdit = true;
            }
        }
        return response()->json([
            'data' => $comment
        ],200);
    }

    public function delete(Request $req){
        $comment = Comments::where('id',$req->id)->delete();
        return response()->json([
            'message' => 'Xoá dữ liệu thành công'
        ],200);
    }
    public function update(Request $req){
        if($req->comment){
            $comment = Comments::where('id',$req->id)->first();
            $comment->content = $req->comment;
            $comment->update();
            return response()->json([
                'message' => 'chỉnh sửa comment thành công'
            ],200);
        }else{
            return response()->json([
                'message' => 'Vui lòng không để trống'
            ],422);
        }
    }

    public function repply(Request $req){
        if($req->comment){
            $comment = new Comments;
            $comment->user_id = $req->userId;
            $comment->task_detail_id = $req->taskDetailId;
            $comment->content = $req->comment;
            $comment->parent_id = $req->parent_id;
            $comment->save();
            $comment->user;
            $comment->editComment = false;
            $comment->showComment = true;
            $comment->showRepply = false;
            $comment->Edit = false;
            $comment->OEdit = true;
            return response()->json([
                'message' => 'Thêm bình luận thành công',
                'data' => $comment
            ],200);
        }else{
            return response()->json([
                'message' => 'Vui lòng nhập bình luận'
            ],422);
        }
    }

    public function deleteReply(Request $req){
        $comment = Comments::where('id',$req->id)->delete();
        return response()->json([
            'data' => $comment
        ],200);
    }

    public function updateReply(Request $req){
        if($req->comment){
            $comment = Comments::where('id',$req->id)->first();
            $comment->content = $req->comment;
            $comment->update();
            return response()->json([
                'message' => 'chỉnh sửa comment thành công'
            ],200);
        }else{
            return response()->json([
                'message' => 'Vui lòng không để trống'
            ],422);
        }
    }
}
