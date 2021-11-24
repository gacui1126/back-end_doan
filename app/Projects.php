<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $Table = "projects";

    public function task(){
        return $this->hasMany(Tasks::class,'project_id','id');
    }

    public function user(){
        return $this->belongsToMany(User::class,'project_user','project_id','user_id');
    }
}
