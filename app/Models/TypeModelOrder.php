<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Type Model Order
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Type Model Order model",
 *     description="Type Model Order model",
 * )
 */

class TypeModelOrder extends Model
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
     *     property="name",
     *     type="string",
     *     description="Name",
     *     title="Name",
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

    use HasFactory, Filterable;

    const BIPI = 1;
    const REDRIVE = 2;
    const D2 = 3;
    const ALDFLEX = 4;
    const CARMARKET = 5;
    const DEVOLUTION = 6;
    const VO = 7;
    const VO_ENTREGADO = 8;
    const ALDFLEX_NUEVO = 9;
    const ALDFLEX_VN = 11;
    const ALDFLEX_REACOND = 12;

    protected $fillable = [
        'name'
    ];

    public function orders(){
        return $this->hasMany(Order::class);
    }
}
