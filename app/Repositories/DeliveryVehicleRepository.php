<?php

namespace App\Repositories;

use App\Models\DeliveryVehicle;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeliveryVehicleRepository extends Repository {

    public function __construct()
    {

    }

    public function index($request){
        return DeliveryVehicle::with($this->getWiths($request->with))
            ->filter($request->all())
            ->paginate($request->input('per_page'));
    }

    public function createDeliveryVehicles($vehicleId, $data){
        $user = User::with('campas')
            ->findOrFail(Auth::id());
        DeliveryVehicle::create([
            'vehicle_id' => $vehicleId,
            'campa_id' => $user->campas[0]->id,
            'data_delivery' => json_encode($data)
        ]);
    }

}
