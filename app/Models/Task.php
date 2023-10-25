<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PendingTask;
use App\Models\SubState;
use App\Models\TypeTask;
use App\Models\PurchaseOperation;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Task
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Task model",
 *     description="Task model",
 * )
 */

class Task extends Model
{

    /**
     * @OA\Schema(
     *      schema="TaskWithSubStateAndTypeTask",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Task"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="sub_state",
     *                  type="object",
     *                  ref="#/components/schemas/SubStateWithState"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="type_task",
     *                  type="object",
     *                  ref="#/components/schemas/TypeTask"
     *              ),
     *          ),
     *      },
     * )
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
     *     property="sub_state_id",
     *     type="integer",
     *     format="int64",
     *     description="Sub State ID",
     *     title="Sub State ID",
     * )
     *
     * @OA\Property(
     *     property="type_task_id",
     *     type="integer",
     *     format="int64",
     *     description="Type of Task ID",
     *     title="Type of Task ID",
     * )
     *
     * @OA\Property(
     *     property="need_authorization",
     *     type="boolean",
     *     description="Need authorization",
     *     title="Need authorization",
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
     *     property="duration",
     *     type="number",
     *     format="double",
     *     description="Duration",
     *     title="Type of Task ID",
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

    use HasFactory, Filterable, SoftDeletes, LogsActivity;

    const UBICATION = 1;
    const TOCAMPA = 37;
    const TOALQUILADO = 38;
    const VALIDATE_CHECKLIST = 39;
    const TRANSFER = 40;
    const WORKSHOP_EXTERNAL = 53;
    const CHECK_BLOCKED = 61;
    const CHECK_RELEASE = 64;
    const RECEPTION = 68;
    const FUEL_DIESEL = 51;
    const FUEL_GASOLINE = 54;
    const PREVENTIVE = 71;
    const VO = 73;

    protected $fillable = [
        'company_id',
        'sub_state_id',
        'type_task_id',
        'need_authorization',
        'name',
        'duration',
        'cost'
    ];

    public function pendingTasks(){
        return $this->hasMany(PendingTask::class);
    }

    public function subState(){
        return $this->belongsTo(SubState::class);
    }

    public function typeTask(){
        return $this->belongsTo(TypeTask::class);
    }

    public function purchaseOperations(){
        return $this->hasMany(PurchaseOperation::class);
    }

    public function pendingAuthorizations(){
        return $this->hasMany(PendingAuthorization::class);
    }

    public function damages(){
        return $this->belongsToMany(Damage::class);
    }

    public function scopeByIds($query, array $ids){
        return $query->whereIn('id', $ids);
    }

    public function scopeByTypeTasks($query, array $ids){
        return $query->whereIn('type_task_id', $ids);
    }

    public function scopeBySubStates($query, array $ids){
        return $query->whereIn('sub_state_id', $ids);
    }

    public function scopeByCompany($query, array $ids){
        return $query->whereIn('company_id', $ids);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
}
