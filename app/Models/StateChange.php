<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StateChange extends Model
{
    
    protected $fillable = [
        'vehicle_id',
        'group_task_id',
        'sub_state_id',
        'datetime_finish_sub_state'
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }

    public function groupTask(){
        return $this->belongsTo(GroupTask::class);
    }

    public function subState(){
        return $this->belongsTo(SubState::class);
    }

}
