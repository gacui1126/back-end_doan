<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    protected $fillable = [
        'name'
    ];

    protected $guard_name = 'api';

    public function users(){
        return $this->hasMany(User::class,'team_id','id');
    }
    public function projects(){
        return $this->belongsToMany(Projects::class,'project_team','team_id','project_id');
    }
}
