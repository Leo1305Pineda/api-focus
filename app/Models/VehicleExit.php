<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleExit extends Model
{
    use HasFactory, Filterable;

    protected $fillable = [
        'vehicle_id',
        'pending_task_id',
        'delivery_by',
        'delivery_to',
        'name_place',
        'is_rolling',
        'date_delivery'
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }

    public function pendingTask(){
        return $this->belongsTo(PendingTask::class);
    }
}
