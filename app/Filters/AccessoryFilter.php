<?php 

namespace App\Filters;

use EloquentFilter\ModelFilter;
use Illuminate\Database\Eloquent\Builder;

class AccessoryFilter extends ModelFilter
{

    public function ids($ids){
        return $this->byIds($ids);
    }

    public function accessoryTypeIds($ids){
        return $this->whereIn('accessory_type_id', $ids);
    }

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
}
