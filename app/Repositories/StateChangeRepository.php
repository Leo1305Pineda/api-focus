<?php

namespace App\Repositories;

use App\Models\PendingTask;
use App\Models\SubStateChangeHistory;
use App\Models\StateChange;
use App\Models\SubState;
use App\Models\StatePendingTask;
use App\Models\Task;
use App\Models\TypeModelOrder;
use App\Models\Vehicle;
use Carbon\Carbon;
use DateTime;

class StateChangeRepository extends Repository
{

    public function updateSubStateVehicle($vehicle, $param_sub_state_id = null, $force_sub_state_id = null)
    {
        $vehicle = Vehicle::find($vehicle->id);
        $sub_state_id = $vehicle->sub_state_id;

        if (!is_null($vehicle) && $sub_state_id !== SubState::DEFLEETED && $sub_state_id !== SubState::WORKSHOP_EXTERNAL) {
            $approvedPendingTasks = $vehicle->lastReception->approvedPendingTasks;
            $count = count($approvedPendingTasks);
            if ($count === 0) {
                $pendingTasks = PendingTask::where('vehicle_id', $vehicle->id)
                    ->where('reception_id', $vehicle->lastReception?->id)
                    ->where('approved', 1)
                    ->where('task_id', Task::TOALQUILADO)
                    ->where('state_pending_task_id', StatePendingTask::FINISHED)
                    ->get();

                if (count($pendingTasks) > 0) {
                    $sub_state_id = SubState::ALQUILADO;
                } else if ($sub_state_id != SubState::ALQUILADO) {
                    $ald_sub_state_id = count($vehicle->lastReception->pendingTasks) === 0 ? SubState::CHECK_RELEASE : SubState::CAMPA;
                    $sub_state_type_model_order = $vehicle->type_model_order_id === TypeModelOrder::ALDFLEX ? $ald_sub_state_id : SubState::CAMPA;
                    $sub_state_id = $param_sub_state_id ?? $sub_state_type_model_order;
                }
            } else {
                $pendingTask = $approvedPendingTasks[0];
                $sub_state_id = $pendingTask->task->sub_state_id;
                if ($sub_state_id === SubState::ALQUILADO && $pendingTask->state_pending_task_id === StatePendingTask::FINISHED) {
                    $sub_state_id = SubState::ALQUILADO;
                } else if (!is_null($vehicle->lastReception->lastQuestionnaire) && is_null($vehicle->lastReception->lastQuestionnaire?->datetime_approved)) {
                    $sub_state_id = SubState::CHECK;
                }
            }
        } else if ($sub_state_id == SubState::DEFLEETED) {
            $approvedPendingTasks = $vehicle->lastReception->approvedPendingTasks;
            $count = count($approvedPendingTasks);
            if ($count > 0) {
                foreach ($approvedPendingTasks as $key => $pendingTask) {
                    if ($param_sub_state_id === SubState::ALQUILADO) {
                        $pendingTask->state_pending_task_id = StatePendingTask::FINISHED;
                        $pendingTask->save();
                    }
                }
                if ($param_sub_state_id === SubState::ALQUILADO) {
                    $reception = $pendingTask->reception;
                    if ($reception && $vehicle->type_model_order_id) {
                        $reception->type_model_order_id = $vehicle->type_model_order_id;
                        $reception->save();
                    }
                } elseif ($param_sub_state_id === SubState::CHECK){
                    $sub_state_id = $param_sub_state_id;
                }
            }
        }

        $currentPendingTask = null;
        $lastPendingTask = null;
        $approvedPendingTasks = $vehicle->lastReception->approvedPendingTasks;
        $count = count($approvedPendingTasks);
        if ($count > 0) {
            if ($vehicle->sub_state_id !== SubState::DEFLEETED) {
                $pendingTask = $vehicle->lastReception->lastTaskWithState;
                if (!is_null($pendingTask)) {
                    $pendingTask->last_change_state = $vehicle->lastReception->lastChangeState?->last_change_state;
                    $pendingTask->last_change_sub_state = $vehicle->lastReception->lastChangeSubState?->last_change_sub_state;

                    if ($vehicle->subState?->state_id != $pendingTask->task->subState->state_id || is_null($pendingTask->last_change_state)) {
                        $pendingTask->last_change_state = Carbon::now();
                    }

                    if ($vehicle->sub_state_id != $pendingTask->task->sub_state_id || is_null($pendingTask->last_change_sub_state)) {
                        $pendingTask->last_change_sub_state = Carbon::now();
                    }

                    $pendingTask->save();
                }
            }
            $currentPendingTask = $approvedPendingTasks[$count > 1 ? 1 : 0];
            $lastPendingTask =  $approvedPendingTasks[0];
            $this->createOrUpdate($vehicle, $lastPendingTask, $currentPendingTask);
        }

        if ($force_sub_state_id) {
            $sub_state_id = $force_sub_state_id;
        }

        if ($sub_state_id != $vehicle->sub_state_id) {
            $vehicle->last_change_sub_state = Carbon::now();
        }

        $subState = SubState::find($sub_state_id);

        $state_id = $subState?->state_id ?? null;
        if ($state_id != $vehicle->sub_state?->state_id) {
            $vehicle->last_change_state = Carbon::now();
        }

        $vehicle->sub_state_id = $sub_state_id;
        $vehicle->save();

        if ($vehicle->lastReception) {
            $vehicle->lastReception->update([
                'remote_id' => $vehicle->remote_id,
                'company_id' => $vehicle->company_id,
                "campa_id" => $vehicle->campa_id,
                'category_id' => $vehicle->category_id,
                'sub_state_id' => $vehicle->sub_state_id,
                'plate' => $vehicle->plate,
                'vehicle_model_id' => $vehicle->vehicle_model_id,
                'type_model_order_id' => $vehicle->type_model_order_id,
                'kms' => $vehicle->kms,
                'next_itv' => $vehicle->next_itv,
                'has_environment_label' => $vehicle->has_environment_label,
                'observations' => $vehicle->observations,
                'priority' => $vehicle->priority,
                'version' => $vehicle->version,
                'vin' => $vehicle->vin,
                'first_plate' => $vehicle->first_plate,
                'latitude' => $vehicle->latitude,
                'longitude' => $vehicle->longitude,
                'trade_state_id' => $vehicle->trade_state_id,
                'documentation' => $vehicle->documentation,
                'ready_to_delivery' => $vehicle->ready_to_delivery,
                'deleted_user_id' => $vehicle->deleted_user_id,
                'seater' => $vehicle->seater
            ]);
        }

        $this->store($vehicle->id, $vehicle->sub_state_id);
        return $vehicle;
    }


    public function createOrUpdate($vehicle, $lastPendingTask, $currentPendingTask)
    {
        $stateChange = StateChange::where('vehicle_id', $vehicle->id)
            ->where('sub_state_id', $lastPendingTask?->task?->sub_state_id)
            ->whereNull('datetime_finish_sub_state')
            ->orderBy('id', 'desc')
            ->first();
        if ($stateChange && $currentPendingTask != null && $stateChange->sub_state_id != $currentPendingTask?->task?->sub_state_id) {
            $stateChange->update([
                'datetime_finish_sub_state' => date('Y-m-d H:i:s'),
                'total_time' => $stateChange->total_time + $this->diffDateTimes($stateChange->created_at)
            ]);
            StateChange::create([
                'campa_id' => $vehicle->campa_id ?? null,
                'vehicle_id' => $vehicle->id,
                'pending_task_id' => $currentPendingTask->id,
                'sub_state_id' => $currentPendingTask == null ? SubState::CAMPA : $currentPendingTask->task->sub_state_id,
            ]);
            return;
        }
        if ($currentPendingTask == null) {
            StateChange::create([
                'campa_id' => $vehicle->campa_id ?? null,
                'vehicle_id' => $vehicle->id,
                'pending_task_id' => null,
                'sub_state_id' => SubState::CAMPA,
            ]);
            return;
        }
        if (!$stateChange && $currentPendingTask != null) {
            StateChange::create([
                'campa_id' => $vehicle->campa_id ?? null,
                'vehicle_id' => $vehicle->id,
                'pending_task_id' => $currentPendingTask->id ?? null,
                'sub_state_id' => $currentPendingTask->task->sub_state_id ?? null,
            ]);
            return;
        }
    }

    private function diffDateTimes($datetime)
    {
        $datetime1 = new DateTime($datetime);
        $diference = date_diff($datetime1, new DateTime());
        $minutes = $diference->days * 24 * 60;
        $minutes += $diference->h * 60;
        $minutes += $diference->i;
        return $minutes;
    }



    public function store($vehicleId, $subStateId)
    {
        $subStateChangeHistory = SubStateChangeHistory::where('vehicle_id', $vehicleId)
            ->orderBy('id', 'DESC')
            ->first();
        if ($subStateChangeHistory && $subStateChangeHistory->sub_state_id != $subStateId) {
            SubStateChangeHistory::create([
                'vehicle_id' => $vehicleId,
                'sub_state_id' => $subStateId
            ]);
        }
    }
}
