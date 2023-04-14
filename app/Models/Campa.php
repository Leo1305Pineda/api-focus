<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Province;
use App\Models\Company;
use App\Models\Vehicle;
use App\Traits\CoincidenceFilterTrait;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Campa
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Campa model",
 *     description="Campa model",
 * )
 */

class Campa extends Model
{

    /**
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     format="int64",
     *     description="ID",
     *     title="ID",
     * )
     *
     * @OA\Property(
     *     property="company_id",
     *     type="integer",
     *     format="int64",
     *     description="Company ID",
     *     title="Company ID",
     * )
     *
     * @OA\Property(
     *     property="province_id",
     *     type="integer",
     *     format="int64",
     *     description="Province ID",
     *     title="Province ID",
     * )
     *
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     description="Name",
     *     title="Name",
     * )
     *
     * @OA\Property(
     *     property="location",
     *     type="string",
     *     description="Location",
     *     title="location",
     * )
     *
     * @OA\Property(
     *     property="address",
     *     type="string",
     *     description="Address",
     *     title="Address",
     * )
     *
     * @OA\Property(
     *     property="active",
     *     type="boolean",
     *     description="Status",
     *     title="Active",
     * )
     *
     * @OA\Property(
     *     property="ocupation",
     *     type="integer",
     *     format="int64",
     *     description="Ocupation",
     *     title="Ocupation",
     * )
     *
     * @OA\Property(
     *     property="created_at",
     *     type="string",
     *     format="date-time",
     *     description="When was created",
     *     title="Created at",
     * )
     *
     * @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     format="date-time",
     *     description="When was last updated",
     *     title="Updated at",
     * )
     */

    use HasFactory, Filterable, CoincidenceFilterTrait, SoftDeletes;

    const ROCIAUTO = 1;
    const VIAS_PALANTE = 2;
    const LEGANES = 3;
    const MYR_CORUÑA = 4;
    const AUTOSELEC = 5;
    const DEKRA_VALENCIA = 6;
    const MOTORDOS_LAS_PALMAS = 7;
    const CAMPA_LOIU = 8;
    const TOQUERO_MALLORCA = 9;
    const OSKIALIA = 10;
    const DELOTSER = 11;
    const TALAVERON = 12;
    const DELOTSER_MADRID = 13;
    const PRO_MOTOR_OLIVA = 14;
    const MYR_SANTIAGO = 15;
    const MOTORDOS_TENERIFE = 16;
    const DEKRA_ZARAGOZA = 17;
    const PULLMANCAR = 18;
    const TOQUERO_SAN_FERNANDO = 19;
    const TOQUERO_SOTO = 20;
    const VALLADOLID = 21;

    protected $fillable = [
        'company_id',
        'province_id',
        'name',
        'location',
        'address',
        'active',
        'ocupation'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function users(){
        return $this->belongsToMany(User::class);
    }

    public function campaTypeModelOrders(){
        return $this->hasMany(CampaTypeModelOrder::class);
    }

    public function province(){
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function company(){
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function vehicles(){
        return $this->hasMany(Vehicle::class, 'campa_id');
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function receptions(){
        return $this->hasMany(Reception::class);
    }

    public function zones(){
        return $this->hasMany(Zone::class);
    }

    public function scopeByCompany($query, int $companyId){
        return $query->where('company_id', $companyId);
    }

    public function scopeByCompanies($query, array $ids){
        return $query->whereIn('company_id', $ids);
    }

    public function scopeByProvince($query, int $provinceId){
        return $query->where('province_id', $provinceId);
    }

    public function scopeByIds($query, array $ids){
        return $query->whereIn('id', $ids);
    }

    public function scopeByName($query, $name){
        return $query->where('name','like',"%$name%");
    }

    public function scopeByProvinces($query, array $ids){
        return $query->whereIn('province_id', $ids);
    }

    public function scopeByRegion($query, int $regionId){
        return $query->whereHas('province', function (Builder $builder) use($regionId){
            return $builder->where('region_id', $regionId);
        });
    }

    public function scopeByRegions($query, array $ids){
        return $query->whereHas('province', function (Builder $builder) use($ids){
            return $builder->whereIn('region_id', $ids);
        });
    }


}
