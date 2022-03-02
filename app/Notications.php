<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notications extends Model
{
    protected $guarded = [];

    public function user(){
        $this->belongsToMany(User::class,'noti_user','noti_id','user_id');
    }
}
