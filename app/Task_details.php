<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task_details extends Model
{
    protected $fillable = [
        'name', 'user_create_id' , 'task_id','project_id','completed','deadline'
    ];
    public function task_details(){
        return $this->belongsTo(Tasks::class,'id','task_id');
    }
    public function users(){
        return $this->belongsToMany(User::class,'task_detail_user','task_detail_id','user_id');
    }
    public function tags(){
        return $this->belongsToMany(Tags::class,'tag_task_detail','task_detail_id','tag_id');
    }
}
