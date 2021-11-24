<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject= "Tài khoản đăng nhập hệ thống quản lý dự án của bạn là";
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $data = this->data;
        return $this->subject($this->subject)->replyTo('ntson@gmail.com','Sơn Nguyễn')->view('send_emails',[
           'email' => $this->data['email'],
           'password' => $this->data['password']
        ]);
    }
}
