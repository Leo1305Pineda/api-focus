<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{

    use HasFactory;

    protected $fillable = [
        'brand_id',
        'name'
    ];

    public function vehicles(){
        return $this->hasMany(Vehicle::class);
    }

    public function brand(){
        return $this->belongsTo(Brand::class);
    }
}
