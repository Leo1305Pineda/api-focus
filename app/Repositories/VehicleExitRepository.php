<?php

namespace App\Repositories;

use App\Models\PendingTask;
use App\Models\StatePendingTask;
use App\Models\Task;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleExit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class VehicleExitRepository extends Repository
{

    // public function getAll($request)
    // {
    //     return VehicleExit::with($this->getWiths($request->with))
    //         ->filter($request->all())
    //         ->orderByDesc('created_at')
    //         ->paginate($request->input('per_page'));
    // }

    public function getAll($request)
    {
        $perPage = $request->input('per_page');
        $query = VehicleExit::with($this->getWiths($request->with))
            ->filter($request->all())
            ->orderBy('created_at', 'DESC');
    
        if ($perPage) {
            return $query->paginate($perPage);
        } else {
            return $query->get();
        }
    }

    public function getById($request, $id)
    {
        return VehicleExit::with($this->getWiths($request->with))
            ->findOrFail($id);
    }

    public function create($request)
    {
        $vehicleExit = VehicleExit::create($request->all());
        $vehicleExit->save();
        return $vehicleExit;
    }

    public function update($request, $id)
    {
        $vehicleExit = VehicleExit::findOrFail($id);
        $vehicleExit->update($request->all());
        return $vehicleExit;
    }

    public function registerExit($vehicle_id, $deliveryNoteId, $campaId)
    {
        $user = User::findOrFail(Auth::id());
        $vehicle = Vehicle::findOrFail($vehicle_id);
        $vehicleExit = new VehicleExit();
        $pending_task = PendingTask::updateOrCreate([
            'vehicle_id' => $vehicle_id,
            'reception_id' => $vehicle->lastReception->id ?? null,
            'task_id' => Task::WORKSHOP_EXTERNAL,
        ], [
            'state_pending_task_id' => StatePendingTask::FINISHED,
            'user_id' => Auth::id(),
            'user_start_id' => Auth::id(),
            'order' => 1,
            'approved' => true,
            'datetime_pending' => Carbon::now(),
            'datetime_start' => Carbon::now(),
            'campa_id' => $vehicle->campa_id
        ]);
        $vehicleExit->pending_task_id = $pending_task->id;
        $vehicleExit->vehicle_id = $vehicle_id;
        $vehicleExit->campa_id = $campaId;
        $vehicleExit->delivery_note_id = $deliveryNoteId;
        $vehicleExit->delivery_by = $user->name;
        $vehicleExit->date_delivery = date('Y-m-d');
        $vehicleExit->save();
    }
}
