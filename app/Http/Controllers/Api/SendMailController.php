<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Mail;

class SendMailController extends Controller
{
    public function send_mail(Request $request) {
        $data = $request->all();
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        \Mail::to($email)->send(new \App\Mail\SendMail(['email' => $email,'password'=>$password]));
     }
}
