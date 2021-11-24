<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Teams;
use Illuminate\Http\Request;
use App\User;

class TeamController extends Controller
{
    public function create(Request $req){
        $checkTeam = Teams::where('name', $req->name)->first();
        if($checkTeam){
            return response()->json([
                'code' => 404,
                'message' => 'Tên team đã tồn tại, vui lòng nhập tên khác'
            ],404);
        }else{
            $team = Teams::create([
                'name' => $req->name
            ]);
            return response()->json([
                'code' => 200,
                'message' => 'Tạo tành công'
            ],200);
        }
    }
    public function show(Request $req){
        $showTeam = Teams::orderBy('id','desc')->paginate($req->total);
        return response()->json([
            'code' => 200,
            'data' => $showTeam
        ],200);
    }
    public function delete(Request $req){
        $team = Teams::find($req->id);
        $team->delete();
        return response()->json([
            'code' => 202,
            'data' => $team,
        ], 202);
    }
    public function edit(Request $req){
        $data = Teams::where('id',$req->id)->first();
        return response()->json([
            'code' => 200,
            'data' => $data,
        ], 200);
    }
    public function update(Request $req){
        $team = Teams::find($req->id);
        $team->name = $req->name;
        $team->save();
        return response()->json([
            'code' => 200,
            'data' => $team,
        ], 200);
    }
    public function teamUser(Request $req){
        $team = Teams::find($req->id);
        $userdata = $team->users;
        return response()->json([
            'code' => 200,
            'data' => $userdata,
        ], 200);
    }
    public function changeTeam(Request $req){
        $user = User::where('id',$req->id)->first();
        $team = Teams::where('name',$req->name)->first();
        $user->team_id = $team->id;
        $user->save();
        return response()->json([
            'message' => 'Thay đổi thành công'
        ],201);
    }
}