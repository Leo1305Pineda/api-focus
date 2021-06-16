<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Task;

class TypeTask extends Model
{

    protected $fillable = [
        'name'
    ];

    public function tasks(){
        return $this->hasMany(Task::class, 'type_task_id');
    }
}
