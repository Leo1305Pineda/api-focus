<?php 

namespace App\Filters;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class AccessoryVehicleFilter extends ModelFilter
{

    public function ids($ids){
        return $this->byIds($ids);
    }

    public function accessoryIds($ids){
        return $this->whereIn('accessory_id', $ids);
    }

    public function accessoryTypeIds($ids){
        return $this->whereHas('accessory.accessoryType', function(Builder $builder) use ($ids) {
            return $builder->whereIn('id', $ids);
        });
    }

    public function vehiclePlate($plate){
        return $this->whereHas('vehicle', function(Builder $builder) use ($plate) {
            return $builder->where('plate','like',"%$plate%");
        });
    }

    public function campaIds($value) {
        return $this->whereHas('vehicle', function(Builder $builder) use ($value) {
            return $builder->whereIn('campa_id', $value);
        });
    }

    public function createdAt($date)
    {
        return $this->whereDate('created_at', $date);
    }

    public function createdAtFrom($dateTime)
    {
        return $this->where('created_at','>=', $dateTime);
    }

    public function createdAtTo($dateTime)
    {
        return $this->where('created_at','<=', $dateTime);
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
