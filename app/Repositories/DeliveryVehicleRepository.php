<?php

namespace App\Repositories;

use App\Models\DeliveryVehicle;
use App\Models\PendingTask;
use App\Models\StatePendingTask;
use App\Models\SubState;
use App\Models\Task;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DeliveryVehicleRepository extends Repository
{

    public function __construct(
        SquareRepository $squareRepository,
    ) {
        $this->squareRepository = $squareRepository;
    }

    public function index($request)
    {
        return DeliveryVehicle::with($this->getWiths($request->with))
            ->filter($request->all())
            ->orderBy('delivery_note_id', 'DESC')
            ->paginate($request->input('per_page'));
    }

    public function createDeliveryVehicles($vehicleId, $data, $deliveryNoteId)
    {
        $user = User::with('campas')
            ->findOrFail(Auth::id());
        $vehicle = Vehicle::findOrFail($vehicleId);
        $pending_task = PendingTask::updateOrCreate([
            'vehicle_id' => $vehicleId,
            'reception_id' => $vehicle->lastReception->id ?? null,
            'task_id' => Task::TOALQUILADO
        ], [
            'state_pending_task_id' => StatePendingTask::FINISHED,
            'user_id' => Auth::id(),
            'user_start_id' => Auth::id(),
            'user_end_id' => Auth::id(),
            'order' => 1,
            'approved' => true,
            'datetime_pending' => Carbon::now(),
            'datetime_start' => Carbon::now(),
            'datetime_finish' =>  Carbon::now(),
            'campa_id' => $vehicle->campa_id
        ]);
        DeliveryVehicle::create([
            'vehicle_id' => $vehicleId,
            'campa_id' => key_exists("force_exit_from_campa",$data)  ? $data["force_exit_from_campa_id"] : $user->campas[0]->id,
            'delivery_note_id' => $deliveryNoteId,
            'data_delivery' => json_encode($data),
            'delivery_by' => $user->name,
            'pending_task_id' => $pending_task->id
        ]);
        if ($vehicle->lastReception) {
            $vehicle->lastReception->finished = true;
            $vehicle->lastReception->save();
        }
    }

    public function delete($id)
    {
        $deliveryVehicle = DeliveryVehicle::findOrFail($id);
        $user = Auth::user();
        $deliveryVehicle->canceled_by = $user->name;
        $deliveryVehicle->save();

        $pendingTask = $deliveryVehicle->pendingTask;

        if (!is_null($pendingTask)) {
            $pendingTask->state_pending_task_id = StatePendingTask::CANCELED;
            $pendingTask->save();
        }

        $vehicle = Vehicle::findOrFail($deliveryVehicle->vehicle_id);
        $vehicle->campa_id = $deliveryVehicle->campa_id;

        $vehicle->sub_state_id = SubState::CAMPA;
        $vehicle->save();

        $groupTask = $vehicle->lastReception?->groupTask;
        if (!!$groupTask) {
            $groupTask->approved_available = 1;
            $groupTask->approved = 1;
            $groupTask->save();
        }

        $deliveryVehicle->delete();
        return $deliveryVehicle;
    }
}
