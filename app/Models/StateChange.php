<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class StateChange extends Model
{

    use Filterable;
    
    protected $fillable = [
        'campa_id',
        'vehicle_id',
        'pending_task_id',
        'group_task_id',
        'sub_state_id',
        'total_time',
        'datetime_finish_sub_state'
    ];

    public function campa(){
        return $this->belongsTo(Campa::class);
    }

    public function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }

    public function groupTask(){
        return $this->belongsTo(GroupTask::class);
    }

    public function subState(){
        return $this->belongsTo(SubState::class);
    }

    public function pendingTask(){
        return $this->belongsTo(PendingTask::class);
    }

}