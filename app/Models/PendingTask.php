<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vehicle;
use App\Models\VehicleExit;
use App\Models\Task;
use App\Models\StatePendingTask;
use App\Models\GroupTask;
use App\Models\Incidence;
use Carbon\Carbon;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class Pending Task
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Pending Task model",
 *     description="Pending Task model",
 * )
 */

class PendingTask extends Model
{
    /**
     *
     *  @OA\Schema(
     *      schema="PendingTaskPaginate",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Paginate"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/PendingTask"),
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="LastDeliveryVehicle",
     *      allOf = {
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="campa",
     *                  type="object",
     *                  ref="#/components/schemas/Campa"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="campa_id",
     *                  type="integer",
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="canceled_by",
     *                  type="string",
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="data_delivery",
     *                  type="string",
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="created_at",
     *                  type="string",
     *                  format="date-time"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="deleted_at",
     *                  type="string",
     *                  format="date-time"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="created_at",
     *                  type="string",
     *                  format="date-time"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="updated_at",
     *                  type="string",
     *                  format="date-time"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="id",
     *                  type="integer",
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="delivery_note_id",
     *                  type="integer",
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="pending_task_id",
     *                  type="integer",
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="vehicle_id",
     *                  type="integer",
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="LastPendingTaskDelivery",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/PendingTask"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="last_delivery_vehicle",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/LastDeliveryVehicleWithPendingTask"),
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="LastDeliveryVehicleWithPendingTask",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/PendingTask"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="pending_task",
     *                  type="object",
     *                  ref="#/components/schemas/PendingTask",
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="PendingTaskWithVehicle",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/PendingTask"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="vehicle",
     *                  type="object",
     *                  ref="#/components/schemas/Vehicle"
     *              ),
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
     *     property="vehicle_id",
     *     type="integer",
     *     format="int64",
     *     description="Vehicle ID",
     *     title="Vehicle ID",
     * )
     *
     * @OA\Property(
     *     property="reception_id",
     *     type="integer",
     *     format="int64",
     *     description="Reception ID",
     *     title="Reception ID",
     * )
     *
     * @OA\Property(
     *     property="question_answer_id",
     *     type="integer",
     *     format="int64",
     *     description="Question Answer ID",
     *     title="Question Answer ID",
     * )
     *
     * @OA\Property(
     *     property="task_id",
     *     type="integer",
     *     format="int64",
     *     description="Task ID",
     *     title="Task ID",
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
     *     property="state_pending_task_id",
     *     type="integer",
     *     format="int64",
     *     description="State Pending Task ID",
     *     title="State Pending Task ID",
     * )
     *
     * @OA\Property(
     *     property="user_start_id",
     *     type="integer",
     *     format="int64",
     *     description="User Start ID",
     *     title="User Start ID",
     * )
     *
     * @OA\Property(
     *     property="user_end_id",
     *     type="integer",
     *     format="int64",
     *     description="User End ID",
     *     title="User End ID",
     * )
     *
     * @OA\Property(
     *     property="damage_id",
     *     type="integer",
     *     format="int64",
     *     description="Damage ID",
     *     title="Damage ID",
     * )
     *
     * @OA\Property(
     *     property="duration",
     *     type="number",
     *     format="double",
     *     description="duration",
     *     title="duration",
     * )
     *
     * @OA\Property(
     *     property="order",
     *     type="integer",
     *     format="int32",
     *     description="Order",
     *     title="Order",
     * )
     *
     * @OA\Property(
     *     property="approved",
     *     type="boolean",
     *     description="Approved",
     *     title="Approved",
     * )
     *
     * @OA\Property(
     *     property="observations",
     *     type="string",
     *     description="Observations",
     *     title="Observations",
     * )
     *
     * @OA\Property(
     *     property="code_authorization",
     *     type="string",
     *     description="Code of Authorization",
     *     title="Code of Authorization",
     * )
     *
     * @OA\Property(
     *     property="created_from_checklist",
     *     type="boolean",
     *     description="Created from Checklist",
     *     title="Created from Checklist",
     * )
     *
     * @OA\Property(
     *     property="status_color",
     *     type="string",
     *     description="Status color",
     *     title="Status color",
     * )
     *
     * @OA\Property(
     *     property="datetime_pending",
     *     type="string",
     *     format="date-time",
     *     description="Datetime pending",
     *     title="Datetime pending",
     * )
     *
     * @OA\Property(
     *     property="datetime_start",
     *     type="string",
     *     format="date-time",
     *     description="Datetime start",
     *     title="Datetime start",
     * )
     *
     * @OA\Property(
     *     property="datetime_pause",
     *     type="string",
     *     format="date-time",
     *     description="Datetime pause",
     *     title="Datetime pause",
     * )
     *
     * @OA\Property(
     *     property="total_paused",
     *     type="number",
     *     format="double",
     *     description="Total paused",
     *     title="Total paused",
     * )
     *
     * @OA\Property(
     *     property="datetime_finish",
     *     type="string",
     *     format="date-time",
     *     description="Datetime finish",
     *     title="Datetime finish",
     * )
     *
     * @OA\Property(
     *     property="last_change_state",
     *     type="string",
     *     format="date-time",
     *     description="Last Change State",
     *     title="Last Change State",
     * )
     *
     * @OA\Property(
     *     property="last_change_sub_state",
     *     type="string",
     *     format="date-time",
     *     description="Last Change Sub State",
     *     title="Last Change Sub State",
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
     *     property="comment_state",
     *     type="string",
     *     description="Comment State",
     *     title="Comment State",
     * )
     *
     * @OA\Property(
     *     property="user_id",
     *     type="integer",
     *     format="int64",
     *     description="User ID",
     *     title="User ID",
     * )
     */

    use HasFactory, Filterable, LogsActivity;

    const ORDER_TASKS = [39, 11, 2, 3, 4, 41, 74, 5, 6, 7, 8];

    protected $fillable = [
        'vehicle_id',
        'reception_id',
        'task_id',
        'question_answer_id',
        'campa_id',
        'state_pending_task_id',
        'type_model_order_id',
        'user_start_id',
        'user_end_id',
        'group_task_id',
        'damage_id',
        'duration',
        'order',
        'approved',
        'observations',
        'code_authorization',
        'created_from_checklist',
        'status_color',
        'datetime_pending',
        'datetime_start',
        'datetime_pause',
        'datetime_finish',
        'comment_state',
        'user_id'
    ];

    protected $appends = ['total_paused', 'hours'];

    public function getTotalPausedAttribute()
    {
        $datetime_start = Carbon::create($this->datetime_start);
        $datetime_finish = Carbon::create($this->datetime_finish);
        // $diffD = $datetime_start->diffInDays($datetime_finish) || '';
        // $diffS = $datetime_start->diffInSeconds($datetime_finish);
        // $gmdate = gmdate('H:i:s', $diffS);

        setlocale(LC_TIME, 'es_ES');
        Carbon::setLocale('es');
        $difference = $datetime_finish->longRelativeDiffForHumans($datetime_start, 3);
        return str_replace('antes', '', str_replace('después', '', $difference));
        // return str_replace(' horas ', ':'  ,str_replace('días' , ' ',$difference));

        // return "$diffD $gmdate";
    }

    public function hhmm($secs)
    {
        $minutes = floor($secs / 60);
        $secs = $secs % 60;
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return $hours < 10 ? $hours : "$hours,$minutes";
        // return pad(hours)+":"+pad(minutes)+":"+pad(secs); for old browsers
    }

    public function getHoursAttribute()
    {
        $datetime_start = Carbon::parse($this->datetime_start);
        $datetime_finish = Carbon::parse($this->datetime_finish);
        $diffS = $datetime_start->diffInSeconds($datetime_finish);
        return $this->hhmm($diffS);
        //return str_replace('.', ',', (string) round($diffS / 3600, 2));
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function typeModelOrder()
    {
        return $this->belongsTo(TypeModelOrder::class);
    }

    public function reception()
    {
        return $this->belongsTo(Reception::class);
    }

    public function lastDeliveryVehicle()
    {
        return $this->hasOne(DeliveryVehicle::class)->withTrashed()->ofMany([
            'pending_task_id' => 'max'
        ]);
    }

    public function lastVehicleExit()
    {
        return $this->hasOne(VehicleExit::class)->ofMany([
            'pending_task_id' => 'max'
        ]);
    }

    public function lastDeliveredPendingTask()
    {
        return $this->belongsTo(PendingTask::class, 'last_delivered_pending_task_id', 'id');
    }

    public function questionAnswer()
    {
        return $this->belongsTo(QuestionAnswer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function campa()
    {
        return $this->belongsTo(Campa::class);
    }

    public function statePendingTask()
    {
        return $this->belongsTo(StatePendingTask::class);
    }

    public function groupTask()
    {
        return $this->belongsTo(GroupTask::class);
    }

    public function damage()
    {
        return $this->belongsTo(Damage::class);
    }

    public function incidences()
    {
        return $this->belongsToMany(Incidence::class);
    }

    public function pendingTaskCanceled()
    {
        return $this->hasMany(PendingTaskCanceled::class);
    }

    public function vehicleExit()
    {
        return $this->hasOne(VehicleExit::class);
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }

    public function budgetPendingTasks()
    {
        return $this->hasMany(BudgetPendingTask::class);
    }

    public function estimatedDates()
    {
        return $this->hasMany(EstimatedDate::class);
    }

    public function lastEstimatedDate()
    {
        return $this->hasOne(EstimatedDate::class)->ofMany([
            'id' => 'max'
        ]);
    }

    public function userStart()
    {
        return $this->belongsTo(User::class, 'user_start_id');
    }

    public function userEnd()
    {
        return $this->belongsTo(User::class, 'user_end_id');
    }

    public function scopeByCampas($query, array $ids)
    {
        return $query->whereHas('vehicle.campa', function (Builder $builder) use ($ids) {
            return $builder->whereIn('id', $ids);
        });
    }

    public function scopePendingOrInProgress($query)
    {
        return $query->where('state_pending_task_id', StatePendingTask::PENDING)
            ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS);
    }

    public function scopeCanSeeHomework($query, $user)
    {
        return $query->whereHas('task.subState.type_users_app', function ($query) use ($user) {
            return $query->where(function ($where) use ($user) {
                $where->where('type_user_app_id', $user['type_user_app_id'])
                    ->when(in_array(3, $user->campas->pluck('id')->toArray()) && ($user['type_user_app_id'] == 3), function ($q) { // solo para leganes
                        $tasks = [Task::FUEL_DIESEL, Task::FUEL_GASOLINE];
                        return $q->orWhereIn('task_id', $tasks);
                    });
            });
        });
    }

    public function scopeByPlate($query, string $plate)
    {
        return $query->whereHas('vehicle', function (Builder $builder) use ($plate) {
            return $builder->where('plate', $plate);
        });
    }

    public function scopeByVehicleIds($query, array $ids)
    {
        return $query->whereIn('vehicle_id', $ids);
    }

    public function scopeByTaskIds($query, array $ids)
    {
        return $query->whereIn('task_id', $ids);
    }

    public function scopeByStatePendingTaskIds($query,  array $ids)
    {
        return $query->whereIn('state_pending_task_id', $ids);
    }

    public function scopeByIds($query, array $ids)
    {
        return $query->whereIn('id', $ids);
    }

    public function scopeByApproved($query, int $approved)
    {
        return $query->where('approved', $approved);
    }

    /**
     * Scope a query to only include the last n days records
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereDateBetween($query, $fieldName, $fromDate, $todate)
    {
        return $query->whereDate($fieldName, '>=', $fromDate)->whereDate($fieldName, '<=', $todate);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['*'])
            ->logOnlyDirty();
    }
}
