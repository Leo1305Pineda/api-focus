<?php

namespace App\Repositories;
use App\Models\GroupTask;
use App\Models\PendingTask;
use App\Models\SubState;
use App\Models\Vehicle;
use Exception;

class GroupTaskRepository extends Repository {

    public function __construct()
    {

    }

    public function getAll(){
        return GroupTask::all();
    }

    public function getById($request, $id){
        return GroupTask::with($this->getWiths($request))
                    ->findOrFail($id);
    }

    public function createWithVehicleId($vehicle_id){
        $group_task = new GroupTask();
        $group_task->vehicle_id = $vehicle_id;
        $group_task->approved = 0;
        $group_task->save();
        return $group_task;
    }

    public function create($request){
        $group_task = new GroupTask();
        $group_task->vehicle_id = $request->input('vehicle_id');
        $group_task->approved = 0;
        $group_task->save();
        return $group_task;
    }

    public function update($request, $id){
        $group_task = GroupTask::findOrFail($id);
        $group_task->update($request->all());
        return response()->json(['group_task' => $group_task], 200);
    }

    public function getLastByVehicle($vehicle_id){
        return GroupTask::where('vehicle_id', $vehicle_id)
                    ->orderBy('id', 'desc')
                    ->first();
    }

    public function approvedGroupTaskToAvailable($request){
        $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));
        $vehicle->sub_state_id = SubState::CAMPA;
        $vehicle->save();
        PendingTask::where('group_task_id', $request->input('group_task_id'))
                    ->delete();
        $group_task = GroupTask::findOrFail($request->input('group_task_id'));
        $group_task->approved_available = 1;
        $group_task->save();
        return response()->json(['message' => 'Solicitud aprobada!'], 200);
    }

    public function declineGroupTask($request){
        $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));
        $vehicle->sub_state_id = null;
        $vehicle->save();
        PendingTask::where('group_task_id', $request->input('group_task_id'))
                    ->delete();
        GroupTask::findOrFail($request->input('group_task_id'))
                    ->delete();
        return response()->json(['message' => 'Solicitud declinada!'], 200);
    }
}
