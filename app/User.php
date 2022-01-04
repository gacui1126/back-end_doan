<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','age','team_id','address','img'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function roles(){
    //     return $this->belongsToMany(Roles::class,'role_user','user_id','role_id');
    // }

    public function comments(){
        return $this->hasMany(Comments::class, 'user_id', 'id');
    }

    public function teams(){
        return $this->belongsTo(Teams::class,'team_id','id');
    }

    public function tasks(){
        return $this->belongsToMany(Tasks::class,'task_user','user_id','task_id');
    }
    public function projects(){
        return $this->belongsToMany(Projects::class,'project_user','user_id','project_id');
    }
    public function tags(){
        return $this->belongsToMany(Tags::class,'tag_user','user_id','tag_id');
    }
}
