<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $Table = "permissions";

    public function permissionChildrent(){
        return $this->hasMany(Permissions::class,'parent_id','id');
    }
}
