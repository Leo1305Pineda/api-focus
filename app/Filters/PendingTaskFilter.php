<?php

namespace App\Filters;

use App\Models\PendingTask;
use App\Models\State;
use App\Models\Vehicle;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PendingTaskFilter extends ModelFilter
{
    protected function relatedDate($relation, $column, $value)
    {
        return $this->whereHas($relation, function ($query) use ($column, $value) {
            $query->whereDate($column, $value);
        });
    }

    protected function relatedDateFrom($relation, $column, $fromDate)
    {
        return $this->whereHas($relation, function ($query) use ($column, $fromDate) {
            $query->whereDate($column, '>=',$fromDate);
        });
    }
    protected function relatedDateTo($relation, $column, $toDate)
    {
        return $this->whereHas($relation, function ($query) use ($column, $toDate) {
            $query->whereDate($column, '<=',$toDate);
        });
    }

    public function vehicles($ids){
        return $this->byVehicleIds($ids);
    }

    public function typeModelOrderIds($ids){
        return $this->whereHas('vehicle', function($query) use($ids) {
            return $query->whereIn('type_model_order_id', $ids);
        });
    }

    public function tasks($ids){
        return $this->byTaskIds($ids);
    }

    public function campasIds($campasIds)
    {
        return $this->whereHas('reception', function(Builder $builder) use($campasIds){
            $ids = array_filter($campasIds, fn($value) => !is_null($value) && $value !== '' && $value != 0); 
            if (count($ids) == count($campasIds)) {
                return $builder->whereIn('campa_id', $ids);
            }
            return $builder->whereNull('campa_id')->whereIn('campa_id', $ids);
        });
    }

    public function withoutOrderOrOrderFinished($value) {
        return $this->whereDoesntHave('vehicle.orders')
            ->orWhereHas('vehicle.orders', function(Builder $builder) {
            return $builder->where('state_id', State::FINISHED);
        });
    }

    public function withWorkshop($value) {
        return $this->whereHas('vehicle.orders', function(Builder $builder) use ($value) {
            return $builder->where('workshop_id', $value);
        });
    }

    public function states($value) {
        return $this->whereHas('vehicle.subState', function(Builder $builder) use ($value) {
            return $builder->whereIn('state_id', $value);
        });
    }

    public function campas($value) {
        return $this->whereHas('vehicle', function(Builder $builder) use ($value) {
            return $builder->whereIn('campa_id', $value);
        });
    }

    public function statePendingTasks($ids){
        return $this->byStatePendingTaskIds($ids);
    }

    public function ids($ids){
        return $this->byIds($ids);
    }

    public function taskIds($ids){
        return $this->byTaskIds($ids);
    }

    public function vehiclePlate($plate){
        return $this->whereHas('vehicle', function(Builder $builder) use($plate){
            return $builder->where('plate','like',"%$plate%");
        });
    }

    public function receptionNull($value)
    {
        if ($value) {
            return $this->whereNull('reception_id');
        }
        return $this->whereNotNull('reception_id');
    }

    public function createdAt($dateTime)
    {
        return $this->relatedDate('reception', 'created_at', $dateTime);
    }

    public function receptionFrom($dateTime)
    {
        return $this->relatedDateFrom('reception', 'receptions.created_at', $dateTime);
    }

    public function receptionTo($dateTime)
    {
        return $this->relatedDateTo('reception', 'receptions.created_at', $dateTime);
    }

    public function exitFrom($dateTime)
    {
        return $this->relatedDateFrom('lastDeliveryVehicle', 'delivery_vehicles.created_at', $dateTime);
    }

    public function exitTo($dateTime)
    {
        return $this->relatedDateTo('lastDeliveryVehicle', 'delivery_vehicles.created_at', $dateTime);
    }

    public function createdAtFrom($dateTime)
    {
        return $this->whereDate('created_at','>=', $dateTime);
    }

    public function createdAtTo($dateTime)
    {
        return $this->whereDate('created_at','<=', $dateTime);
    }

    public function dateTimeStartFrom($dateTime)
    {
        return $this->whereDate('datetime_start','>=', $dateTime);
    }

    public function dateTimeStartTo($dateTime)
    {
        return $this->whereDate('datetime_start','<=', $dateTime);
    }

    public function dateTimeStart($dateTime)
    {
        return $this->whereDate('datetime_start', $dateTime);
    }

    public function dateTimeEndFrom($dateTime)
    {
        return $this->whereDate('datetime_finish','>=', $dateTime);
    }

    public function dateTimeEnd($dateTime)
    {
        return $this->whereDate('datetime_finish', $dateTime);
    }

    public function dateTimeEndTo($dateTime)
    {
        return $this->whereDate('datetime_finish','<=', $dateTime);
    }

    public function dateTimePendingFrom($dateTime)
    {
        return $this->whereDate('datetime_pending','>=', $dateTime);
    }

    public function dateTimePendingTo($dateTime)
    {
        return $this->whereDate('datetime_pending','<=', $dateTime);
    }

    public function userIds($ids)
    {
        return $this->whereIn('user_id', $ids);
    }

    public function userStartIds($ids)
    {
        return $this->whereIn('user_start_id', $ids);
    }

    public function userEndIds($ids)
    {
        return $this->whereIn('user_end_id', $ids);
    }

    public function alreadyReception($ids)
    {
        $vehicles = Vehicle::with('lastReception')->get();
        foreach ($vehicles as $key => $value) {
            if ($value->lastReception) {
                $ids[] = $value->lastReception->id;                
            }
        }
        return $this->whereIn('reception_id', $ids);
    }

    public function approved($approved){
        return $this->byApproved($approved);
    }

    public function orderDesc($field){
        return $this->orderByDesc($field);
    }

    public function order($field){
        return $this->orderBy($field);
    }

    public function defleetingAndDelivery($value)
    {
        $vehicle_ids = collect(
            PendingTask::where('state_pending_task_id', 3)
                ->where('approved', 1)
                ->where('task_id', 38)
                ->whereRaw(DB::raw('reception_id = (Select max(r.id) from receptions r where r.vehicle_id = pending_tasks.vehicle_id)'))
                ->whereRaw(DB::raw('vehicle_id in (SELECT v.id from vehicles v where v.sub_state_id = 8)'))
                ->get('vehicle_id')
        )->map(function ($item) {
            return $item->vehicle_id;
        })->toArray();
        if ($value == 1) {
            return $this->whereNotIn('vehicle_id', $vehicle_ids);
        }
        return $this->whereIn('vehicle_id', $vehicle_ids);
    }

    

}
