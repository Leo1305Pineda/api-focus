<?php

namespace App\Repositories;

use App\Mail\NotificationDAMail;
use App\Mail\NotificationItvMail;
use App\Models\PendingTask;
use App\Models\QuestionAnswer;
use App\Models\Questionnaire;
use App\Models\Role;
use App\Models\StatePendingTask;
use App\Repositories\StateChangeRepository;
use App\Models\SubState;
use App\Models\Task;
use App\Models\TypeModelOrder;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionAnswerRepository
{

    public function __construct(
        TaskRepository $taskRepository,
        QuestionnaireRepository $questionnaireRepository,
        ReceptionRepository $receptionRepository,
        PendingTaskRepository $pendingTaskRepository,
        VehicleRepository $vehicleRepository,
        NotificationDAMail $notificationDAMail,
        NotificationItvMail $notificationItvMail,
        SquareRepository $squareRepository,
        StateChangeRepository $stateChangeRepository,
        VehiclePictureRepository $vehiclePictureRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->questionnaireRepository = $questionnaireRepository;
        $this->receptionRepository = $receptionRepository;
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->vehicleRepository = $vehicleRepository;
        $this->notificationDAMail = $notificationDAMail;
        $this->notificationItvMail = $notificationItvMail;
        $this->stateChangeRepository = $stateChangeRepository;
        $this->squareRepository = $squareRepository;
        $this->vehiclePictureRepository = $vehiclePictureRepository;
    }

    public function create($request)
    {
        try {
            DB::beginTransaction();

            $questionnaire = null;
            $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));
            $defleetAndDelivery = Vehicle::where('id',$request->input('vehicle_id'))->filter(['defleetAndDelivery'=>0])->exists();
            if (!is_null($vehicle->lastReception->lastQuestionnaire) && $vehicle->sub_state_id !== SubState::ALQUILADO &&  !$defleetAndDelivery) {
                return [
                    'questionnaire' => $vehicle->lastReception->lastQuestionnaire,
                    'message'=>'1'
                ];
            }


            $user = Auth::user();

            $vehicle->campa_id = $user['campas'][0]['id'];
            $vehicle->save();

            $this->vehicleRepository->newReception($request->input('vehicle_id'));

            $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));

            if ($vehicle->type_model_order_id === TypeModelOrder::ALDFLEX) {
                $questionnaire = $this->questionnaireRepository->create($vehicle->id);
                $questionnaire->reception_id = $vehicle->lastReception->id;
                $questionnaire->save();
                $questions = $request->input('questions');
                $has_environment_label = $vehicle->has_environment_label;
                foreach ($questions as $question) {
                    $questionAnswer = new QuestionAnswer();
                    $questionAnswer->questionnaire_id = $questionnaire['id'];
                    $questionAnswer->question_id = $question['question_id'];
                    $questionAnswer->task_id = $question['task_id'];
                    $questionAnswer->response = $question['response'];
                    $questionAnswer->description = $question['description'] ?? '';
                    $questionAnswer->save();
                    if ($questionAnswer->question_id === 9) {
                        $has_environment_label = $question['response'];
                    }

                    if ($questionAnswer->question_id === 4 && $question['response'] == 1) {
                        $this->notificationItvMail->build($request->input('vehicle_id'));
                    }
                }

                $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));

                $pendingTasks = $vehicle->lastReception->pendingTasks ?? null;
                if ($pendingTasks) {
                    foreach ($pendingTasks as $key => $pending_task) {
                        $pending_task->state_pending_task_id = StatePendingTask::FINISHED;
                        $pending_task->order = -1;
                        if (is_null($pending_task->datetime_pending)) {
                            $pending_task->datetime_pending = date('Y-m-d H:i:s');
                        }
                        if (is_null($pending_task->datetime_start)) {
                            $pending_task->datetime_start = date('Y-m-d H:i:s');
                        }
                        if (is_null($pending_task->datetime_finish)) {
                            $pending_task->datetime_finish = date('Y-m-d H:i:s');
                        }
                        if (is_null($pending_task->user_start_id)) {
                            $pending_task->user_start_id = Auth::id();
                        }
                        if (is_null($pending_task->user_end_id)) {
                            $pending_task->user_end_id = Auth::id();
                        }
                        $pending_task->save();
                    }
                }

                $tasks = $request->input('tasks');
                $vehicleId = $request->input('vehicle_id');

                $user = User::where('role_id', Role::CONTROL)
                    ->first();
                $vehicle = Vehicle::findOrFail($vehicleId);
                $isPendingTaskAssign = false;
                $order = 1;

                foreach ($tasks as $task) {
                    $pending_task = new PendingTask();
                    $pending_task->vehicle_id = $vehicleId;
                    $pending_task->reception_id = $vehicle->lastReception->id;
                    $pending_task->campa_id = $vehicle->campa_id;
                    $taskDescription = $this->taskRepository->getById([], $task['task_id']);
                    $pending_task->task_id = $task['task_id'];
                    $pending_task->approved = $task['approved'];
                    $pending_task->created_from_checklist = true;

                    $question_answer = QuestionAnswer::where('task_id', $task['task_id'])
                        ->where('questionnaire_id', $questionnaire->id)->first();
                    if (!is_null($question_answer)) {
                        $pending_task->question_answer_id = $question_answer->id;
                    }
                    if ($task['approved'] == true && $isPendingTaskAssign == false) {
                        if (!isset($task['without_state_pending_task'])) {
                            $pending_task->state_pending_task_id = StatePendingTask::PENDING;
                        }
                        $pending_task->datetime_pending = date('Y-m-d H:i:s');
                        $isPendingTaskAssign = true;
                    }
                    $pending_task->user_id = $user->id;
                    $pending_task->duration = $taskDescription['duration'];
                    if ($task['approved'] == true) {
                        $pending_task->order = $order;
                        $order++;
                    }
                    $pending_task->save();
                }

                $this->vehicleRepository->updateBack($request);

                $vehicle = Vehicle::find($request->input('vehicle_id'));
                $vehicle->datetime_defleeting = $vehicle->sub_state_id === SubState::DEFLEETED ? null : $vehicle->datetime_defleeting;
                $vehicle->has_environment_label = $has_environment_label;
                $vehicle->save();
                $param_sub_state_id = $vehicle->sub_state_id === SubState::DEFLEETED ?  SubState::CHECK :  null ;
                $this->stateChangeRepository->updateSubStateVehicle($vehicle, $param_sub_state_id);
            } else if ($vehicle->lastReception) {
                $vehicle->lastReception->created_at = date('Y-m-d H:i:s');
                $vehicle->lastReception->updated_at = date('Y-m-d H:i:s');
                $vehicle->lastReception->save();
                //$sub_state_id = $vehicle->sub_state_id === SubState::DEFLEETED ? $vehicle->sub_state_id : SubState::CAMPA;
                $sub_state_id = SubState::CAMPA;
                $this->stateChangeRepository->updateSubStateVehicle($vehicle, null, $sub_state_id);
            }

            $pictures = $request->input('pictures') ?? [];

            foreach ($pictures as $url) {
                $this->vehiclePictureRepository->create([
                    'vehicle_id' => $vehicle->id,
                    'place' => 'Recepción',
                    'url' => $url,
                    'latitude' => $vehicle->latitude,
                    'longitude' => $vehicle->longitude
                ]);
            }

            if ($request->input('square_id')) {
                $this->squareRepository->update($request, $request->input('square_id'));
            }

            $pending_task = new PendingTask();
            $pending_task->vehicle_id = $vehicle->id;
            $pending_task->reception_id = $vehicle->lastReception->id;
            $pending_task->campa_id = $vehicle->campa_id;
            $pending_task->task_id = Task::RECEPTION;
            $pending_task->state_pending_task_id = StatePendingTask::FINISHED;
            $pending_task->approved = 1;
            $pending_task->created_from_checklist = true;
            $pending_task->datetime_pending = date('Y-m-d H:i:s');
            $pending_task->datetime_start = date('Y-m-d H:i:s');
            $pending_task->datetime_finish = date('Y-m-d H:i:s');
            $pending_task->user_start_id = Auth::id();
            $pending_task->user_end_id = Auth::id();
            $pending_task->user_start_id = Auth::id();
            $pending_task->user_id = Auth::id();
            $pending_task->save();

            DB::commit();
            return [
                'questionnaire' => $questionnaire,
                'message'=>'2'
            ];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw $e;
        }
    }

    public function createChecklist($request)
    {
        $questionnaire = $this->questionnaireRepository->create($request->input('vehicle_id'));
        $questions = $request->input('questions');
        foreach ($questions as $question) {
            $questionAnswer = new QuestionAnswer();
            $questionAnswer->questionnaire_id = $questionnaire['id'];
            $questionAnswer->question_id = $question['question_id'];
            $questionAnswer->response = $question['response'];
            $questionAnswer->description = $question['description'];
            $questionAnswer->save();
        }
        $questionnaireComplete = Questionnaire::with(['questionAnswers.question', 'vehicle.lastOrder'])
            ->findOrFail($questionnaire['id']);
        $client = new \GuzzleHttp\Client();
        $response = $client->post(
            'https://devgsp20.invarat.com/api/createChecklist',
            [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Zm9jdXM6aW52YXJhdGZvY3Vz'
                ],
                'body' => json_encode($questionnaireComplete)
            ]
        );
        return [
            'message' => 'Ok',
            'message_gsp' => $response
        ];
    }

    public function update($request, $id)
    {
        $questionAnswer = QuestionAnswer::findOrFail($id);
        if ($request->input('task_id')) {
            $questionnaire = Questionnaire::find($request->input('questionnaire_id'));
            $this->pendingTaskRepository->updatePendingTaskFromValidation($questionnaire->reception_id, $request->input('last_task_id'), $request->input('task_id'));
        }
        $questionAnswer->update($request->all());
        return ['question_answer' => $questionAnswer];
    }

    public function updateResponse($request, $id)
    {
        $questionAnswer = QuestionAnswer::findOrFail($id);
        $vehicle = $questionAnswer->questionnaire->vehicle;
        $questionAnswer->update($request->all());
        $pendingTask = PendingTask::where('question_answer_id', $questionAnswer->id)->first();
        if (!is_null($pendingTask)) {
            $pendingTask->approved = $questionAnswer->response;
            $pendingTask->save();
        } else if (!!$questionAnswer->response && !!$questionAnswer->task_id) {
            $pending_task = new PendingTask();
            $pending_task->question_answer_id = $questionAnswer->id;
            $pending_task->vehicle_id = $vehicle->id;
            $pending_task->reception_id = $vehicle->lastReception->id;
            $pending_task->campa_id = $vehicle->campa_id;
            $taskDescription = $this->taskRepository->getById([], $questionAnswer->task_id);
            $pending_task->task_id = $questionAnswer->task_id;
            $pending_task->duration = $taskDescription['duration'];
            $pending_task->approved = $questionAnswer->response;
            $pending_task->created_from_checklist = true;

            $count = count($vehicle->lastReception->approvedPendingTasks);

            if (!!$pending_task->approved && $count == 0) {
                $pending_task->state_pending_task_id = StatePendingTask::PENDING;
                $pending_task->datetime_pending = date('Y-m-d H:i:s');
            }
            $pending_task->user_id = Auth::id();
            if (!!$pending_task->approved) {
                $pending_task->order = $count + 1;
            }
            $pending_task->save();
            if ($pending_task->state_pending_task_id == StatePendingTask::PENDING) {
                $this->stateChangeRepository->updateSubStateVehicle($vehicle);
            }
            $pendingTask = $pending_task;
        }
        foreach ($vehicle->lastReception->defaultOrderApprovedPendingTasks as $key => $value) {
            $value->order = $key + 1;
            $value->save();
        }
        return $questionAnswer;
    }
}
