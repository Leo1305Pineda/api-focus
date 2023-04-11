<?php

namespace App\Repositories;

use App\Models\Campa;
use App\Models\CampaTypeModelOrder;

class CampaRepository extends Repository {

    public function __construct()
    {

    }

    public function index($request){
        return Campa::with($this->getWiths($request->with))
                ->filter($request->all())
                ->get();
    }

    public function show($request, $id){
        return Campa::with($this->getWiths($request->with))
                ->findOrFail($id);
    }

    public function getByName($name){
        return Campa::where('name', $name)
                    ->first();
    }

    public function create($request){
        $campa = Campa::create($request->all());
        $campaTypeModelOrder = [];
        foreach ($request->input('type_model_orders', []) as $key => $type_model_order_id) {
            $campaTypeModelOrder[] = [
                'campa_id' => $campa->id,
                'type_model_order_id' => $type_model_order_id
            ];
        }
        $campa->campaTypeModelOrders()->createMany($campaTypeModelOrder);
        return ["campa" => $campa];
    }

    public function update($request, $id){
        $campa = Campa::findOrFail($id);
        $campa->update($request->all());
        CampaTypeModelOrder::where('campa_id', $id)->delete();
        foreach ($request->input('type_model_orders', []) as $key => $type_model_order_id) {
            CampaTypeModelOrder::create([
                'campa_id' => $id,
                'type_model_order_id' => $type_model_order_id
            ]);
        }
        return ["campa" => $campa];
    }

    public function delete($id){
        $campa = Campa::find($id);
        if(!empty($campa)){
            $campa->delete();
            return [ 'message' => 'Campa deleted' ];
        } else {
            return [ 'message' => 'iMPOSSIBLE DELETE'];
        }
    }
}
