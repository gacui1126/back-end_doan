<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    protected $fillable = [
        'color'
    ];

    public function users(){
        return $this->belongsToMany(User::class,'tag_user','tag_id','user_id');
    }
    public function tasks(){
        return $this->belongsToMany(User::class,'tag_task','tag_id','task_id');
    }

}
