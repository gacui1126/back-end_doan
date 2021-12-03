<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $fillable = [
        'name', 'end_at', 'start_at','user_create_id'
    ];

    public function task(){
        return $this->hasMany(Tasks::class,'project_id','id');
    }

    public function teams(){
        return $this->belongsToMany(Teams::class,'project_team','project_id','team_id');
    }
    public function users(){
        return $this->belongsToMany(User::class,'project_user','project_id','user_id');
    }
}
