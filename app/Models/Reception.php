<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class Reception
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Reception model",
 *     description="Reception model",
 * )
 */

class Reception extends Model
{
    /**
     * @OA\Schema(
     *      schema="ReceptionPaginate",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Paginate"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/ReceptionWithCampaAndVehicle"),
     *              ),
     *          ),
     *      },
     * )
     * /**
     * @OA\Schema(
     *      schema="ReceptionWithCampaAndVehicle",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Reception"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="campa",
     *                  type="object",
     *                  ref="#/components/schemas/Campa"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="Vehicle",
     *                  type="object",
     *                  ref="#/components/schemas/Vehicle"
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="ReceptionWithAllApprovedPendingTaskAndGroupTask",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Reception"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="group_task",
     *                  type="object",
     *                  ref="#/components/schemas/GroupTask"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="all_approved_pending_task",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/AllApprovedPendingTask"),
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="last_pending_task_delivery",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/LastPendingTaskDelivery"),
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="AllApprovedPendingTask",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/PendingTask"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="state_pending_task",
     *                  type="object",
     *                  ref="#/components/schemas/StatePendingTask"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="Task",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Task"),
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="user",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/User"),
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="user_start",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/User"),
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
     *     property="campa_id",
     *     type="integer",
     *     format="int64",
     *     description="Campa ID",
     *     title="Campa ID",
     * )
     *
     * @OA\Property(
     *     property="vehicle_id",
     *     type="integer",
     *     format="int64",
     *     description="Vehicle ID",
     *     title="Vehicle ID",
     * )
     *
     * @OA\Property(
     *     property="type_model_order_id",
     *     type="integer",
     *     format="int64",
     *     description="Type of Model Order ID",
     *     title="Type of Model Order ID",
     * )
     *
     * @OA\Property(
     *     property="finished",
     *     type="boolean",
     *     description="Finished",
     *     title="Finished",
     * )
     *
     * @OA\Property(
     *     property="has_accessories",
     *     type="boolean",
     *     description="Has accesories",
     *     title="Has accesories",
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
     *
     */

    use HasFactory, Filterable, LogsActivity;

    protected $fillable = [
        'type_model_order_id',
        'vehicle_id',
        'finished',
        'has_accessories',
        'created_at',
        'updated_at',
        /** Vehicle fields */
        'remote_id',
        'company_id',
        "campa_id",
        'category_id',
        'sub_state_id',
        'color_id',
        'ubication',
        'plate',
        'vehicle_model_id',
        'color',
        'type_model_order_id',
        'kms',
        'last_change_state',
        'last_change_sub_state',
        'next_itv',
        'has_environment_label',
        'observations',
        'priority',
        'version',
        'vin',
        'first_plate',
        'latitude',
        'longitude',
        'trade_state_id',
        'documentation',
        'ready_to_delivery',
        'deleted_user_id',
        'seater',
        /** End */

    ];

    protected $dates = [
        'deleted_at'
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function subState(){
        return $this->belongsTo(SubState::class);
    }

    public function typeModelOrder(){
        return $this->belongsTo(TypeModelOrder::class);
    }

    public function typeReception(){
        return $this->belongsTo(TypeReception::class);
    }

    public function accessories(){
        return $this->hasMany(Accessory::class);
    }

    public function damages(){
        return $this->hasMany(Damage::class);
    }

    public function historyLocations(){
        return $this->hasMany(HistoryLocation::class);
    }
    public function allPendingTasks() {
        return $this->hasMany(PendingTask::class);
    }

    public function pendingTasks() {
        return $this->hasMany(PendingTask::class)
        ->whereRaw(DB::raw('reception_id = (Select max(id) from receptions where receptions.vehicle_id = pending_tasks.vehicle_id)'))
        ->where('approved', 1)
        ->orderBy('state_pending_task_id', 'desc')
        ->orderBy('order')
        ->orderBy('datetime_finish', 'desc');;
    }

    public function receptionPendingTask() {
        return $this->hasMany(PendingTask::class)
        ->whereRaw(DB::raw('reception_id = (Select max(id) from receptions where receptions.vehicle_id = pending_tasks.vehicle_id)'))
        ->where('task_id', Task::RECEPTION)
        ->where('approved', 1);
    }

    public function approvedPendingTasks() {
        return $this->hasMany(PendingTask::class, 'reception_id')
        ->where('approved', true)
        ->where(function ($query) {
            $query->whereNotIn('state_pending_task_id',[StatePendingTask::FINISHED, StatePendingTask::CANCELED])
                ->orWhereNull('state_pending_task_id');
        })
        ->orderBy('state_pending_task_id', 'desc')
        ->orderBy('order')
        ->orderBy('datetime_finish', 'desc');
    }

    public function vehiclePictures(){
        return $this->hasMany(VehiclePicture::class);
    }

    public function campa(){
        return $this->belongsTo(Campa::class);
    }

    public function groupTask(){
        return $this->belongsTo(GroupTask::class);
    }

    public function defleetingAndDelivery($value) {
        $vehicle_ids = collect(PendingTask::where('state_pending_task_id', 3)
            ->where('task_id', 38)
            ->whereRaw(Db::raw('reception_id = (Select max(r.id) from receptions r where r.vehicle_id = pending_tasks.vehicle_id)'))
            ->whereRaw(DB::raw('vehicle_id in (SELECT v.id from vehicles v where v.sub_state_id = 8)'))
            ->get('vehicle_id'))->map(function ($item){ return $item->vehicle_id;})->toArray();
        if ($value) {
            return $this->whereNotIn('vehicle_id', $vehicle_ids);
        }
        return $this->whereIn('vehicle_id', $vehicle_ids);
    }

    public function allApprovedPendingTasks(){
        return $this->hasMany(PendingTask::class)
            ->where('approved', 1)
            ->selectRaw('*, (CASE WHEN pending_tasks.order > 0 THEN pending_tasks.order Else 100000000000 END) as order_str')
            ->orderByRaw('order_str asc')
            ->orderBy('datetime_finish', 'desc');
    }

    public function lastPendingTaskDelivery(){
        return $this->hasOne(PendingTask::class)
            ->where('task_id', Task::TOALQUILADO)
            ->where('approved', true)
            ->where(function($query){
                return $query->whereIn('state_pending_task_id', [StatePendingTask::FINISHED, StatePendingTask::CANCELED]);
            });
    }

    public function lastPendingTaskWorkshopExternal(){
        return $this->hasOne(PendingTask::class)
            ->where('task_id', Task::WORKSHOP_EXTERNAL)
            ->where('approved', true)
            ->where(function($query){
                return $query->whereIn('state_pending_task_id', [StatePendingTask::FINISHED]);
            });
    }

    public function lastTaskValidateChecklist(){
        return $this->hasOne(PendingTask::class)
            ->where('task_id', Task::VALIDATE_CHECKLIST)
            ->where('approved', true)
            ->where(function($query){
                return $query->whereIn('state_pending_task_id', [StatePendingTask::FINISHED]);
            });
    }

    public function lastTaskVehicleExit(){
        return $this->hasOne(PendingTask::class)
            ->where('task_id', Task::TOALQUILADO)
            ->where('approved', true)
            ->where(function($query){
                return $query->whereIn('state_pending_task_id', [StatePendingTask::FINISHED]);
            });
    }

    public function lastTaskVo(){
        return $this->hasOne(PendingTask::class)
            ->where('task_id', Task::VO)
            ->where('approved', true)
            ->where(function($query){
                return $query->whereIn('state_pending_task_id', [StatePendingTask::FINISHED]);
            });
    }


    public function scopeBySubStatesNotIds($query, array $ids){
        return $query->whereHas('vehicle', function (Builder $builder) use ($ids) {
            return $builder->whereNotIn('sub_state_id', $ids)->orWhereNull('sub_state_id');
        });
    }

    public function lastPendingTaskWithState(){
        return $this->hasOne(PendingTask::class)
            ->where('approved', true)
            ->where(function ($query) {
                $query->where('state_pending_task_id', StatePendingTask::PENDING)
                ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS);
            });
    }

    public function defaultOrderApprovedPendingTasks() {
        return $this->hasMany(PendingTask::class, 'reception_id')
        ->where('approved', true)
        ->where(function ($query) {
            $query->whereNotIn('state_pending_task_id',[StatePendingTask::FINISHED, StatePendingTask::CANCELED])
                ->orWhereNull('state_pending_task_id');
        })
        ->orderByRaw('FIELD(task_id,'.implode(',',PendingTask::ORDER_TASKS).') asc');
    }

    public function lastChangeState(){
        return $this->hasOne(PendingTask::class)
            ->where('approved', true)
            ->whereIn('state_pending_task_id', [StatePendingTask::PENDING, StatePendingTask::IN_PROGRESS, StatePendingTask::FINISHED])
            ->whereNotNull('last_change_state');
    }

    public function lastChangeSubState(){
        return $this->hasOne(PendingTask::class)
            ->where('approved', true)
            ->whereNotNull('last_change_sub_state');
    }

    public function lastQuestionnaire(){
        // ->with(['questionAnswers.question','questionAnswers.task'])
        return $this->hasOne(Questionnaire::class)->ofMany([
            'id' => 'max'
        ]);
    }

    public function deletedUser(){
        return $this->belongsTo(User::class, 'deleted_user_id');
    }

    public function vehicleComments() {
        return $this->hasMany(VehicleComment::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
}
