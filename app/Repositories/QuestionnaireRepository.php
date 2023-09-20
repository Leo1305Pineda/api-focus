<?php

namespace App\Repositories;

use App\Mail\NotificationDAMail;
use App\Models\PendingTask;
use App\Models\Questionnaire;
use App\Models\StatePendingTask;
use App\Models\Task;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionnaireRepository extends Repository
{

    protected $stateChangeRepository;
    protected  $notificationDAMail;
    
    public function __construct(
        StateChangeRepository $stateChangeRepository,
        NotificationDAMail $notificationDAMail
    ) {
        $this->stateChangeRepository = $stateChangeRepository;
        $this->notificationDAMail = $notificationDAMail;
    }

    public function index($request)
    {
        return Questionnaire::with($this->getWiths($request->with))
            ->whereRaw('vehicle_id NOT IN(SELECT id FROM vehicles WHERE deleted_at is not null)')
            ->filter($request->all())
            ->paginate($request->input('per_page'));
    }

    public function create($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        return  Questionnaire::create([
            'user_id' => Auth::id(),
            'vehicle_id' => $vehicle->id,
            'reception_id' =>  $vehicle->lastReception->id
        ]);
    }

    public function getById($request, $id)
    {
        return Questionnaire::with($this->getWiths($request->with))
            ->findOrFail($id);
    }

    public function approved($request)
    {
        try {
            DB::beginTransaction();
            $data = collect([]);
            $user = Auth::user();
            $questionnaire = Questionnaire::with('user')->findOrFail($request->input('questionnaire_id'));
            $vehicle = $questionnaire->vehicle;

            if (!is_null($questionnaire->datetime_approved)) {
                $data['message'] = 'Currently Approved';
            } else {

                $questionnaire->datetime_approved = Carbon::now();
                $questionnaire->save();

                $data_update =  [
                    'state_pending_task_id' => StatePendingTask::FINISHED,
                    'user_id' => Auth::id(),
                    'user_end_id' => Auth::id(),
                    'duration' => 0,
                    'approved' => true,
                    'datetime_finish' => Carbon::now(),
                    'campa_id' => $vehicle->campa_id,
                    'order' => -1
                ];
                $pendingTask = PendingTask::updateOrCreate([
                    'reception_id' => $vehicle->lastReception->id,
                    'task_id' => Task::VALIDATE_CHECKLIST,
                    'vehicle_id' => $vehicle->id
                ], $data_update);
                if (is_null($pendingTask->datetime_pending)) {
                    $pendingTask->datetime_pending = Carbon::now();
                }
                if (is_null($pendingTask->datetime_start)) {
                    $pendingTask->user_start_id = Auth::id();
                    $pendingTask->datetime_start = Carbon::now();
                }
                $pendingTask->save();
                $pendingTask = PendingTask::updateOrCreate([
                    'reception_id' => $vehicle->lastReception->id,
                    'task_id' => Task::TOCAMPA,
                    'vehicle_id' => $vehicle->id
                ], $data_update);
                if (is_null($pendingTask->datetime_pending)) {
                    $pendingTask->datetime_pending = Carbon::now();
                }
                if (is_null($pendingTask->datetime_start)) {
                    $pendingTask->datetime_start = Carbon::now();
                }
                $pendingTask->save();
                $count = count($vehicle->lastReception->approvedPendingTasks);
                if ($count > 0) {
                    $pendingtTask = PendingTask::findOrFail($vehicle->lastReception->approvedPendingTasks[0]->id);
                    $pendingtTask->state_pending_task_id = StatePendingTask::PENDING;
                    $pendingtTask->datetime_pending = Carbon::now();
                    $pendingtTask->save();
                }

                if (is_null($vehicle->company_id)) {
                    $vehicle->company_id = $user->company_id;
                    $vehicle->save();
                }
                if ($vehicle->has_environment_label == false) {
                    $this->notificationDAMail->build($vehicle->id);
                }
                $vehicle = $this->stateChangeRepository->updateSubStateVehicle($vehicle);

                $data['message'] = 'Solicitud aprobada!';
            }

            DB::commit();
            $data['questionnaire'] = $questionnaire;
            $data['user'] = $user;
            $data['vehicle'] = $vehicle;
            $createdAt = Carbon::parse($questionnaire->datetime_approved);
            $data['title'] = 'Vehiculo ' . $vehicle->plate . ' con check list aprobado por el usuario ' . $user->name . ' El dia ' . $createdAt;
            return $data;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }
    }
}
