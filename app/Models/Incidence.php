<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PendingTask;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Incidence extends Model
{

    use HasFactory, Filterable;

    protected $fillable = [
        'description',
        'resolved'
    ];

    public function pending_tasks(){
        return $this->belongsToMany(PendingTask::class);
    }

    public function scopeByIds($query, array $ids){
        return $query->whereIn('id', $ids);
    }

    public function scopeByResolved($query, bool $resolved){
        return $query->where('resolved', $resolved);
    }
}
