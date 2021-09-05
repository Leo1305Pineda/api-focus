<?php

namespace App\Filters;

use EloquentFilter\ModelFilter;

class CampaFilter extends ModelFilter
{
    public function companies($ids){
        return $this->byCompanies($ids);
    }

    public function provinces($ids){
        return $this->byProvinces($ids);
    }

    public function regions($ids){
        return $this->byRegions($ids);
    }
}