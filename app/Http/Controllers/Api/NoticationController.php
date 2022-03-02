<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoticationController extends Controller
{
    public function getNoti(){
        $user = auth('api')->user();
        $noti = $user->notications;
        $i = 0;
        foreach($noti as $n){
            if($n->read == false){
                $i++;
            }
        }
        return response()->json([
            'data' => $noti,
            'countNoti' => $i,
        ],200);
    }
    public function selectnoti(){
        $user = auth('api')->user();
        $noti = $user->notications;
        // $noti = Notications::all();
        foreach ($noti as $n) {
            // $n = new Notications();
            $n->read = 1;
            $n->update();
        }
        return response($noti);
    }
}
