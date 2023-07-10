<?php

namespace App\Filters;

use App\Filters\Base\BaseFilter\BaseFilter;
use App\Models\CampaUser;
use App\Models\PendingTask;
use App\Models\Role;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleFilter extends ModelFilter
{
    public function ids($ids)
    {
        return $this->byIds($ids);
    }

    public function campas($ids)
    {
        return $this->campasIds($ids);
    }

    public function campaIds($ids)
    {
        return $this->campasIds($ids);
    }

    public function campaNull($value)
    {
        if ($value) {
            return $this->whereNull('campa_id');
        }
        return $this->whereNotNull('campa_id');
    }

    public function companyIds($ids)
    {
        return $this->byCompanies($ids);
    }

    public function createdAt($date)
    {
        return $this->whereDate('created_at', $date);
    }

    public function subStates($ids)
    {
        return $this->subStateIds($ids);
    }

    public function states($ids)
    {
        return $this->stateIds($ids);
    }

    public function statesNotIds($ids)
    {
        return $this->stateNotIds($ids);
    }

    public function plate($plate)
    {
        return $this->byPlate($plate);
    }

    public function plates($plates)
    {
        return $this->byPlates($plates);
    }


    public function brands($ids)
    {
        return $this->brandIds($ids);
    }

    public function typeModelOrderIds($ids)
    {
        return $this->byTypeModelOrderIds($ids);
    }

    public function accessorieTypeIds($ids)
    {
        return $this->byAccessorieTypeIds($ids);
    }

    public function budgetPendingTaskIds($ids)
    {
        return $this->byBudgetPendingTaskIds($ids);
    }

    public function stateBudgetPendingTaskIds($ids)
    {
        return $this->whereHas('lastReception.pendingTasks', function (Builder $builder) use ($ids) {
            return $builder->whereHas('budgetPendingTasks', function ($query) use ($ids) {
                return $query->whereIn('state_budget_pending_task_id', $ids);
            })
                ->where('approved', true);
        });
    }

    public function vehicleModels($ids)
    {
        return $this->vehicleModelIds($ids);
    }

    public function categories($ids)
    {
        return $this->categoriesIds($ids);
    }

    public function tradeStates($ids)
    {
        return $this->byTradeStateIds($ids);
    }

    public function ubication($ids)
    {
        return $this->byUbication($ids);
    }

    public function readyDelivery($value)
    {
        return $this->byReadyDelivery($value);
    }

    public function statePendingTasks($ids)
    {
        return $this->byStatePendingTasks($ids);
    }

    public function subStatePendingTasks($ids)
    {
        return $this->bySubStatePendingTasks($ids);
    }

    public function subStateNull()
    {
        return $this->bySubStateNull();
    }

    public function whereHasBudgetPendingTask()
    {
        return $this->byWhereHasBudgetPendingTask();
    }

    public function byUserRole($roleId)
    {
        return $this->byRole($roleId);
    }

    public function hasReceptionApproved($value)
    {
        return $this->byHasReceptionApproved($value);
    }

    public function hasOrderNotFinish($value)
    {
        return $this->byHasOrderNotFinish($value);
    }

    public function hasOrdersStateIds($value)
    {
        return $this->byHasOrdersStateIds($value);
    }

    public function taskIds($ids)
    {
        return $this->byTaskIds($ids);
    }

    public function taskSubStatesIds($ids)
    {
        return $this->byTaskSubStatesIds($ids);
    }

    public function taskStatesIds($ids)
    {
        return $this->byTaskStatesIds($ids);
    }

    public function whereHasBudgetPendingTaskByState($stateId)
    {
        return $this->whereHas('pendingTask.budgetPendingTasks', fn (Builder $builder) => $builder->where('state_budget_pending_task_id', $stateId));
    }

    public function pendingTaskDateTimeStartFrom($dateTime)
    {
        return $this->whereHas('pendingTasks', function ($query) use ($dateTime) {
            return $query->whereDate('datetime_start', '>=', $dateTime);
        });
    }

    public function pendingTaskDateTimeStartTo($dateTime)
    {
        return $this->whereHas('pendingTasks', function ($query) use ($dateTime) {
            return $query->whereDate('datetime_start', '<=', $dateTime);
        });
    }

    public function pendingTaskDateTimeEndFrom($dateTime)
    {
        return $this->whereHas('pendingTasks', function ($query) use ($dateTime) {
            return $query->whereDate('datetime_end', '>=', $dateTime);
        });
    }

    public function pendingTaskDateTimeEndTo($dateTime)
    {
        return $this->whereHas('pendingTasks', function ($query) use ($dateTime) {
            return $query->whereDate('datetime_end', '<=', $dateTime);
        });
    }

    public function pendingTaskDateTimePendingFrom($dateTime)
    {
        return $this->whereHas('pendingTasks', function ($query) use ($dateTime) {
            return $query->whereDate('datetime_pending', '>=', $dateTime);
        });
    }

    public function pendingTaskDateTimePendingTo($dateTime)
    {
        return $this->whereHas('pendingTasks', function ($query) use ($dateTime) {
            return $query->whereDate('datetime_pending', '<=', $dateTime);
        });
    }

    public function lastReceptionCreatedAtFrom($dateTime)
    {
        return $this->whereHas('lastReception', function ($query) use ($dateTime) {
            return $query->where('created_at', '>=', $dateTime);
        });
    }

    public function lastReceptionCreatedAtTo($dateTime)
    {
        return $this->whereHas('lastReception', function ($query) use ($dateTime) {
            return $query->where('created_at', '<=', $dateTime);
        });
    }

    public function lastReceptionCreatedAt($dateTime)
    {
        return $this->whereHas('lastReception', function ($query) use ($dateTime) {
            return $query->whereDate('created_at', $dateTime);
        });
    }

    public function approvedPendingTasksNotNull($value)
    {
        if ($value) {
            $vehicle = Vehicle::whereHas('lastReception', function ($query) {
                return $query->whereDoesntHave('approvedPendingTasks');
            })->get('id');
            $value = collect($vehicle)->map(function ($item) {
                return $item->id;
            })->toArray();
            return $this->whereNotIn('id', $value);
        }
    }

    public function lastReceptionFirstPendingTaskIds($value)
    {
        if ($value) {
            $sql = <<<SQL
                select pt.task_id
                from pending_tasks pt
                WHERE pt.state_pending_task_id in (1, 2)
                AND pt.reception_id = (Select max(r.id) from receptions r where r.vehicle_id = pt.vehicle_id)
                AND pt.approved = 1
                AND pt.vehicle_id = vehicles.id
                ORDER BY pt.state_pending_task_id DESC, pt.order, pt.datetime_finish DESC
                limit 1
            SQL;
            return $this->selectRaw('*,(' . $sql . ') as task_id')
                ->whereRaw('(' . $sql . ') IN(' . implode(',', $value) . ')');
        }
    }

    public function isDefleeting($value)
    {
        if ($value) {
            return $this->whereNotNull('datetime_defleeting');
        } else {
            return $this->whereNull('datetime_defleeting');
        }
    }

    public function defleetingAndDelivery($value)
    {
        if ($value == 1) {
            return $this->whereDoesntHave('pendingTasks', function($q){
                $q->where('state_pending_task_id', 3)
                ->where('approved', 1)
                ->where('task_id', 38)
                ->whereRaw(DB::raw('reception_id = (Select max(r.id) from receptions r where r.vehicle_id = pending_tasks.vehicle_id)'))
                ->whereRaw(DB::raw('exists (SELECT v.id from vehicles v where v.sub_state_id = 8 and v.id = vehicle_id)'));
            });
        }
        return $this->whereHas('pendingTasks', function($q){
            $q->where('state_pending_task_id', 3)
            ->where('approved', 1)
            ->where('task_id', 38)
            ->whereRaw(DB::raw('reception_id = (Select max(r.id) from receptions r where r.vehicle_id = pending_tasks.vehicle_id)'))
            ->whereRaw(DB::raw('exists (SELECT v.id from vehicles v where v.sub_state_id = 8 and v.id = vehicle_id)'));
        });
        // $vehicle_ids = collect(
        //     PendingTask::where('state_pending_task_id', 3)
        //         ->where('approved', 1)
        //         ->where('task_id', 38)
        //         ->whereRaw(DB::raw('reception_id = (Select max(r.id) from receptions r where r.vehicle_id = pending_tasks.vehicle_id)'))
        //         ->whereRaw(DB::raw('exists (SELECT v.id from vehicles v where v.sub_state_id = 8 and v.id = vehicle_id)'))
        //         //->whereRaw(DB::raw('vehicle_id in (SELECT v.id from vehicles v where v.sub_state_id = 8)'))
        //         ->get('vehicle_id')
        // )->map(function ($item) {
        //     return $item->vehicle_id;
        // })->toArray();
        // if ($value == 1) {
        //     return $this->whereNotIn('id', $vehicle_ids);
        // }
        // return $this->whereIn('id', $vehicle_ids);
    }

    public function withoutSubState($value)
    {
        if ($value == 1) {
            return $this->orWhereNull('sub_state_id')
                ->where('campa_id', '3')
                ->whereNull('deleted_at');
        }
    }
    public function notSubStateIds($ids){
        return $this->whereNotIn('sub_state_id', $ids);

    }
    public function notExternalWorkshop($value)
    {
        if ($value == 1) {
            return $this->whereHas('subState', function($q){
                $state = [1, 2, 3,  4,  6,  7, 8,  9, 10,  11, 12,  13, 14,  15];
                $q->whereIn('state_id', $state)
                ->where('id', '<>', 22);
           });
        }
    }
    public function hasDamage($value)
    {
        return $this->whereHas('lastReception.damages');
    }

    public function orderDesc($field)
    {
        return $this->orderByDesc($field);
    }

    public function order($field)
    {
        return $this->orderBy($field);
    }


    public function withReceptionId($id)
    {
        $user = Auth::user();
        $campas_user_ids = implode(',',collect(CampaUser::where('user_id', $user->id)->get()->pluck('campa_id'))->toArray());
        $campas_user_ids = $campas_user_ids ?? "''";
        $sql = <<<SQL
            Select max(r.id) from receptions r where r.vehicle_id = vehicles.id
        SQL;
        return $this->selectRaw("*, (SELECT r.id FROM receptions r WHERE r.id = ". ($id ? $id : "({$sql} AND r.campa_id IN({$campas_user_ids}))") ." AND vehicles.id = r.vehicle_id ) AS reception_id");
    }
    public function withUbication()
    {
        return $this->with(array(
            'square' => function ($query) {
                $query->select('vehicle_id', 'id', 'street_id', 'name')
                    ->with(array(
                        'street' => function ($query) {
                            $query->select('id', 'name', 'zone_id')
                                ->with(array(
                                    'zone' => function ($query) {
                                        $query->select('id', 'name');
                                    }
                                ));
                        }
                    ));
            }
        ));
    }

}
