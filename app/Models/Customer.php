<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Province;
use App\Models\Request;

class Customer extends Model
{
    public function province(){
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function company(){
        return $this->belongsTo(Customer::class, 'company_id');
    }

    public function requests(){
        return $this->hasMany(Request::class, 'customer_id');
    }
}
