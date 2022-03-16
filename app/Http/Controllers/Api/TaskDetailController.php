<?php

namespace App\Http\Controllers\Api;

use App\completeConfirmation;
use App\Events\NoticationEvent;
use App\Events\SendRQCompleteCardEvent;
use App\Http\Controllers\Controller;
use App\Notications;
use App\Projects;
use App\Tags;
use App\Task_details;
use App\TaskDetailHistoryChange;
use App\Tasks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Teams;
use App\User;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class TaskDetailController extends Controller
{
    public function create(Request $req){
        if($req->cardName == ''){
            return response() -> json([
                'message' => 'Vui lòng điền tên card'
            ],422);
        }
        if(!$req->deadline){
            return response() -> json([
                'message' => 'Vui lòng Chọn deadline cho thẻ'
            ],422);
        }
        $deadline = date('Y-m-d H:i:s', strtotime($req->deadline));
        $now = date('Y-m-d H:i:s', strtotime(Carbon::now('Asia/Ho_Chi_Minh')));
        $date = Carbon::parse($deadline);
        $project = Projects::where('id',$req->project_id)->first();
        $deadlineProject = Carbon::parse($project->end_at);
        if(auth('api')->id() == $project->user_create_id){
            if($date > $now){
                if($date < $deadlineProject){
                    $card = Task_details::create([
                        'name' => $req->cardName,
                        'user_create_id' => $req->userId,
                        'task_id' => $req->taskId,
                        'project_id' => $req->project_id,
                        'deadline' => $deadline,
                        'completed' => 0
                    ]);
                    foreach($req->user as $u){
                        DB::table('task_detail_user')->insert([
                            'task_detail_id' => $card->id,
                            'user_id' => $u['id']
                        ]);
                    }
                    return response() -> json([
                        'message' => 'Tạo thẻ thành công',
                        'data' => $card
                    ],200);
                }else{
                    return response() -> json([
                        'message' => 'Deadline bạn chọn lớn hơn deadline của dự án vui lòng chọn lại',
                    ],422);
                }
            }else{
                return response() -> json([
                    'message' => 'Vui lòng chọn deadline lớn hơn thời điểm hiện tại',
                ],422);
            }
        }
        return response()->json([
            'message' => 'Bạn không có quyền này',
        ],422);
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
        $task = Task_details::where('id',$req->id)->first();
        $user = $req->user;
        if(!$req->user){
            return response()->json([
                'message' => 'Vui lòng chọn thành viên muốn thêm vào'
            ],422);
        }else{
            if(auth('api')->user()->hasAnyRole(['Admin','manager']) || auth('api')->id() == $task->user_create_id){
                foreach($user as $u){
                    $checkUser = DB::table('task_detail_user')->whereTask_detail_idAndUser_id($req->id,$u['id'])->first();
                    if($checkUser){
                        return response()->json([
                            'message' => 'Tồn tại thành viên đã có trong thẻ'
                        ],400);
                    }
                    TaskDetailHistoryChange::create([
                        'task_detail_id' => $req->id,
                        'user_change_id' => auth('api')->id(),
                        'content' => 'Đã thêm ' .''.$u['name'].''. ' vào thẻ',
                    ]);

                    DB::table('task_detail_user')->insert([
                        'task_detail_id' => $req->id,
                        'user_id' => $u['id']
                    ]);
                    $notication = Notications::create([
                        'content' => 'Bạn vừa được thêm vào thẻ : ' .''. $task->name,
                    ]);
                    DB::table('noti_user')->insert([
                        'user_id' => $u['id'],
                        'noti_id' => $notication->id,
                    ]);
                    $userChannel = User::where('id',$u['id'])->first();
                    broadcast(new NoticationEvent($notication,$userChannel));
                }
                return response()->json([
                    'message' => 'Thêm thành viên thành công',
                    'data' => $user
                ],200);
            }else{
                return response()->json([
                    'message' => 'Bạn không có quyền sử dụng module này'
                ],422);
            }
        }

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
        $task = Task_details::where('id',$req->taskId)->first();
        if(auth('api')->user()->hasAnyRole(['Admin','manager']) || auth('api')->id() == $task->user_create_id){
            $user = DB::table('task_detail_user')->whereTask_detail_idAndUser_id($req->taskId,$req->userId)->delete();
            $u = User::where('id',$req->userId)->first();
            TaskDetailHistoryChange::create([
                'task_detail_id' => $req->taskId,
                'user_change_id' => auth('api')->id(),
                'content' => 'Đã xoá ' .''.$u->name.''. ' khỏi thẻ',
            ]);

            return response()->json([
                'message' => 'Xoá user khỏi thẻ thành công'
            ],200);
        }else{
            return response()->json([
                'message' => 'Bạn không có quyền này'
            ],422);
        }
    }

    public function setDeadline(Request $req){
        $deadline = date('Y-m-d H:i:s', strtotime($req->deadline));
        $taskDetail = Task_details::where('id',$req->taskDetailId)->first();
        $taskDetail->deadline = $deadline;
        $taskDetail->completed = 0;
        $taskDetail->update();
        $now = date('Y-m-d H:i:s', strtotime(Carbon::now('Asia/Ho_Chi_Minh')));
        $date = Carbon::parse($deadline);
        if(auth('api')->user()->hasAnyRole(['Admin','manager']) || auth('api')->id() == $taskDetail->user_create_id){
            if($date > $now){
                $diff = $date->diffInMinutes($now);
                TaskDetailHistoryChange::create([
                    'task_detail_id' => $req->taskDetailId,
                    'user_change_id' => auth('api')->id(),
                    'content' => 'Đã thay đổi deadline',
                ]);
                return response()->json([
                    'message' => 'Chọn thời gian deadline thành công',
                    'data' => $deadline,
                    'dayDeadline' => $diff,
                ],200);
            }else{
                return response()->json([
                    'message' => 'Chọn thời gian deadline thành công',
                    'data' => $deadline,
                    'dayDeadline' => 0,
                ],200);
            }

        }else{
            return response()->json([
                'message' => 'Bạn không có quyền thay đổi deadline của thẻ'
            ],422);
        }
    }
    public function getDeadline(Request $req){
        $taskDetail = Task_details::where('id',$req->taskDetailId)->first();
        // $now = Carbon::now('Asia/Ho_Chi_Minh');
        $now = date('Y-m-d H:i:s', strtotime(Carbon::now('Asia/Ho_Chi_Minh')));
        $date = Carbon::parse($taskDetail->deadline);
        if($date > $now){
            $diff = $date->diffInMinutes($now);

            return response()->json([
                'data' => $taskDetail->deadline,
                'dayDeadline' => $diff,
            ],200);
        }else{
            return response()->json([
                'data' => $taskDetail->deadline,
                'dayDeadline' => 0,
            ],200);
        }

    }
    public function deleteDeadline(Request $req){
        DB::table('task_details')->where('id',$req->taskDetailId)->update([
            'deadline' => null,
            'completed' => 0
        ]);
        return response()->json([
            'message' => 'Xoá deadline thành công',
            'data' => null
        ],200);
    }
    public function completed(Request $req){
        DB::table('task_details')->where('id',$req->taskDetailId)->update([
            'completed' => $req->checkDate
        ]);
        return response()->json([
            'data' => $req->checkDate
        ],200);
    }
    public function getCompleted(Request $req){
        $taskDetail = Task_details::where('id',$req->taskDetailId)->first();
        return response()->json([
            'data' => $taskDetail->completed
        ],200);
    }
    public function delete(Request $req){
        $taskDetail = Task_details::where('id',$req->id)->first();
        if(auth('api')->id() == $taskDetail->user_create_id){
            $taskDetail->delete();
            return response()->json([
                'message' => ' xoá thành công',
                'data' => $taskDetail
            ],200);
        }
        return response()->json([
            'message' => 'Bạn không có quyền này'
        ],422);
    }

    public function taskForMe(){
        $user = auth('api')->user();
        $card = $user->taskDetail;
        foreach($card as $c){
            $pro = $c->projects;
            $now = date('Y-m-d H:i:s', strtotime(Carbon::now('Asia/Ho_Chi_Minh')));
            $date = Carbon::parse($c->deadline);
            if($date > $now){
                $diff = $date->diffInDays($now);
                $c->remainingTime = $diff;
            }else{
                $c->remainingTime = 0;
            }
        }
        return response()->json([
            'data' => $card,
        ],200);
    }
    public function CompleteConfi(Request $req){
        $senderId = auth('api')->id();
        $taskDetail = Task_details::where('id',$req->id)->first();
        $userInCard = $taskDetail->users;
        $complete = completeConfirmation::where('task_detail_id',$taskDetail->id)->first();
        foreach($userInCard as $u){
            if($u->id == $senderId){
                if(!$complete && $taskDetail->completed == 0){
                    completeConfirmation::create([
                        'sender_id' => $senderId,
                        'receiver_id' => $taskDetail->user_create_id,
                        'task_detail_id' => $taskDetail->id,
                    ]);
                    $userChannel = User::where('id',$taskDetail->user_create_id)->first();
                    $notication = 'Bạn có yêu cầu xác nhận công việc, vui lòng kiểm tra';
                    broadcast(new SendRQCompleteCardEvent($notication,$userChannel));
                    return response()->json([
                        'msg' => 'Đã gửi xác nhận cho người quản lý dự án'
                    ],200);
                }else{
                    if($taskDetail->completed == 1){
                        return response()->json([
                            'msg' => 'Thẻ này đã được xác nhận hoàn thành trước đó'
                        ],422);
                    }
                    return response()->json([
                        'msg' => 'Yêu cầu đã được gửi trước đó, vui lòng đợi phản hồi.'
                    ],422);
                }
            }
        }

        return response()->json([
            'msg' => 'Bạn không thuộc thẻ này, không thực hiện thao tác này được'
        ],422);
    }
    public function getReComplete(Request $req){
        $receiverId = auth('api')->id();
        $complete = completeConfirmation::where('receiver_id',$receiverId)->get();
        foreach($complete as $co){
            $co->task_detail = Task_details::where('id',$co->task_detail_id)->first();
            $co->task_detail->projects;
            $co->sender = User::where('id',$co->sender_id)->first();
        }
        return response()->json([
            'data' => $complete
        ]);
    }
    public function detroyRE(Request $req){
        $complete = completeConfirmation::where('task_detail_id',$req->id)->delete();
        return response()->json([
            'msg' => 'Huỷ yêu cầu thành công'
        ],200);
    }

    public function managerCompleteConfir(Request $req){
        DB::table('task_details')->where('id',$req->id)->update([
            'completed' => true
        ]);

        DB::table('complete_confirmations')->where('task_detail_id',$req->id)->delete();
        return response()->json([
            'msg' => 'Xác nhận thành công'
        ],200);
    }

    public function historyChange(Request $req){
        $history = TaskDetailHistoryChange::where('task_detail_id',$req->id)->get();
        foreach($history as $h){
            $h->userChange;
        }
        return response()->json([
            'data' => $history
        ],200);
    }

    public function getAllData(Request $req){
        $taskDetail = Task_details::where('id',$req->taskDetailId)->first();
        $tag = $taskDetail->tags;
        $job = $taskDetail->jobs;
        $file = $taskDetail->files;
        return response()->json([
            'tag' => $tag,
            'job' =>$job,
            'file' => $file
        ],200);
    }
}
