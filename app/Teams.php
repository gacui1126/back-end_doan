<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    protected $fillable = [
        'name'
    ];

    public function users(){
        return $this->hasMany(User::class,'team_id','id');
    }
}
