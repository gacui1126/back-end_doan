<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task_detail_user extends Model
{
    protected $fillable = [
        'task_detail_id', 'user_id'
    ];
}
