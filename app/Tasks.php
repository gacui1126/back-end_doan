<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    protected $fillable = [
        'name', 'user_id' , 'project_id'
    ];

    public function users(){
        return $this->belongsToMany(User::class,'task_user','task_id','user_id');
    }

    public function projects(){
        return $this->belongsTo(Projects::class,'id','project_id');
    }

    public function task_completed(){
        return $this->belongsTo(task_completed::class,'task_id','id');
    }
    public function task_details(){
        return $this->hasMany(Task_details::class,'task_id','id');
    }
}
