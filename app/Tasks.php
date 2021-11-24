<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected $Table = "tasks";

    public function users(){
        return $this->belongsToMany(User::class,'task_user','task_id','user_id');
    }

    public function projects(){
        return $this->belongsTo(Projects::class,'id','project_id');
    }

    public function task_completed(){
        return $this->belongsTo(task_completed::class,'task_id','id');
    }
}
