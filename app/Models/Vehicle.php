<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Campa;
use App\Models\Category;
use App\Models\State;
use App\Models\DeliveryVehicle;
use App\Models\Request;
use App\Models\PendingTask;
use App\Models\GroupTask;
use App\Models\VehiclePicture;
use App\Models\Reservation;
use App\Models\HistoryLocation;
use App\Models\Reception;
use App\Models\TradeState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Class Vehicle
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Vehicle model",
 *     description="Vehicle model",
 * )
 */

class Vehicle extends Model
{

    /**
     * @OA\Schema(
     *      schema="VehicleWithAccessories",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Vehicle"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="accesories",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Accessory")
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="VehiclesWith",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Vehicle"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="accessories",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Accessory"),
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="orders",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Order"),
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="requests",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Request"),
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="reservations",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Reservation"),
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="category",
     *                   type="object",
     *                   ref="#/components/schemas/Category"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="color",
     *                   type="object",
     *                   ref="#/components/schemas/Color"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="campa",
     *                   type="object",
     *                   ref="#/components/schemas/Campa"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="sub_state",
     *                   type="object",
     *                   ref="#/components/schemas/SubStateWithState"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="type_model_order",
     *                   type="object",
     *                   ref="#/components/schemas/TypeModelOrder"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="vehicle_model",
     *                   type="object",
     *                   ref="#/components/schemas/VehicleModelWithBrand"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="square",
     *                   type="object",
     *                   ref="#/components/schemas/SquareWithStreet"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="last_delivery_vehicle",
     *                   type="object",
     *                   ref="#/components/schemas/LastDeliveryVehicle"
     *              )
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="VehiclePaginate",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Paginate"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/VehiclesWith"),
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="VehicleChangeSubState",
     *      allOf = {
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="vehicles",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Vehicle"),
     *              ),
     *          ),
     *          @OA\Property(
     *              property="type_delivery_note_id",
     *              type="integer",
     *          ),
     *          @OA\Property(
     *              property="sub_state_id",
     *              type="integer",
     *          ),
     *          @OA\Property(
     *              property="data",
     *              type="object",
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="VehiclesByID",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Vehicle"),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="last_group_task",
     *                   type="object",
     *                   ref="#/components/schemas/GroupTask"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="category",
     *                   type="object",
     *                   ref="#/components/schemas/Category"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="sub_state",
     *                   type="object",
     *                   ref="#/components/schemas/SubStateWithState"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="type_model_order",
     *                   type="object",
     *                   ref="#/components/schemas/TypeModelOrder"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="vehicle_model",
     *                   type="object",
     *                   ref="#/components/schemas/VehicleModelWithBrand"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="square",
     *                   type="object",
     *                   ref="#/components/schemas/SquareWithStreet"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="last_questionnaire",
     *                   type="object",
     *                   ref="#/components/schemas/LastQuestionnaire"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="receptions",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Reception")
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="reception",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/ReceptionWithAllApprovedPendingTaskAndGroupTask")
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="vehicle_pictures",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/VehiclePicture")
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="vehicle_exits",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/VehicleExit")
     *              ),
     *          ),
     *      },
     * )
     *
     * @OA\Schema(
     *      schema="VehicleWithVehicleModel",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Vehicle"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="vehicle_model",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/VehicleModel")
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="VehicleWithTypeModelOrderAndVehicleModel",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Vehicle"),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="type_model_order",
     *                   type="object",
     *                   ref="#/components/schemas/TypeModelOrder"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="vehicle_model",
     *                   type="object",
     *                   ref="#/components/schemas/VehicleModelWithBrand"
     *              )
     *          ),
     *      },
     * )
     *
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
     *     property="remote_id",
     *     type="integer",
     *     format="int64",
     *     description="Remote ID",
     *     title="Remote ID",
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
     *     property="campa_id",
     *     type="integer",
     *     format="int64",
     *     description="Campa ID",
     *     title="Campa ID",
     * )
     *
     * @OA\Property(
     *     property="category_id",
     *     type="integer",
     *     format="int64",
     *     description="Category ID",
     *     title="Category ID",
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
     *     property="color_id",
     *     type="integer",
     *     format="int64",
     *     description="Color ID",
     *     title="Color ID",
     * )
     *
     * @OA\Property(
     *     property="type_model_order_id",
     *     type="integer",
     *     format="int64",
     *     description="Type Model Order ID",
     *     title="Type Model Order ID",
     * )
     *
     * @OA\Property(
     *     property="ubication",
     *     type="string",
     *     description="Ubication",
     *     title="Ubication",
     * )
     *
     * @OA\Property(
     *     property="plate",
     *     type="string",
     *     description="Plate",
     *     title="Plate",
     * )
     *
     * @OA\Property(
     *     property="vehicle_model_id",
     *     type="integer",
     *     format="int64",
     *     description="Vehicle Model ID",
     *     title="Vehicle Model ID",
     * )
     *
     * @OA\Property(
     *     property="kms",
     *     type="integer",
     *     format="int32",
     *     description="KMS",
     *     title="KMS",
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
     *     property="next_itv",
     *     type="string",
     *     format="date-time",
     *     description="Next ITV",
     *     title="Next ITV",
     * )
     *
     * @OA\Property(
     *     property="has_environment_label",
     *     type="boolean",
     *     description="Has environment label",
     *     title="Has environment label",
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
     *     property="priority",
     *     type="boolean",
     *     description="Priority",
     *     title="Priority",
     * )
     *
     * @OA\Property(
     *     property="version",
     *     type="string",
     *     description="Version",
     *     title="Version",
     * )
     *
     * @OA\Property(
     *     property="vin",
     *     type="string",
     *     description="Vin",
     *     title="Vin",
     * )
     *
     * @OA\Property(
     *     property="first_plate",
     *     type="string",
     *     format="date-time",
     *     description="First Plate",
     *     title="First Plate",
     * )
     *
     * @OA\Property(
     *     property="latitude",
     *     type="string",
     *     description="Latitude",
     *     title="Latitude",
     * )
     *
     * @OA\Property(
     *     property="longitude",
     *     type="string",
     *     description="Longitude",
     *     title="Longitude",
     * )
     *
     * @OA\Property(
     *     property="image",
     *     type="string",
     *     description="Image",
     *     title="Image",
     * )
     *
     * @OA\Property(
     *     property="trade_state_id",
     *     type="integer",
     *     format="int64",
     *     description="Trade State ID",
     *     title="Trade State ID",
     * )
     *
     * @OA\Property(
     *     property="documentation",
     *     type="boolean",
     *     description="Documentation",
     *     title="Documentation",
     * )
     *
     * @OA\Property(
     *     property="ready_to_delivery",
     *     type="boolean",
     *     description="Ready to delivery",
     *     title="Ready to delivery",
     * )
     *
     * @OA\Property(
     *     property="created_by",
     *     type="integer",
     *     format="int64",
     *     description="Created by",
     *     title="Created by",
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
     * @OA\Property(
     *     property="deleted_user_id",
     *     type="integer",
     *     format="int64",
     *     description="Deleted User ID",
     *     title="Deleted User ID",
     * )
     */

    use HasFactory, Filterable, SoftDeletes, LogsActivity;

    protected $fillable = [
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
        'created_at',
        'updated_at'
    ];

    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function campa(){
        return $this->belongsTo(Campa::class, 'campa_id');
    }

    public function subState(){
        return $this->belongsTo(SubState::class);
    }

    public function color(){
        return $this->belongsTo(Color::class);
    }

    public function requests(){
        return $this->hasMany(Request::class, 'vehicle_id');
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function pendingTasks(){
        return $this->hasMany(PendingTask::class, 'vehicle_id');
    }

    public function pendingTasksBudget(){
        return $this->hasMany(PendingTask::class, 'vehicle_id')
            ->where('approved', true)
            ->where(function($query){
                return $query->where('state_pending_task_id', StatePendingTask::PENDING)
                    ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS);
            })->whereHas('budgetPendingTasks');
    }

    public function groupTasks(){
        return $this->hasMany(GroupTask::class);
    }

    public function vehiclePictures(){
        return $this->hasMany(VehiclePicture::class)
        ->whereRaw(DB::raw('reception_id = (Select max(r.id) from receptions r where r.vehicle_id = vehicle_pictures.vehicle_id)'));
    }

    public function reservations(){
        return $this->hasMany(Reservation::class);
    }

    public function questionnaries(){
        return $this->hasMany(Questionnaire::class);
    }

    public function receptions(){
        return $this->hasMany(Reception::class)->orderBy('id', 'desc');
    }

    public function reception(){
        return $this->hasOne(Reception::class, 'id', 'reception_id');
    }

    public function budgetPendingTask(){
        return $this->belongsTo(BudgetPendingTask::class);
    }

    public function typeModelOrder(){
        return $this->belongsTo(TypeModelOrder::class);
    }

    public function tradeState(){
        return $this->belongsTo(TradeState::class, 'trade_state_id');
    }

    public function questionnaires(){
        return $this->hasMany(Questionnaire::class);
    }

    public function vehicleExits(){
        return $this->hasMany(VehicleExit::class);
    }

    public function lastVehicleExit(){
        return $this->hasOne(VehicleExit::class)->ofMany([
            'id' => 'max'
        ]);
    }


    public function operations(){
        return $this->hasMany(Operation::class);
    }

    public function incidences(){
        return $this->hasMany(Incidence::class);
    }

    public function damages(){
        return $this->hasMany(Damage::class);
    }

    public function historyLocations(){
        return $this->hasMany(HistoryLocation::class);
    }

    public function accessories(){
        return $this->belongsToMany(Accessory::class)->withTimestamps();
    }

    public function accessoriesTypeAccessory(){
        return $this->belongsToMany(Accessory::class)
            ->where('accessory_type_id', Accessory::ACCESSORY);
    }

    public function pendingAuthorizations(){
        return $this->hasMany(PendingAuthorization::class);
    }

    public function lastQuestionnaire(){
        // ->with(['questionAnswers.question','questionAnswers.task'])
        return $this->hasOne(Questionnaire::class)->ofMany([
            'id' => 'max'
        ]);
    }

    public function lastReception(){
        return $this->hasOne(Reception::class)->ofMany([
            'id' => 'max'
        ]);
    }

    public function orders(){
        return $this->hasMany(Order::class);
    }

    public function lastOrder(){
        return $this->hasOne(Order::class)->ofMany([
            'id' => 'max'
        ]);
    }

    public function budgets(){
        return $this->hasMany(Budget::class);
    }

    public function deliveryVehicles(){
        return $this->hasMany(DeliveryVehicle::class);
    }

    public function lastDeliveryVehicle(){
        return $this->hasOne(DeliveryVehicle::class)->withTrashed()->ofMany([
            'id' => 'max'
        ]);
    }

    public function scopeByIds($query, $ids){
        return $query->whereIn('id', $ids);
    }

    public function vehicleModel(){
        return $this->belongsTo(VehicleModel::class);
    }

    public function subStateChangeHistories(){
        return $this->hasMany(SubStateChangeHistory::class);
    }

    public function vehicleComments() {
        return $this->hasMany(VehicleComment::class);
    }

    public function stateChange(){
        return $this->hasMany(StateChange::class);
    }

    public function lastStateChange(){
        return $this->hasOne(StateChange::class)->ofMany([
            'id' => 'max'
        ]);
    }

    public function square(){
        return $this->hasOne(Square::class)->orderBy('name', 'asc');
    }

    public function scopeByRole($query, $roleId){
        if ($roleId == Role::MANAGER_MECHANIC) {
            return $this->mechanic($query);
        }

        if ($roleId == Role::MANAGER_CHAPA) {
            return $this->chapa($query);
        }
    }

    private function mechanic($query){
        return $query->with(['vehicleModel.brand','lastReception.approvedPendingTasks.task.subState','lastReception.approvedPendingTasks.incidences','lastReception.approvedPendingTasks.vehicle.vehicleModel.brand',
            'lastReception.approvedPendingTasks.budgetPendingTasks.stateBudgetPendingTask','lastReception','campa','category','subState.state','tradeState','reservations',
            'requests.customer','lastReception.pendingTasks.budgetPendingTasks','lastReception.pendingTasks.incidences','lastReception.pendingTasks.task','lastReception.pendingTasks.statePendingTask','typeModelOrder','lastReception.approvedPendingTasks.statePendingTask',
            'lastReception.approvedPendingTasks' => function ($query) {
                return $query->where(function($query){
                    return $query->where('state_pending_task_id', StatePendingTask::PENDING)
                        ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS);
                })
                ->whereHas('task.subState', function($query){
                    return $query->where('state_id', State::WORKSHOP);
                });
            }
        ])
        ->whereHas('pendingTasks', function(Builder $builder){
            return $builder->where('approved', true)
                ->where(function($query){
                    $query->where('state_pending_task_id', StatePendingTask::PENDING)
                        ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS);
                })
                ->whereHas('task.subState', function(Builder $builder){
                    return $builder->where('state_id', State::WORKSHOP);
                });
        });
    }

    private function chapa($query){
        return $query->with(['vehicleModel.brand','lastReception.approvedPendingTasks.task.subState','lastReception.approvedPendingTasks.incidences','lastReception.approvedPendingTasks.vehicle.vehicleModel.brand',
        'lastReception.approvedPendingTasks.budgetPendingTasks.stateBudgetPendingTask','lastReception','campa','category','subState.state','tradeState','reservations',
        'requests.customer','lastReception.pendingTasks.budgetPendingTasks','lastReception.pendingTasks.incidences','lastReception.pendingTasks.task','lastReception.pendingTasks.statePendingTask','typeModelOrder','lastReception.approvedPendingTasks.statePendingTask',
        'lastReception.approvedPendingTasks' => function ($query) {
                return $query->where(function($query){
                    return $query->where('state_pending_task_id', StatePendingTask::PENDING)
                        ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS)
                        ->orWhereNull('state_pending_task_id');
                })
                ->whereHas('task', function($query){
                    return $query->where('sub_state_id', SubState::CHAPA)
                        ->orWhere('sub_state_id', SubState::MECANICA);
                });
            }
        ])
        ->whereHas('pendingTasks', function(Builder $builder){
            return $builder->where('approved', true)
                ->where(function($query){
                    $query->where('state_pending_task_id', StatePendingTask::PENDING)
                        ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS)
                        ->orWhereNull('state_pending_task_id');
                })
                ->whereHas('task', function(Builder $builder){
                    return $builder->where('sub_state_id', SubState::CHAPA);
                });
        });
    }

    public function scopeByWhereHasBudgetPendingTask($query){
        return $query->whereHas('pendingTasks.budgetPendingTasks');
    }

    public function scopeByCampasOfUser($query, array $campaIds){
        return $query->whereIn('campa_id', $campaIds);
    }

    public function scopeBySubStateNull($query){
        return $query->whereNull('sub_state_id');
    }

    public function scopeByCampaId($query, int $id){
        return $query->where('campa_id', $id);
    }

    public function scopeByCampas($query, array $campaIds){
        $ids = array_filter($campaIds, fn($value) => !is_null($value) && $value !== '' && $value != 0);
        if (count($ids) == count($campaIds)) {
            return $query->whereIn('campa_id', $ids);
        }
        return $query->whereNull('campa_id')->orWhereIn('campa_id', $ids);
    }

    public function scopeSubStateIds($query, array $ids){
        $idNull = count(array_filter($ids)) < count($ids) || in_array("null", $ids);
        if ($idNull) {
            return $query->where(function ($query) use ($ids) {
                $query->whereIn('sub_state_id', $ids)->orWhereNull('sub_state_id');
            });
        }
        return $query->whereIn('sub_state_id', $ids);
    }

    public function scopeStateIds($query, array $ids){
        return $query->whereHas('subState', function (Builder $builder) use ($ids) {
            return $builder->whereIn('state_id', $ids);
        });
    }

    public function scopeStateNotIds($query, array $ids){
        return $query->whereHas('subState', function (Builder $builder) use ($ids) {
            return $builder->whereNotIn('state_id', $ids);
        });
    }

    public function scopeByCompanies($query, array $ids){
        return $query->whereIn('company_id', $ids);
    }

    public function scopeByPlates($query, array $plates){
        return $query->whereIn('plate', $plates);
    }

    public function scopeByPlate($query, string $plate){
        return $query->where('plate','like',"%" . $plate . "%");
    }

    public function scopeByTradeStateIds($query, array $ids){
        return $query->whereIn('trade_state_id', $ids);
    }

    public function scopeBrandIds($query, array $ids){
        return $query->whereHas('vehicleModel', function (Builder $builder) use($ids) {
            return $builder->whereIn('brand_id', $ids);
        });
    }

    public function scopeByAccessorieTypeIds($query, array $ids){
        return $query->whereHas('accessories', function (Builder $builder) use($ids) {
            return $builder->whereIn('accessory_type_id', $ids);
        });
    }

    public function scopeByTypeModelOrderIds($query, array $ids){
        return $query->whereIn('type_model_order_id', $ids);
    }

    public function scopeByBudgetPendingTaskIds($query, array $ids){
        return $query->whereHas('pendingTasks', function(Builder $builder) use($ids){
            return $builder->whereHas('budgetPendingTasks', function ($query) use ($ids){
                return $query->whereIn('state_budget_pending_task_id', $ids);
            })
            ->where('approved', true);
        });
    }

    public function scopeVehicleModelIds($query, array $ids){
        return $query->whereIn('vehicle_model_id', $ids);
    }

    public function scopeCategoriesIds($query, array $ids){
        return $query->whereIn('category_id',$ids);
    }

    public function scopeByUbication($query, string $ubication){
        return $query->where('ubication','LIKE', "%$ubication%");
    }

    public function scopeByReadyDelivery($query, $value){
        return $query->where('ready_to_delivery', $value);
    }

    public function scopeByStatePendingTasks($query, array $ids){
        return $query->whereHas('lastReception.approvedPendingTasks.task.subState', function (Builder $builder) use($ids) {
            return $builder->whereIn('state_id', $ids);
        });
    }

    public function scopeBySubStatePendingTasks($query, array $ids){
        return $query->whereHas('lastReception.approvedPendingTasks.task', function (Builder $builder) use($ids) {
            return $builder->whereIn('sub_state_id', $ids);
        });
    }

    public function approvedPendingTasks(){
        return $this->hasMany(PendingTask::class, 'vehicle_id')
        ->where('approved', true)
        ->where(function ($query) {
            $query->where('state_pending_task_id', '<>', StatePendingTask::FINISHED)
                ->orWhereNull('state_pending_task_id');
        })
        ->orderBy('state_pending_task_id', 'desc')
        ->orderBy('order')
        ->orderBy('datetime_finish', 'desc');
    }

    public function allApprovedPendingTasks(){
        return $this->hasMany(PendingTask::class, 'vehicle_id')
            ->where('approved', 1)
            ->orderBy('reception_id', 'desc')
            ->orderBy('order')
            ->orderBy('datetime_finish', 'desc');
    }

    public function lastOrders(){
        return $this->hasOne(Order::class)->ofMany([
            'id' => 'max'
        ]);
    }

    public function lastUnapprovedReception(){
        return $this->hasOne(Questionnaire::class)->ofMany([
            'id' => 'max'
        ], function ($query) {
            $query
                ->whereRaw('reception_id = (Select max(r.id) from receptions r where r.vehicle_id = questionnaires.vehicle_id)')
                ->whereNull('datetime_approved');
        });
    }

    public function scopeByHasReceptionApproved($query, $value){
        return $query->whereHas('lastReception.lastQuestionnaire', function(Builder $builder) use ($value) {
            if (!$value) {
                return $builder->whereNull('datetime_approved');
            }
            return $builder->whereNotNull('datetime_approved');
        });
    }

    public function scopeByHasOrderNotFinish($query, $value){
        return $query->whereHas('orders', function (Builder $builder) {
            return $builder->where('state_id','!=', State::FINISHED);
        });
    }

    public function scopeByHasOrdersStateIds($query, $value){
         return $query->whereHas('orders', function (Builder $builder)  use ($value){
            return $builder->whereIn('state_id', $value);
        });
    }

    public function scopeNoActiveOrPendingRequest($query){
        return $query->whereHas('requests', function(Builder $builder) {
            return $builder->where('state_request_id', StateRequest::DECLINED);
        })
        ->orWhereDoesntHave('requests');
    }

    public function scopeByParameterDefleet($query, $dateDefleet, $kms){
        return $query->where('first_plate','<', $dateDefleet)
                    ->orWhere('kms','>', $kms);
    }

    public function scopeByPendingRequestDefleet($query){
        return $query->whereHas('requests', function(Builder $builder){
            return $builder->where('type_request_id', TypeRequest::DEFLEET)
                ->where(function($query) {
                    return $query->where('state_request_id', StateRequest::REQUESTED)
                        ->orWhere('state_request_id', StateRequest::APPROVED);
                });
        });
    }

    public function scopeThathasReservationWithoutOrderWithoutDelivery($query){
        return $query->whereHas('reservations', function (Builder $builder) {
            return $builder->where(function ($query) {
                return $query->whereNull('order');
            })
            ->orWhere(function ($query) {
                return $query->whereNotNull('order')
                    ->whereNull('pickup_by_customer')
                    ->whereNull('transport_id');
            })
            ->where('active', true);
        });
    }

    public function withRequestActive(){
        return $this->hasMany(Request::class)
        ->where('state_request_id', StateRequest::REQUESTED);
    }

    public function scopeByWithOrderWithoutContract($query){
        return $query->whereHas('reservations', function(Builder $builder) {
            return $builder->whereNotNull('order')
                ->whereNull('contract')
                ->where('active', true)
                ->where(function($query) {
                    return $query->whereNotNull('pickup_by_customer')
                                ->orWhereNotNull('transport_id');
                });
        });
    }

    public function scopeWithRequestDefleetActive($query){
        return $query->whereHas('requests', function (Builder $builder) {
            return $builder->where('type_request_id', TypeRequest::DEFLEET)
                    ->where('state_request_id', StateRequest::REQUESTED);
        });
    }

    public function scopeDifferentDefleeted($query){
        return $query->whereHas('subState.state', function (Builder $builder) {
            return $builder->where('id','!=', State::PENDING_SALE_VO);
        });
    }

    public function scopeDefleetBetweenDateApproved(Builder $builder, $dateStart, $dateEnd){
        return $builder->whereHas('requests', function($query) use($dateStart, $dateEnd){
            return $query->where('type_request_id', TypeRequest::DEFLEET)
                ->whereDate('datetime_approved', '>=', $dateStart)
                ->whereDate('datetime_approved', '<=', $dateEnd);
        });
    }

    public function withPendingTaskOrProgress(){
        return $this->hasMany(PendingTask::class)
            ->where(function($query){
                return $query->where('state_pending_task_id', StatePendingTask::PENDING)
                    ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS);
            })->orderBy('order');
    }

    public function withLastPendingTaskOrProgress(){
        return $this->hasOne(PendingTask::class)
            ->where(function($query){
                return $query->where('state_pending_task_id', StatePendingTask::PENDING)
                    ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS);
            })->ofMany([
            'order' => 'min'
        ]);
    }


    public function scopeByTaskSubStatesIds($query, $ids){
        return $query->whereHas('lastReception.approvedPendingTasks.task', function(Builder $builder) use ($ids){
            return $builder->whereIn('sub_state_id', $ids);
        });
    }

    public function scopeByTaskStatesIds($query, $ids){
        return $query->whereHas('lastReception.approvedPendingTasks.task.subState', function(Builder $builder) use ($ids){
            return $builder->whereIn('state_id', $ids);
        });

    }

    public function scopeByTaskIds($query, $ids){
        return $query->whereHas('lastReception.approvedPendingTasks', function(Builder $builder) use ($ids){
            return $builder->whereIn('task_id', $ids);
        });
    }

    public function receptionsByUserCampa()
    {
        $user = Auth::user();
        $campas_user_ids = CampaUser::where('user_id', $user->id)->get()->pluck('campa_id');
        return $this->hasMany(Reception::class, 'vehicle_id')->whereIn('campa_id', $campas_user_ids)->orderBy('id', 'desc');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
}
