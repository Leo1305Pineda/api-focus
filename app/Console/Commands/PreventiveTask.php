<?php

namespace App\Console\Commands;

use App\Models\PendingTask;
use App\Models\StatePendingTask;
use App\Models\SubState;
use App\Models\Task;
use App\Models\TypeModelOrder;
use App\Models\User;
use App\Models\Vehicle;
use App\Repositories\SquareRepository;
use App\Repositories\StateChangeRepository;
use App\Repositories\TaskRepository;
use App\Repositories\VehicleRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PreventiveTask extends Command
{
    protected $vehicleRepository;
    protected $taskRepository;
    protected $squareRepository;
    protected $stateChangeRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:preventive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear tarea automatica de mantenimiento preventico';

    /**
     * Create a new command instance.
     *
     * @return void
     */
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
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $user = User::find(1); //Admin
        $vehicles = Vehicle::byTypeModelOrderIds([
            TypeModelOrder::ALDFLEX,
            TypeModelOrder::ALDFLEX_NUEVO,
            TypeModelOrder::ALDFLEX_REACOND,
            TypeModelOrder::BIPI,
            TypeModelOrder::ALDFLEX_VN
        ])
        ->where('sub_state_id', SubState::CAMPA) //disponible
        ->whereNotNull('last_change_state')
        ->whereNotNull('last_change_sub_state')
        ->where(function($q){
            $q->where('last_change_state', '<', Carbon::now()->subMonth())
            ->orWhere('last_change_sub_state', '<', Carbon::now()->subMonth());
        })
        ->get();
        foreach($vehicles as $v){
            echo($v->plate);
            $request = new Request();
            $request->merge([
                "state_pending_task_id"=>StatePendingTask::PENDING,
                "task_id"=>Task::PREVENTIVE,
                "vehicle_id"=>$v->id
            ]);
            try {
                DB::beginTransaction();
                $this->vehicleRepository->newReception($request->input('vehicle_id'), null,$request->input('campa_id') );
                $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));

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

                $pending_task->user_id = $user->id;
                $pending_task->vehicle_id = $vehicle->id;

                $pending_task->reception_id = $vehicle->lastReception->id;

                if (is_null($vehicle->campa_id) && $vehicle->lastReception->campa) {
                    $vehicle->campa_id = $vehicle->lastReception->campa->id;
                    $vehicle->save();
                }
                $pending_task->task_id = $request->input('task_id');

                $taskDescription = $this->taskRepository->getById([], $pending_task->task_id);
                $pending_task->duration = $taskDescription['duration'];
                $pending_task->approved = true;


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
                    $approvedPendingTask->user_start_id = $user->id;
                    $approvedPendingTask->state_pending_task_id = StatePendingTask::IN_PROGRESS;
                    $approvedPendingTask->datetime_start = date('Y-m-d H:i:s');
                    $approvedPendingTask->save();

                    $vehicle->sub_state_id = SubState::CHECK_BLOCKED;
                    $vehicle->save();
                } else {
                    $this->stateChangeRepository->updateSubStateVehicle($vehicle);
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                echo($e->getMessage());
            }
        }

    }
}
