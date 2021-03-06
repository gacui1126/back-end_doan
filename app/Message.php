<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userReceiver(){
        return $this->belongsTo(User::class,'receiver_id','id');
    }

    public function fromContact(){
        return $this->hasOne(User::class,'id', 'user_id');
    }
}
