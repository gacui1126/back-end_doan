<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task_details extends Model
{
    protected $fillable = [
        'name', 'user_create_id' , 'task_id','project_id','completed','deadline'
    ];
    public function tasks(){
        return $this->belongsTo(Tasks::class,'id','task_id');
    }
    public function users(){
        return $this->belongsToMany(User::class,'task_detail_user','task_detail_id','user_id');
    }
    public function tags(){
        return $this->belongsToMany(Tags::class,'tag_task_detail','task_detail_id','tag_id');
    }
    public function comments(){
        return $this->hasMany(Comments::class,'task_detail_id','id');
    }
    public function jobs(){
        return $this->hasMany(jobs::class,'task_detail_id','id');
    }
    public function files(){
        return $this->hasMany(File::class,'task_detail_id','id');
    }
    public function projects(){
        return $this->belongsto(Projects::class,'project_id','id');
    }
}
