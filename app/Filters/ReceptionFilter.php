<?php

namespace App\Filters;

use EloquentFilter\ModelFilter;

class ReceptionFilter extends ModelFilter
{

    public function campaIds($ids)
    {
        return $this->whereIn('campa_id', $ids);
    }

    public function subStatesIds($ids)
    {
        return $this->whereIn('sub_state_id', $ids);
    }

    public function vehicleIds($ids)
    {
        return $this->whereIn('vehicle_id', $ids);
    }

    public function subStatesNotIds($ids)
    {
        return $this->bySubStatesNotIds($ids);
    }

    public function vehiclePlate($plate)
    {
        return $this->whereHas('vehicle', function ($query) use ($plate) {
            return $query->where('plate', 'LIKE', "%$plate%");
        });
    }

    public function finished($value)
    {
        return $this->where('finished', $value);
    }

    public function createdAt($date)
    {
        return $this->whereDate('created_at', $date);
    }

    public function createdAtFrom($dateTime)
    {
        return $this->where('receptions.created_at','>=', $dateTime);
    }

    public function createdAtTo($dateTime)
    {
        return $this->where('receptions.created_at','<=', $dateTime);
    }

    public function brandIds($ids)
    {
        return $this->whereHas('vehicle.vehicleModel', function ($query) use ($ids) {
            return $query->whereIn('brand_id', $ids);
        });
    }

    public function typeModelOrderIds($ids)
    {
        return $this->whereIn('type_model_order_id', $ids);
    }

    public function vehicleDeleted($idDelete)
    {
        return $this->whereHas('vehicle', function ($query) use ($idDelete) {
            return $idDelete ? $query->whereNotNull('deleted_at') : $query->whereNull('deleted_at');
        });
    }


    public function orderDesc($field)
    {
        return $this->orderByDesc($field);
    }

    public function order($field)
    {
        return $this->orderBy($field);
    }

    public function whereHasVehicle($value)
    {
        return $this->whereHas('vehicle');
    }


    /**
     * Related Models that have ModelFilters as well as the method on the ModelFilter
     * As [relationMethod => [input_key1, input_key2]].
     *
     * @var array
     */
    public $relations = [];
}
