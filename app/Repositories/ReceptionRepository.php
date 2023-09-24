<?php

namespace App\Repositories;

use App\Models\PendingTask;
use App\Models\Reception;
use Illuminate\Support\Facades\Auth;

class ReceptionRepository extends Repository
{
    protected $userRepository;
    protected $vehiclePictureRepository;
    protected $vehicleRepository;
    public function __construct(
        UserRepository $userRepository,
        VehiclePictureRepository $vehiclePictureRepository,
        VehicleRepository $vehicleRepository,
    ) {
        $this->userRepository = $userRepository;
        $this->vehiclePictureRepository = $vehiclePictureRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function index($request)
    {
        ini_set("memory_limit", "-1");
        return Reception::with($this->getWiths($request->with))
            ->filter($request->all())
            ->orderBy('vehicle_id')
            ->paginate($request->input('per_page'));
    }

    public function create($request)
    {
        if ($request->input('trash_reception')) {
            $pending_tasks = PendingTask::where('reception_id', $request->input('trash_reception'))->get();
            if (count($pending_tasks) === 0) {
                Reception::where('id', $request->input('trash_reception'))->delete();
            }
        }

        $reception = new Reception();
        $campa = Auth::user()->campas->first();
        if (!is_null($campa)) {
            $reception->campa_id = $campa->id;
        }
        $reception->vehicle_id = $request->input('vehicle_id');
        $reception->finished = false;
        $reception->has_accessories = false;

        $reception->save();
        if (is_null($reception->vehicle->campa_id)) {
            $reception->vehicle->campa_id = $campa->campa_id;
            $reception->vehicle->save();
        }
    }

    public function getById($id)
    {
        $reception = Reception::with(['vehicle.vehicleModel.brand', 'vehicle.subState.state'])
            ->findOrFail($id);
        return ['reception' => $reception];
    }

    public function lastReception($vehicle_id)
    {
        return Reception::where('vehicle_id', $vehicle_id)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function update($reception_id)
    {
        $reception = Reception::findOrFail($reception_id);
        $reception->has_accessories = true;
        $reception->save();
        return $reception;
    }

    public function updateReception($request, $id)
    {
        $reception = Reception::findOrFail($id);
        $reception->update($request->all());
        return $reception;
    }
}
