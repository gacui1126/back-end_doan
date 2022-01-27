<?php

namespace App\Http\Controllers\Api;

use App\Events\ChatEvent;
use App\GroupChat;
use App\Http\Controllers\Controller;
use App\Message;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatsController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    public function user(){
        $users = User::where('id','!=',auth('api')->id())->orderby('id','desc')->get();

        $unreadIds = Message::select(DB::raw('`user_id` as receiver_id, count(`user_id`) as message_count'))->where('receiver_id',auth('api')->id())->where('read',false)->groupBy('user_id')->get();

        $users = $users->map(function($user) use ($unreadIds){
            $userUnread = $unreadIds->where('receiver_id', $user->id)->first();
            $user->unread = $userUnread ? $userUnread->message_count : 0;
            return $user;
        });

        return response()->json([
            'data' => $users
        ],200);
    }

    public function fetchMessages(User $user)
    {
        Message::where('user_id',$user->id)->where('receiver_id',auth('api')->user()->id)->update(['read'=>true]);
        $message = Message::with('user')->where(['user_id' => auth('api')->user()->id,'receiver_id' => $user->id])->orWhere(function($query) use($user){
            $query->where(['user_id' => $user->id, 'receiver_id' => auth('api')->user()->id]);
        })->get();
        return response()->json([
            'message' => $message,
            'user' => $user
        ],200);
    }

    public function sendMessage(Request $req, User $user)
    {
        $input=$req->all();
        $input['receiver_id'] = $user['id'];
        $message = auth('api')->user()->messages()->create($input);

        broadcast(new ChatEvent($message->load('user')))->toOthers();
        return response()->json($message);
    }

    public function getMess(){
        $message = Message::where('user_id',auth('api')->id())->orWhere('receiver_id',auth('api')->id())->get();
        foreach($message as $key => $mess){
            $user[] = User::where('id',$mess->receiver_id)->where('id','!=',auth('api')->id())->orWhere('id',$mess->user_id)->first();
            $user[$key]['messageRe'];
            $mess->userReciver;
        }

        foreach($user as $key => $us){
            $messages = Message::where('user_id',$user[$key]['id'])->orWhere('receiver_id',$user[$key]['id'])->get();
            $us['message_re'] = $messages;

            $unreadIds = Message::select(DB::raw('`user_id` as receiver_id, count(`user_id`) as message_count'))->where('receiver_id',auth('api')->id())->where('read',false)->groupBy('user_id')->get();

            $userUnread = $unreadIds->where('receiver_id', $user[$key]['id'])->first();
            $us['unread'] = $userUnread ? $userUnread->message_count : 0;
        }
        $user = array_unique($user);
        return response()->json($user,200);
    }

}
