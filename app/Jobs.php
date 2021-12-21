<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    protected $fillable = [
        'name','task_detail_id'
    ];
    public function job_details(){
        return $this->hasMany(job_detail::class,'job_id','id');
    }
}
