<?php

namespace App\Repositories\Invarat;

use App\Models\BudgetPendingTask;
use App\Models\PendingTask;
use App\Models\StatePendingTask;
use App\Models\Vehicle;
use App\Repositories\PendingTaskCanceledRepository;
use App\Repositories\Repository;
use App\Repositories\TaskRepository;
use App\Repositories\VehicleRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvaratPendingTaskRepository extends Repository {

    public function __construct(
        TaskRepository $taskRepository,
        VehicleRepository $vehicleRepository,
        PendingTaskCanceledRepository $pendingTaskCanceledRepository
    )
    {
        $this->taskRepository = $taskRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->pendingTaskCanceledRepository = $pendingTaskCanceledRepository;
    }

    /**
     * Método que trae la siguiente task de un grupo de tareas
     *
     * @param object $request
     * @return object|array
     */
    public function nextPendingTask($request){

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $pending_task = PendingTask::with('task')
            ->where('reception_id',$vehicle->lastReception->id)
            ->where('vehicle_id',$request->vehicle_id)
            ->where('order',">",$request->order)
            ->where('state_pending_task_id',"!=",StatePendingTask::FINISHED)
            ->orderBy("order", "ASC")
            ->first();

        if($pending_task){
            return $pending_task;
        }else{
            //No encuentra más tareas pendientes
            return [
                'message' => 'No hay más tareas.'
            ];
        }

    }

    /**
     * Inicia una tarea.
     *
     * @param $request
     * @return false|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|string[]|null
     */
    public function startTask($request){

        $pending_task = PendingTask::with($this->getWiths($request->with))
            ->findOrFail($request->input('pending_task_id'));

        if ($pending_task->state_pending_task_id == StatePendingTask::PENDING) {
            $pending_task->user_start_id = Auth::id();
            $pending_task->state_pending_task_id = StatePendingTask::IN_PROGRESS;
            $pending_task->datetime_start = date('Y-m-d H:i:s');

            if($pending_task->save()){

                $pending_task->load($this->getWiths($request->with));
                return $pending_task;

            }

            return false;

        } else {

            return [
                'message' => 'La tarea no está en estado pendiente'
            ];
        }

    }

    /**
     * Cancela tarea y guarda la tarea cancelada.
     *
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelTask($request)
    {
        $pending_task = PendingTask::findOrFail($request->input('pending_task_id'));
        $pending_task->state_pending_task_id = StatePendingTask::PENDING;
        $pending_task->datetime_start = null;
        $pending_task->save();

        return $this->pendingTaskCanceledRepository->create($request);

    }

    /**
     * Finalizamos una tarea, comprobamos si hay proximas como pendientes para resetear la fecha.
     *
     * @param $request
     * @return array|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|void|null
     */
    public function finishTask($request)
    {

        try {

            $pending_task = PendingTask::with($this->getWiths($request->with))->findOrFail($request->input('pending_task_id'));
            $vehicle = $pending_task->vehicle;

            if ($pending_task->state_pending_task_id == StatePendingTask::IN_PROGRESS) {

                $pending_task->state_pending_task_id = StatePendingTask::FINISHED;
                $pending_task->user_end_id = Auth::id();
                $pending_task->datetime_finish = date('Y-m-d H:i:s');

                if (count($vehicle->lastReception->approvedPendingTasks) > 0) {
                    $pending_task_next = $vehicle->lastReception->approvedPendingTasks[0];

                    if ($pending_task_next) {
                        $pending_task_next->state_pending_task_id = StatePendingTask::PENDING;
                        $pending_task_next->datetime_pending = date('Y-m-d H:i:s');
                        $pending_task_next->save();

                    }

                }

                if($pending_task->save()){

                    $pending_task->load($this->getWiths($request->with));
                    return $pending_task;

                }

                throw new \Exception("Error al finalizar la tarea, ponte en contacto con el administrador.");

            }else{

                throw new \Exception("La tarea no está en estado proceso");

            }

        }catch (\Exception $e){
             Log::debug($e->getMessage()." - ".$e->getFile()." - ".$e->getLine());
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *
     * Generamos una pending task con el siguiente orden
     *
     * @param $request
     * @return PendingTask[]|false
     */
    public function addPendingTaskReacondicionamiento($request)
    {
        $vehicle = $this->vehicleRepository->getById($request, $request->input('vehicle_id'));
        $pendingTask = PendingTask::where('reception_id', $vehicle->lastReception->id)->orderBy("order","DESC")->first();
        $task = $this->taskRepository->getById([], $request->input('task_id'));
        $pendingTask = new PendingTask();
        $pendingTask->reception_id = $vehicle->lastReception->id;
        $pendingTask->task_id = $task['id'];
        $pendingTask->campa_id = $vehicle->campa_id;
        $pendingTask->vehicle_id = $request->input('vehicle_id');
        $pendingTask->state_pending_task_id = StatePendingTask::PENDING;
        $pendingTask->duration = $task['duration'];
        $pendingTask->order = $request->input('order') != "" ? $request->input('order') :  $pendingTask->order + 1;
        $pendingTask->observations = $request->input('observations');
        $pendingTask->user_id = Auth::id();

        if(!$pendingTask->save()){

            return false;
        };

        return ['pending_task' => $pendingTask ];
    }

    /**
     * Creamos o actualimos el presupuesto de tarea pendiente.
     *
     * @param $request
     * @return mixed
     */
    public function updateOrCreateBudgetPengingTaskGtWeb($request){

        $budgetPendingTask = BudgetPendingTask::updateOrCreate(
            array(
                "pending_task_id" => $request->input("pending_task_id")
            ),
            $request->all()
        );

        return $budgetPendingTask;

    }

    /**
     *
     * Actualizar tarea pendiente
     *
     * @param $request
     * @param $id
     * @return array
     */
    public function updatePendingTaskReacondicionamiento($request)
    {
        $pending_task = PendingTask::findOrFail($request->input("id"));
        $pending_task->update($request->all());
        return $pending_task;
    }

}
