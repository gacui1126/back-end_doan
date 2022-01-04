<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Permissions;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function get(){
        $permission = Permissions::all();
        return response()->json([
            'data' => $permission
        ],200);
    }

    public function check($permissionName) {
        // $users = auth('api')->user()->getRoleNames();

        // return response()->json([
        //     'data' => $users
        // ],200);
            if (!auth('api')->user()->hasPermissionTo($permissionName)) {
               abort(403);
            }
            return response('', 204);
    }

    public function deletePerUser(Request $req){
        $user = User::where('id',$req->userId)->first();
        $user->revokePermissionTo($req->perName);
        return response()->json([
            'message' => 'Xoá quyền thành công'
        ],200);
    }

    public function create(Request $req){
        // $permission = Permission::create(['name' => $req->name]);
        // $role = Role::find(1);
        // $permission = Permission::find(3);
        // $role->givePermissionTo($permission);

        $user = User::where('id',100)->first();
        $user->hasRole('Admin');
        // auth()->user()->assignRole('Admin');
    }


}
