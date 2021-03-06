<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Roles;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api')->except('login');
        $this->middleware('permission:list role')->only('get');
        $this->middleware('permission:add role')->only('create');
        $this->middleware('permission:edit role')->only('update');
        $this->middleware('permission:delete role')->only('delete');
    }
    public function get(Request $req){
        $role = Roles::orderBy('id','desc')->paginate($req->total);
        foreach($role as $r){
            $r->permissions;
        }
        return response()->json([
            'data' => $role
        ],200);
    }

    public function create(Request $req){
        $role = Roles::create([
            'name' => $req->name,
            'guard_name' => 'api'
        ]);

        $permission = $req->permission;
        foreach($permission as $per){
            DB::table('role_has_permissions')->insert([
                'permission_id' => $per['id'],
                'role_id' => $role->id
            ]);
        }
        return response()->json([
            'data' => $permission
        ],200);
    }
    public function edit(Request $req){
        $role = Roles::where('id',$req->id)->first();
        $role->permissions;
        return response()->json([
            'data' => $role
        ],200);
    }
    public function update(Request $req){
        $role = Roles::where('id',$req->id)->first();
        $role->name = $req->name;
        $role->guard_name = 'api';
        $role->update();

        DB::table('role_has_permissions')->where('role_id',$req->id)->delete();

        $permission = $req->permission;
        foreach($permission as $per){
            DB::table('role_has_permissions')->insert([
                'permission_id' => $per['id'],
                'role_id' => $role->id
            ]);
        }

        return response()->json([
            'message' => 'Update th??nh c??ng',
            'data' => $permission
        ],200);
    }

    public function delete(Request $req){
        Roles::where('id', $req->id)->delete();
        DB::table('role_has_permissions')->where('role_id',$req->id)->delete();

        return response()->json([
            'message' => 'delete th??nh c??ng'
        ],200);
    }

    public function all(){
        $role = Roles::all();
        return response()->json([
            'data' => $role
        ],200);
    }

    public function userPer(Request $req){
        if(!$req->user){
            return response()->json([
                'message' => 'Vui l??ng ch???n user c???n ph??n quy???n'
            ],422);
        }else{
            $user = $req->user;
            $userP = User::where('id',$user['id'])->first();
            if(!$req->role){
                if(!$req->permission){
                    return response()->json([
                        'message' => 'Vui l??ng ch???n quy???n ho???c vai tr??'
                    ],422);
                }else{
                    foreach($req->permission as $per){
                        $userP->givePermissionTo($per['name']);
                    }
                    return response()->json([
                        'message' => 'C???p quy???n cho user th??nh c??ng'
                    ],200);
                }
            }else{
                if(!$req->permission){
                    foreach($req->role as $role){
                        $userP->assignRole($role['name']);
                    }
                    return response()->json([
                        'message' => 'C???p quy???n cho user th??nh c??ng'
                    ],200);
                }else{
                    foreach($req->permission as $per){
                        $userP->givePermissionTo($per['name']);
                    }
                    foreach($req->role as $role){
                        $userP->assignRole($role['name']);
                    }
                    return response()->json([
                        'message' => 'C???p quy???n cho user th??nh c??ng'
                    ],200);
                }
            }
        }
    }
    public function deleteRoleUser(Request $req){
        $user = User::where('id',$req->userId)->first();
        // $role = $req->
        $user->removeRole($req->roleName);
        return response()->json([
            'message' => 'Xo?? quy???n kh???i user th??nh c??ng'
        ],200);
    }

    public function checkRole(){
        $user = auth('api')->user();
        $roles = $user->getRoleNames();
        return response()->json([
            'data' => $roles
        ]);
    }
}
