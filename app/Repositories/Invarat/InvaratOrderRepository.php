<?php

namespace App\Repositories\Invarat;

use App\Models\Order;
use App\Repositories\Repository;

class InvaratOrderRepository extends Repository {

    public function __construct()
    {
    }

    public function createOrder($request){
        try{

            $order = new Order();
            $order->vehicle_id = $request->input("vehicle_id");
            $order->id_gsp = $request->input("id_gsp");

            if(!$order->save()){
                throw new \Exception("Error al generar el registro orden de GSP20");
            }

            return [
                "type" => "success",
                "msg" => ""
            ];

        }catch (\Exception $e){

            return [
                "type" => "error",
                "msg" => $e->getMessage()
            ];

        }

    }

    public function orderFilter($request){
        return Order::with($this->getWiths($request->with))
                    ->filter($request->all())
                    ->paginate($request->input('per_page'));
    }

}
