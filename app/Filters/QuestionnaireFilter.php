<?php

namespace App\Filters;

use EloquentFilter\ModelFilter;

class QuestionnaireFilter extends ModelFilter
{

    public function ids($ids){
        return $this->whereIn('id', $ids);
    }

    public function userIds($ids){
        return $this->whereIn('user_id', $ids);
    }

    public function vehicleids($ids){
        return $this->whereIn('vehicle_id', $ids);
    }

    public function orderDesc($field){
        return $this->orderByDesc($field);
    }

    public function order($field){
        return $this->orderBy($field);
    }

    public function receptionNull($value) {
        if ($value) {
            return $this->whereNull('reception_id');
        }
        return $this->whereNotNull('reception_id');
    }

    /**
     * @deprecate
     */
    public function approvedGroupTask($value) {
        return $this->whereHas('reception.groupTask', function ($query) use ($value) {
            return $query->where('approved_available', $value)->where('approved', $value);
        });
    }

    public function isApproved($value) {
        // $query = $this->whereHas('vehicle', function ($query) use ($value) {
        //     if ($value) {
        //         $query->whereNotNull('datetime_defleeting');
        //     }
        //     $query->whereNull('datetime_defleeting');
        // });
        if ($value) {
            return $this->whereNotNull('datetime_approved')
            ->whereHas('vehicle', function ($query){
                $query->whereNull('datetime_defleeting');
            });
        }
        return $this->whereNull('datetime_approved')
        ->whereRaw(DB::raw('reception_id = (Select max(r.id) from receptions r where r.vehicle_id = questionnaires.vehicle_id)'))
        ->whereHas('vehicle', function ($query){
            $query->whereNull('datetime_defleeting');
        });
    }

    public function isDefleeting($value) {
        return $this->whereHas('reception.groupTask', function ($query) use ($value) {
            if ($value) {
                return $query->whereNotNull('datetime_defleeting');
            }
            return $query->whereNull('datetime_defleeting');
        });
    }

    public function vehiclePlate($plate) {
        return $this->whereHas('vehicle', function ($query) use ($plate) {
            return $query->where('plate','like',"%$plate%");
        });
    }

    public function campaIds($value) {
        return $this->whereHas('reception', function ($query) use ($value) {
           return $query->whereIn('campa_id', $value);
        });
    }

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
}
