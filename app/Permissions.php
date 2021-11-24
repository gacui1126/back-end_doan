<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $Table = "permissions";

    public function permissionChildrent(){
        return $this->hasMany(Permission::class,'parent_id');
    }
}
