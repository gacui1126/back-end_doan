<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskDetailHistoryChange extends Model
{
    protected $fillable = [
        'task_detail_id','content','user_change_id'
    ];
    public function userChange(){
        return $this->hasOne(User::class,'id','user_change_id');
    }
}
