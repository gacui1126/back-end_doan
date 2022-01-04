<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $fillable = [
        'name', 'guard_name'
    ];

    public function permissions(){
        return $this->belongsToMany(Permissions::class,'role_has_permissions','role_id','permission_id');
    }
}
