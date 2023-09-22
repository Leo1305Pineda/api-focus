<?php

namespace App\Filters;

use App\Models\Campa;
use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class DeliveryVehicleFilter extends ModelFilter
{

    public function ids($ids)
    {
        return $this->byIds($ids);
    }

    public function vehicleIds($ids){
        return $this->whereIn('vehicle_id', $ids);
    }

    public function campaIds($ids){
        return $this->whereIn('campa_id', $ids);
    }

    public function pendindTaskNull($value){
        if ($value) {
            return $this->whereNull('pending_task_id');
        }
        return $this->whereNotNull('pending_task_id');
    }

    public function createdAt($date){
        return $this->whereDate('created_at', $date);
    }

    public function createdAtFrom($dateTime)
    {
        return $this->where('delivery_vehicles.created_at','>=', $dateTime);
    }

    public function createdAtTo($dateTime)
    {
        return $this->where('delivery_vehicles.created_at','<=', $dateTime);
    }

    public function whereHasVehicle($value)
    {
        return $this->whereHas('vehicle');
    }

    public function vehicleDeleted($value){
        return $this->whereHas('vehicle', function(Builder $builder) use($value){
            if ($value) {
                return $builder->whereNotNull('deleted_at');
            }
            return $builder->whereNull('deleted_at');
        });
    }

    public function vehiclePlate($plate){
        return $this->whereHas('vehicle', function(Builder $builder) use($plate){
            return $builder->where('plate','like',"%$plate%");
        });
    }

    public function vehicleBrandIds($ids){
        return $this->whereHas('vehicle.vehicleModel', function(Builder $builder) use($ids){
            return $builder->whereIn('brand_id', $ids);
        });
    }

    public function vehicleTypeModelOrderIds($ids){
        return $this->whereHas('vehicle', function(Builder $builder) use($ids){
            return $builder->whereIn('type_model_order_id', $ids);
        });
    }

    public function notNullId($value){
        return $this->whereNotNull('delivery_note_id');
    }

    public function forceExitFromCampa($value){
        $campa = Campa::find($value);
        $name =$campa->name;
        return $this->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(data_delivery) , '$.force_exit_from_campa'))='$name'");
    }
    public function forceExitToCampa($value){
        return $this->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(data_delivery) , '$.exit_campa'))=$value");
    }

    public function hasForceExit(){
        return $this->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(JSON_UNQUOTE(data_delivery) , '$.force_exit_from_campa')) IS NOT NULL");
    }

    public function deletedAt($value){
        if ($value == 1) {
            return $this->withTrashed()->whereNotNull('deleted_at');
        } if ($value == 2) {
            return $this->withTrashed();
        } else {
            return $this->withTrashed()->whereNull('deleted_at');
        }
    }

    public function deliveryNoteIds($ids){
        return $this->whereIn('delivery_note_id', $ids);
    }

    public function orderDesc($field){
        return $this->orderByDesc($field);
    }

    public function order($field){
        return $this->orderBy($field);
    }


    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
}
