<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Province;
use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{

    use HasFactory;

    protected $fillable = [
        'company_id',
        'province_id',
        'name',
        'cif',
        'phone',
        'address'
    ];

    public function province(){
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function requests(){
        return $this->hasMany(Request::class, 'customer_id');
    }

    public function scopeByCompany($query, int $companyId){
        return $query->where('company_id', $companyId);
    }
}
