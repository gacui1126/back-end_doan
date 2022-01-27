<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Teams;

// use Symfony\Component\HttpFoundation\Request;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;
use Mail;
use Image;
use file;
use App\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth:api')->except('login');
       $this->middleware('permission:list user')->only('getUserA');
       $this->middleware('permission:add user')->only('CreateUser');
       $this->middleware('permission:edit user')->only('updateUser');
       $this->middleware('permission:delete user')->only('deleteUser');
    }

    public function CreateUser(Request $req){
        $array = $req->inputs;
        $this->validate($req,[
        'inputs.*.name' => 'required',
        'inputs.*.email' => 'required|email|unique:users',
        'inputs.*.password' => 'required|min:8|max:20',
        'inputs.*.team' => 'required',
    ],[
        'inputs.*.name.required' => 'Hãy nhập tên',
        'inputs.*.email.required' => 'Hãy nhập email',
        'inputs.*.email.email' => 'Định dạng mail bị sai',
        'inputs.*.email.unique' => 'email đã tồn tại',
        'inputs.*.password.required' => 'Hãy nhập mật khẩu',
        'inputs.*.password.min' => 'Mật khẩu không ít hơn 8 kí tự',
        'inputs.*.password.max' => 'Mật khẩu không vượt quá 20 kí tự',
        'inputs.*.team.required' => 'Hãy chọn team'
    ]);

        for($i = 0; $i < $req->count; $i++){
            $a[$i] = $array[$i];
            $emailCheck = User::where('email',$a[$i]['email'])->first();
            $team_id = Teams::where('name', $a[$i]['team'])->first();
            if($emailCheck){
                return response()->json([
                    'code' => 422,
                    'message' => 'Email đã tồn tại'
                ]);
            }else{
                $user = User::create([
                    'name' => $a[$i]['name'],
                    'email' => $a[$i]['email'],
                    'password' => Hash::make($a[$i]['password']),
                    'team_id' => $team_id->id,
                ]);

                $email = $a[$i]['email'] ?? '';
                $password = $a[$i]['password'] ?? '';
                \Mail::to($email)->send(new \App\Mail\SendMail(['email' => $email,'password'=>$password]));
            }

        }
        return response()->json([
            'code' => 201,
            // 'data' => $a,
            'message' => 'Tạo tài khoản thành công'
        ],201);

    }

    public function login(Request $req){

        $this->validate($req,[
            'email' => 'required|email',
            'password' => 'required|min:8|max:20',
        ],[
            'email.required' => 'Không được để trống email',
            'email.email' => 'Đuôi phải là "@gmail.com" ',
            'password.required' => 'Không được để trống mật khẩu',
            'password.min' => 'Mật khẩu không được ngắn hơn 8 kí tự',
            'password.max' => 'Mật khẩu không được dài hơn 20 kí tự'
        ]);
        if(Auth::guard('web')->attempt([
            'email' => $req->email,
            'password' => $req->password
        ])){
            $user = User::where('email',$req->email)->first();
            $user->token = $user->createToken('login')->accessToken;
            return response()->json([
                'code' => 200,
                'data' => $user,
                'message' => 'Đăng nhập thành công'
            ],200);
        }else{
            return response()->json([
                'code' => 401,
                'message' => 'email hoặc mật khẩu không đúng'
            ],401);
        }
    }

    public function info(Request $req){
        $user = auth('api')->user();
        return response()->json(['data' => $user],200);
    }

    public function getUserA(Request $req){
        // if(auth('api')->user()->hasPermissionTo('list user','api')){
            $userAll = User::orderBy('id','desc')->paginate($req->total);
            foreach($userAll as $u){
                $u->teams;
            }
            return response()->json([
                'data' => $userAll,
            ],200);
        // }else{
        //     return response()->json([
        //         'message' => 'Không có quyền truy cập',
        //     ],403);
        // }
    }

    public function editUSer(Request $req){
        $user = User::where('id',$req->id)->first();
        $user_team = User::find($req->id);
        $team = $user->teams;
        return response()->json([
            'data' => $user
        ],202);
    }
    public function updateUser(Request $req){

        $user = User::where('id',$req->id)->first();
        $user->name = $req->name;
        $user->age = $req->age;
        $user->email = $req->email;
        $user->address = $req->address;
        $user->phone = $req->phone;

        $team_name = $req->teamName;
        $team = Teams::where('name',$team_name)->first();
        $user->team_id = $team->id;

        $user->save();
        return response()->json([
            'message' => 'update user thành công',
            'data' => $user
        ],200);
    }
    public function deleteUser(Request $req){
        $user = User::find($req->id);
        $user->delete();
        return response()->json([
            'message' => 'delete thành công'
        ],202);
    }

    public function view(Request $req){
        $user = User::where('id',$req->id)->first();
        return response()->json([
            'data' => $user
        ],200);
    }
    public function uploadImg(Request $req){
            $imgName = Str::random(40);
            $duoi = pathinfo($req->file('image')->getClientOriginalName(),PATHINFO_EXTENSION); //Lấy đuôi ảnh
            $fileName = $imgName .'.'. $duoi;
            $id = $req->get('id');
            $user = User::where('id',$id)->first();
            $user->img = 'http://127.0.0.1:8080/storage/images/'.$fileName;
            $user->save();

            $req->file('image')->storeAs('images', $fileName, 'public');

            return response()->json([
                'success' => 'Đổi ảnh đại diện thành công',
                'data' => $fileName
            ], 200);
    }
    public function updateUserInfo(Request $req){
        $user = User::where('id',$req->id)->first();
        $user->name = $req->name;
        $user->phone = $req->phone;
        $user->age = $req->age;
        $user->address = $req->address;
        $user->save();
        return response()->json([
            'success' => 'Cập nhật dữ liệu thành công',
        ], 200);
    }
    public function resetPass(Request $req){
        $this->validate($req,[
            'password_old' => 'required|min:8|max:20',
            'password_new' => 'required|min:8|max:20',
            'password_re' => 'required|min:8|max:20',
        ],[
            'password_old.required' => 'Vui lòng nhập mật khẩu',
            'password_old.min' => 'Mật khẩu không được ngắn hơn 8 kí tự" ',
            'password_old.max' => 'Mật khẩu không vượt quá 20 kí tự',
            'password_new.required' => 'Không được để trống mật khẩu',
            'password_new.min' => 'Mật khẩu không được ngắn hơn 8 kí tự',
            'password_new.max' => 'Mật khẩu không được dài hơn 20 kí tự',
            'password_re.required' => 'Không được để trống mật khẩu',
            'password_re.min' => 'Mật khẩu không được ngắn hơn 8 kí tự',
            'password_re.max' => 'Mật khẩu không được dài hơn 20 kí tự',
        ]);

        $user = User::where('id',$req->id)->first();
        $user_password = $user->password;
        // $password_old = Hash::make($req->password_old);
        if(!(Hash::check($req->password_old, $user_password))){
            return response()->json([
                'message' => 'Nhập sai mật khẩu'
            ],403);
        }else{
            if (strcmp($req->password_old, $req->password_new) == 0) {
                return response()->json([
                    'message' => 'Mật khẩu mới trùng với mật khẩu cũ',
                ], 403);
            }else if(strcmp($req->password_new, $req->password_re) == 0){
                $user->password = Hash::make($req->password_new);
                $user->save();
                return response()->json([
                    'message' => 'Thay đổi mật khẩu thành công'
                ], 200);
            }else{
                return response()->json([
                    'message' => 'Mật khẩu nhập lại không khớp'
                ], 403);
            }
        }
    }
    public function all(){
        $user = User::all();
        return response()->json([
            'data' => $user
        ],200);
    }

    public function checkPer(Request $req){
        if(!$req->user){
            return response()->json([
                'message' => 'Vui lòng chọn user cần kiểm tra'
            ],422);
        }else{
            $user = $req->user;
            $checkUser = User::where('id', $user['id'])->first();
            $teams = $checkUser->teams;
            $checkRole = $checkUser->getRoleNames();
            $checkPer  = $checkUser->getPermissionNames();
            return response()->json([
                'user' => $checkUser,
                'role' => $checkRole,
                'permission' => $checkPer,
                'team' => $teams
            ],200);
        }

    }
}
