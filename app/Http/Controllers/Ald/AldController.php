<?php

namespace App\Http\Controllers\Ald;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PendingTask;
use App\Models\StatePendingTask;
use App\Models\SubState;
use App\Models\Task;
use App\Models\Vehicle;
use App\Repositories\SquareRepository;
use App\Repositories\StateChangeRepository;
use App\Repositories\TaskRepository;
use App\Repositories\VehicleRepository;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class AldController extends Controller
{

    protected $taskRepository;
    protected $stateChangeRepository;
    protected $vehicleRepository;
    protected $squareRepository;

    public function __construct(
        TaskRepository $taskRepository,
        StateChangeRepository $stateChangeRepository,
        VehicleRepository $vehicleRepository,
        SquareRepository $squareRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->stateChangeRepository = $stateChangeRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->squareRepository = $squareRepository;
    }

    public function unapprovedTask(Request $request)
    {
        try {
            return Vehicle::with($this->getWiths($request->with))
                ->whereHas('lastQuestionnaire', function (Builder $builder) {
                    return $builder->whereNull('datetime_approved');
                })
                ->whereHas('campa', function (Builder $builder) {
                    return $builder->where('company_id', Company::ALD);
                })
                ->filter($request->all())
                ->paginate($request->input('per_page'));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function approvedTask(Request $request)
    {
        try {
            return Vehicle::with($this->getWiths($request->with))
                ->whereHas('lastQuestionnaire', function (Builder $builder) {
                    return $builder->whereNotNull('datetime_approved');
                })
                ->whereHas('campa', function (Builder $builder) {
                    return $builder->where('company_id', Company::ALD);
                })
                ->filter($request->all())
                ->paginate($request->input('per_page'));
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function createTaskVehiclesAvalible(Request $request)
    {
        try {
            $this->vehicleRepository->newReception($request->input('vehicle_id'), null,$request->input('campa_id') );
            $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));
            if (!!$request->input('return_workshop_external')) {
                $vehicle->sub_state_id = null;
                $vehicle->save();
                PendingTask::updateOrCreate([
                    'vehicle_id' => $vehicle->id,
                    'reception_id' => $vehicle->lastReception->id ?? null,
                    'task_id' => Task::WORKSHOP_EXTERNAL
                ], [
                    'state_pending_task_id' => StatePendingTask::FINISHED,
                    'user_end_id' => Auth::id(),
                    'order' => -1,
                    'datetime_pending' => Carbon::now(),
                    'datetime_start' => Carbon::now(),
                    'datetime_finish' =>  Carbon::now(),
                    'campa_id' => $vehicle->campa_id
                ]);
                    // 'approved' => Task::find(Task::WORKSHOP_EXTERNAL)->cost
            }
            $pending_task = new PendingTask();
            $pending_task->campa_id = $vehicle->campa_id;

            $tasksApproved = count($vehicle->lastReception->approvedPendingTasks);

            if ($tasksApproved == 0) {
                $pending_task->order = 1;
                $pending_task->state_pending_task_id = StatePendingTask::PENDING;
                $pending_task->datetime_pending = date('Y-m-d H:i:s');
            } else {
                $pending_task->order = $tasksApproved + 1;
            }

            $pending_task->user_id = Auth::id();
            $pending_task->vehicle_id = $vehicle->id;

            $pending_task->reception_id = $vehicle->lastReception->id;

            if (is_null($vehicle->campa_id) && $vehicle->lastReception->campa) {
                $vehicle->campa_id = $vehicle->lastReception->campa->id;
                $vehicle->save();
            }
            $pending_task->task_id = $request->input('task_id');
            if ($pending_task->task_id === Task::CHECK_RELEASE) {
                $reception = $vehicle->lastResception;
                $reception->created_at = date('Y-m-d H:i:s');
                $reception->updated_at = date('Y-m-d H:i:s');
                $reception->save();
            }
            $taskDescription = $this->taskRepository->getById([], $pending_task->task_id);
            $pending_task->duration = $taskDescription['duration'];
            $pending_task->approved = true;
            // $pending_task->approved = Task::find($request->input('task_id'))->cost;

            if ($request->input('state_pending_task_id') == StatePendingTask::FINISHED) {
                $pending_task->state_pending_task_id = StatePendingTask::FINISHED;
                $pending_task->datetime_pending = date('Y-m-d H:i:s');
                $pending_task->datetime_start = date('Y-m-d H:i:s');
                $pending_task->datetime_finish = date('Y-m-d H:i:s');
                $pending_task->user_start_id = Auth::id();
                $pending_task->user_end_id = Auth::id();
                $pending_task->order = -1;
            }

            if ($request->input('commentary')) {
                $pending_task->comment_state = $request->input('commentary');
            }

            $pending_task->save();
            $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));

            $is_pending_task = false;
            $order = 1;

            foreach ($vehicle->lastReception->approvedPendingTasks as $update_pending_task) {
                if (!is_null($update_pending_task->state_pending_task_id)) {
                    $is_pending_task = true;
                }
                if (!$is_pending_task) {
                    $is_pending_task = true;
                    $update_pending_task->state_pending_task_id = StatePendingTask::PENDING;
                    $update_pending_task->datetime_pending = date('Y-m-d H:i:s');
                }
                $update_pending_task->order = $order;
                $update_pending_task->save();
                $order++;
            }
            $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));

            if (count($vehicle->lastReception->approvedPendingTasks) > 0 && $vehicle->lastReception->approvedPendingTasks[0]->task_id == Task::CHECK_BLOCKED) {
                $approvedPendingTask = $vehicle->lastReception->approvedPendingTasks[0];
                $approvedPendingTask->user_start_id = Auth::id();
                $approvedPendingTask->state_pending_task_id = StatePendingTask::IN_PROGRESS;
                $approvedPendingTask->datetime_start = date('Y-m-d H:i:s');
                $approvedPendingTask->save();

                $vehicle->sub_state_id = SubState::CHECK_BLOCKED;
                $vehicle->save();
            } else {
                $this->stateChangeRepository->updateSubStateVehicle($vehicle);
            }
            if ($request->input('square_id')) {
                $this->squareRepository->update($request, $request->input('square_id'));
            }

            return $this->createDataResponse([
                'data' => $vehicle->lastReception->approvedPendingTasks,
                'vehicle' => Vehicle::find($vehicle->id)
            ], HttpFoundationResponse::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage(),], 400);
        }
    }
}
