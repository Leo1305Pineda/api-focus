<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\State;
use App\Models\Task;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Sub State
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Sub State model",
 *     description="Sub State model",
 * )
 */

class SubState extends Model
{

    /**
     * @OA\Schema(
     *      schema="SubStateWithState",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/SubState"),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="state",
     *                   ref="#/components/schemas/State"
     *              )
     *          ),
     *      },
     * )
     *
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     format="int64",
     *     description="ID",
     *     title="ID",
     * )
     *
     * @OA\Property(
     *     property="state_id",
     *     type="integer",
     *     format="int64",
     *     description="State ID",
     *     title="State ID",
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
     *     property="display_name",
     *     type="string",
     *     description="Display Name",
     *     title="Display Name",
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
     *
     * @OA\Property(
     *     property="deleted_at",
     *     type="string",
     *     format="date-time",
     *     description="When was deleted",
     *     title="Deleted at",
     * )
     */

    use HasFactory, Filterable, SoftDeletes;

    const CAMPA = 1;
    const PENDIENTE_LAVADO = 2;
    const MECANICA = 3;
    const CHAPA = 4;
    const TRANSFORMACION = 5;
    const ITV = 6;
    const LIMPIEZA = 7;
    const DEFLEETED = 8;
    const SIN_DOCUMENTACION = 9;
    const ALQUILADO = 10;
    const CHECK = 11;
    const CHECK_BLOCKED = 12;
    const CHECK_RELEASE = 29;
    const PENDING_TEST_DINAMIC_INITIAL = 13;
    const PENDING_INITIAL_CHECK = 14;
    const PENDING_BUDGET = 15;
    const PENDING_AUTHORIZATION = 16;
    const IN_REPAIR = 17;
    const PENDING_TEST_DINAMIC_FINAL = 18;
    const PENDING_TEST_CHECK_FINAL = 19;
    const PENDING_CERTIFICATED = 20;
    const FINISHED = 21;
    const WORKSHOP_EXTERNAL = 22;
    const PRE_AVAILABLE = 23;
    const WORKSHOP_MOON = 24;
    const TRANSIT = 25;
    const RETOURN_INCIDENCE = 31;

    protected $fillable = [
        'state_id',
        'name'
    ];

    public function state(){
        return $this->belongsTo(State::class, 'state_id');
    }

    public function tasks(){
        return $this->hasMany(Task::class, 'sub_state_id');
    }

    public function type_users_app(){
        return $this->belongsToMany(TypeUserApp::class);
    }

    public function subStateChangeHistories(){
        return $this->hasMany(SubStateChangeHistory::class);
    }

    public function scopeByStateIds($query, array $ids){
        return $query->whereIn('state_id', $ids);
    }

    public function scopeByNoStateIds($query, array $ids){
        return $query->whereNotIn('state_id', $ids);
    }

    public function scopeByIds($query, array $ids){
        return $query->whereIn('id', $ids);
    }

    public function scopeByNoIds($query, array $ids){
        return $query->whereNotIn('id', $ids);
    }
}
