<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $Table = "roles";

    public function permissions(){
        return $this->belongsToMany(Permissions::class,'permission_role','role_id','permission_id');
    }
}
