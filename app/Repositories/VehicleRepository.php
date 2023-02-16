<?php

namespace App\Repositories;

use App\Models\Damage;
use App\Models\PendingTask;
use App\Models\Reception;
use App\Models\SubState;
use App\Models\TradeState;
use App\Models\Vehicle;
use App\Models\Task;
use App\Models\StatePendingTask;
use App\Models\StatusDamage;
use App\Models\TypeModelOrder;
use DateTime;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CategoryRepository;
use App\Repositories\DefleetVariableRepository;
use App\Repositories\StateRepository;
use App\Repositories\BrandRepository;
use App\Repositories\VehicleModelRepository;
use App\Repositories\UserRepository;
use App\Repositories\TypeModelOrderRepository;
use App\Repositories\DeliveryVehicleRepository;
use App\Repositories\VehicleExitRepository;
use App\Repositories\StateChangeRepository;

use App\Repositories\CampaRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VehicleRepository extends Repository
{

    public function __construct(
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        DefleetVariableRepository $defleetVariableRepository,
        StateRepository $stateRepository,
        BrandRepository $brandRepository,
        VehicleModelRepository $vehicleModelRepository,
        TypeModelOrderRepository $typeModelOrderRepository,
        DeliveryVehicleRepository $deliveryVehicleRepository,
        VehicleExitRepository $vehicleExitRepository,
        CampaRepository $campaRepository,
        DeliveryNoteRepository $deliveryNoteRepository,
        SquareRepository $squareRepository,
        StateChangeRepository $stateChangeRepository,
        HistoryLocationRepository $historyLocationRepository,
        VehiclePictureRepository $vehiclePictureRepository
    ) {
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->defleetVariableRepository = $defleetVariableRepository;
        $this->stateRepository = $stateRepository;
        $this->brandRepository = $brandRepository;
        $this->vehicleModelRepository = $vehicleModelRepository;
        $this->campaRepository = $campaRepository;
        $this->typeModelOrderRepository = $typeModelOrderRepository;
        $this->deliveryVehicleRepository = $deliveryVehicleRepository;
        $this->vehicleExitRepository = $vehicleExitRepository;
        $this->squareRepository = $squareRepository;
        $this->deliveryNoteRepository = $deliveryNoteRepository;
        $this->stateChangeRepository = $stateChangeRepository;
        $this->historyLocationRepository = $historyLocationRepository;
        $this->vehiclePictureRepository = $vehiclePictureRepository;
    }

    public function getAll($request)
    {
        $user = $this->userRepository->getById($request, Auth::id());
        $vehicles = Vehicle::with($this->getWiths($request->with))
            ->byCampasOfUser($user->campas->pluck('id')->toArray())
            ->paginate($request->input('per_page'));
        return ['vehicles' => $vehicles];
    }

    public function getById($request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);

        if (is_null($vehicle->lastReception)) {
            $this->newReception($vehicle->id);
        }
        return Vehicle::with($this->getWiths($request->with) ?? [])
        ->filter($request->all())
        ->findOrFail($id);
    }

    public function filterVehicle($request)
    {
        $query = Vehicle::with($this->getWiths($request->with))
            ->filter($request->all());

        $query->selectRaw('vehicles.*, (SELECT MAX(r.id) FROM receptions r WHERE r.vehicle_id = vehicles.id) as reception_id')
            ->orderBy('reception_id', 'desc');

        if ($request->input('noPaginate')) {
            $vehicles = [
                'data' => $query->get()
            ];
        } else {
            $vehicles =  $query->paginate($request->input('per_page') ?? 5);
        }
        return ['vehicles' => $vehicles];
    }

    public function createFromExcel($request)
    {
        $vehicles = $request->input('vehicles');
        foreach ($vehicles as $vehicle) {
            Vehicle::updateOrCreate([
                'plate' => $vehicle['plate']
            ], $vehicle);

            $_vehicle = Vehicle::where('plate', $vehicle['plate'])->first();

            foreach ($vehicle['receptions'] as $key => $reception) {
                Reception::updateOrCreate([
                    'vehicle_id' => $_vehicle->id,
                    'created_at' => Carbon::parse($reception['created_at'])->toDateTimeString()
                ], $reception);
            }

            if (count($vehicle['accessories']) > 0) {
                DB::table('accessory_vehicle')
                ->where('vehicle_id', $_vehicle->id)
                ->delete();
            }
            foreach ($vehicle['accessories'] as $accessory) {
                $accessory['vehicle_id'] = $_vehicle->id;
                DB::table('accessory_vehicle')->insert($accessory);
            }

//           

           

            /* $existVehicle = Vehicle::where('plate', $vehicle['plate'])
                ->first();
            if ($existVehicle) {
                $category = $this->categoryRepository->searchCategoryByName($vehicle['category']);
                if ($category) $existVehicle->category_id = $category['id'];
                $brand = $vehicle['brand'] ? $this->brandRepository->getByNameFromExcel($vehicle['brand']) : null;
                $vehicle_model = $brand ? $this->vehicleModelRepository->getByNameFromExcel($brand['id'], $vehicle['vehicle_model']) : null;
                $typeModelOrder = $vehicle['channel'] ? $this->typeModelOrderRepository->getByName($vehicle['channel']) : null;
                $existVehicle->vehicle_model_id = $vehicle_model ? $vehicle_model['id'] : null;
                $existVehicle->type_model_order_id = $typeModelOrder ? $typeModelOrder['id'] : null;
                $existVehicle->save();
            }*/
        }
        return ['message' => 'Vehicles created!'];
    }

    public function getByPlate($request)
    {
        return Vehicle::where('plate', $request->json()->get('plate'))
            ->first();
    }

    public function create($request)
    {
        $existVehicle = Vehicle::where('plate', $request->input('plate'))->withTrashed()
            ->first();
        if ($existVehicle) {
            return [
                'code' => is_null($existVehicle->deleted_at) ? 422 : 406,
                'vehicle' => $existVehicle
            ];
        }
        $vehicle = Vehicle::create($request->all());
        if (is_null($vehicle->company_id)) {
            $user = Auth::user();
            $vehicle->company_id = $user->company_id;
        }
        $vehicle->created_by = Auth::id();
        $date = $request->input('created_at');
        if (!is_null($date)) {
            $vehicle->created_at = $date;
            $vehicle->updated_at = $date;
        }
        $vehicle->save();
        $this->newReception($vehicle->id);

        $vehicle = Vehicle::find($vehicle->id);
        $reception = $vehicle->lastReception;
        if (!is_null($date) && !is_null($reception)) {
            $reception->created_at = $date;
            $reception->updated_at = $date;
            $reception->save();
        }
        $this->stateChangeRepository->updateSubStateVehicle($vehicle);
        return $vehicle;
    }

    public function update($request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->update($request->all());
        return Vehicle::with($this->getWiths($request->with) ?? [])->findOrFail($id);
    }

    public function updateBack($request)
    {
        $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));
        $vehicle->update($request->all());
        return response()->json(['vehicle' => $vehicle]);
    }

    public function updateCampa($vehicle_id, $campa)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);
        $vehicle->campa_id = $campa;
        $vehicle->save();
        return $vehicle;
    }

    public function updateTradeState($vehicle_id, $trade_state_id)
    {
        $vehicle = Vehicle::findOrFail($vehicle_id);
        $vehicle->trade_state_id = $trade_state_id;
        $vehicle->save();
        return response()->json(['vehicle' => $vehicle]);
    }

    public function verifyPlate($request)
    {
        $vehicle = Vehicle::with($this->getWiths($request->with))
            ->filter($request->all())
            ->first();;

        $vehicleDefleet = Vehicle::where('id', $vehicle?->id)
            ->byPendingRequestDefleet()
            ->first();

        $defleetingAndDelivery = Vehicle::where('id', $vehicle?->id)->filter(['defleetingAndDelivery' => 0])->first();

        $order_tasks = PendingTask::ORDER_TASKS;

        if ($vehicleDefleet) {
            return ['vehicle' => $vehicle, 'defleet' => true, 'vehicle_delivery' => $defleetingAndDelivery, 'order_tasks' => $order_tasks];
        } else if ($vehicle) {
            return ['vehicle' => $vehicle, 'registered' => true, 'vehicle_delivery' => $defleetingAndDelivery, 'order_tasks' => $order_tasks];
        }
        return ['registered' => false];
    }

    public function verifyPlateReception($request)
    {

        $vehicle = Vehicle::with($this->getWiths($request->with))
            ->where('plate', $request->input('plate'))
            ->first();

        if ($vehicle) {
            $variables_defleet = $this->defleetVariableRepository->getVariablesByCompany($vehicle['campa']['company']['id']);
            $date_first_plate = new DateTime($vehicle->first_plate);
            $date = date("Y-m-d H:i:s");
            $today = new DateTime($date);
            $diff = $date_first_plate->diff($today);
            $year = $diff->format('%Y');
            if ($variables_defleet) {
                if (($vehicle->kms > $variables_defleet->kms || $year > $variables_defleet->years)) {
                    //Si el vehículo cumple con los kpis de defleet se cambia el estado a solicitado por defleet a espera de que lleven el vehículo a la zona pendiente de venta V.O.
                    $this->updateTradeState($vehicle->id, TradeState::REQUEST_DEFLEET);
                    return response()->json(['defleet' => true, 'message' => 'Vehículo para defletar'], 200);
                }
            }
            return response()->json(['vehicle' => $vehicle, 'registered' => true], 200);
        } else {
            return response()->json(['registered' => false], 200);
        }
    }

    public function vehicleDefleet($request)
    {
        $user = $this->userRepository->getById($request, Auth::id());
        $variables = $this->defleetVariableRepository->getVariablesByCompany($user->company_id);
        $date = date("Y-m-d");
        $date_defleet = date("Y-m-d", strtotime($date . " - $variables->years years")) . ' 00:00:00';
        return Vehicle::with($this->getWiths($request->with))
            ->noActiveOrPendingRequest()
            ->byParameterDefleet($date_defleet, $variables->kms)
            ->filter($request->all())
            ->paginate($request->input('per_page'));
    }

    public function delete($id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->deleted_user_id = Auth::id();
        $this->squareRepository->freeSquare($vehicle->id);
        $this->historyLocationRepository->saveFromBack($vehicle->id, null, Auth::id());
        $vehicle->save();
        $vehicle->delete();
        return ['message' => 'Vehicle deleted'];
    }

    public function returnVehicle($request, $id)
    {
        $vehicle = Vehicle::where('id', $id)->withTrashed()->first();
        $vehicle->deleted_user_id = Auth::id();
        $vehicle->deleted_at = null;
        $vehicle->update($request->all());
        $user = Auth::user();
        if (is_null($vehicle->campa_id)) {
            if (count($user->campas) > 0) {
                $vehicle->campa_id = $user->campas[0]->id;
            } else if ($vehicle->lastReception?->campa_d) {
                $vehicle->campa_id = $vehicle->lastReception?->campa_id;
            }
        }
        $vehicle->restore();
        $this->finishPendingTaskLastReception($vehicle->id);
        $this->newReception($vehicle->id, true);
        $this->stateChangeRepository->updateSubStateVehicle($vehicle);
        return $vehicle;
    }

    public function newReception($vehicle_id, $force = false)
    {
        $user = $this->userRepository->getById([], Auth::id());
        $vehicle = Vehicle::find($vehicle_id);
        $vehicle_ids = collect(Vehicle::where('id', $vehicle->id)->filter(['defleetingAndDelivery' => 0])->get())->map(function ($item) {
            return $item->id;
        })->toArray();
        if (is_null($vehicle->lastReception) || $vehicle->sub_state_id === SubState::ALQUILADO || count($vehicle_ids) > 0 || $force) {
            $reception = new Reception();
        } else {
            $reception = $vehicle->lastReception;
        }
        $reception->campa_id = $user->campas->pluck('id')->toArray()[0];;
        $reception->vehicle_id = $vehicle_id;
        $reception->finished = false;
        $reception->has_accessories = false;
        $reception->type_model_order_id = $vehicle->type_model_order_id;
        $reception->save();

        $vehicle = Vehicle::find($vehicle_id);

        return $vehicle->lastReception;
    }

    private function finishPendingTaskLastReception($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
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
    }

    public function deleteMassive($request)
    {
        $plates = $request->get('plates');
        foreach ($plates as $plate) {
            Vehicle::where('plate', $plate)->delete();
        }
        return response()->json(['message' => 'Vehicles deleted!']);
    }


    public function getVehiclesWithReservationWithoutOrderCampa($request)
    {
        $vehicles = Vehicle::with($this->getWiths($request->with))
            ->thathasReservationWithoutOrderWithoutDelivery()
            ->filter($request->all())
            ->get();
        return ['vehicles' => $vehicles];
    }

    public function getVehiclesWithReservationWithoutContractCampa($request)
    {
        $vehicles = Vehicle::with($this->getWiths($request->with))
            ->byWithOrderWithoutContract()
            ->filter($request->all())
            ->get();
        return ['vehicles' => $vehicles];
    }

    public function vehicleReserved($request)
    {
        $user = $this->userRepository->getById($request, Auth::id());
        return Vehicle::with(['reservations' => fn ($query) => $query->where('active', true)])
            ->whereHas('reservations', fn (Builder $builder) => $builder->where('active', true))
            ->byCampasOfUser($user->campas->pluck('id')->toArray())
            ->get();
    }

    public function vehicleTotalsState($request)
    {
        return Vehicle::with($this->getWiths($request->with))
            ->filter($request->all())
            ->select(DB::raw('sub_state_id, COUNT(*) AS count'))
            ->groupBy('sub_state_id')
            ->get();
    }

    public function vehiclesByState($request)
    {
        return Vehicle::with($this->getWiths($request->with))
            ->stateIds($request->input('states'))
            ->defleetBetweenDateApproved($request->input('date_start'), $request->input('date_end'))
            ->campasIds($request->input('campas'))
            ->get();
    }

    public function changeSubState($request)
    {
        $vehicles = $request->input('vehicles');
        $deliveryNote = $this->deliveryNoteRepository->create($request->input('data'), $request->input('type_delivery_note_id'));
        Vehicle::whereIn('id', collect($vehicles)->pluck('id')->toArray())
            ->chunk(200, function ($vehicles) use ($request, $deliveryNote) {
                foreach ($vehicles as $vehicle) {
                    if ($request->input('sub_state_id') == SubState::ALQUILADO) {
                        $this->closeDamage($vehicle['id']);
                        if (!is_null($vehicle->lastReception)) {
                            foreach ($vehicle->lastReception->pendingTasks as $key => $pending_task) {
                                $pending_task->state_pending_task_id = StatePendingTask::FINISHED;
                                $pending_task->order = -1;
                                if (is_null($pending_task->datetime_pending)) {
                                    $pending_task->datetime_pending = Carbon::now();
                                }
                                if (is_null($pending_task->datetime_start)) {
                                    $pending_task->datetime_start = Carbon::now();
                                }
                                if (is_null($pending_task->datetime_finish)) {
                                    $pending_task->datetime_finish = Carbon::now();
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
                        if (is_null($vehicle->lastReception)) {
                            $this->newReception($vehicle['id']);
                        }
                        if ($vehicle->sub_state_id != SubState::DEFLEETED) {
                            $vehicle->sub_state_id = null;
                        } else {
                            $vehicle->type_model_order_id = TypeModelOrder::VO_ENTREGADO;
                        }

                        $vehicle->save();

                        $this->stateChangeRepository->updateSubStateVehicle($vehicle, SubState::ALQUILADO);

                        $this->deliveryVehicleRepository->createDeliveryVehicles($vehicle['id'], $request->input('data'), $deliveryNote->id);

                    } else if ($request->input('sub_state_id') == SubState::WORKSHOP_EXTERNAL || $request->input('sub_state_id') == SubState::TRANSIT) {
                        $this->vehicleExitRepository->registerExit($vehicle['id'], $deliveryNote->id, $vehicle->campa_id);
                        $vehicle->sub_state_id = $request->input('sub_state_id');
                        $vehicle->save();
                    }
                    $this->freeSquare($vehicle);
                }
            });
        return [
            'delivery_note' => $deliveryNote
        ];
    }

    private function freeSquare($vehicle)
    {
        $square = $vehicle->square()->first();
        if (!is_null($square)) {
            $this->historyLocationRepository->saveFromBack($square->vehicle_id, null, Auth::id());
            $square->vehicle_id = null;
            $square->save();
        }
    }

    public function setVehicleRented($request)
    {
        $vehicles = $request->input('vehicles');
        foreach ($vehicles as $vehicle) {
            $updateVehicle = Vehicle::where('plate', $vehicle)
                ->first();
            if ($updateVehicle) {
                $updateVehicle->sub_state_id = SubState::ALQUILADO;
                $updateVehicle->save();
            }
        }

        return response()->json(['message' => 'Done!']);
    }
    public function defleeting($id){
        $vehicle = Vehicle::findOrFail($id);
       /* if (!!$vehicle->lastReception && !$vehicle->lastReception->lastQuestionnaire?->datetime_approved){
            return [
                'status'=>false,
                'message'=>'Este vehículo tiene un cuestionario activo en la recepción actual.',
                'data'=>$vehicle->lastReception->lastQuestionnaire
            ];
        } else */
        if (is_null($vehicle->datetime_defleeting)) {
            $vehicle->datetime_defleeting = Carbon::now();
            $vehicle->sub_state_id = SubState::DEFLEETED;
            $vehicle->save();
            /* QUITAR TAREA DE UBICACION*/
            /*$pendingTask = new PendingTask();
            $pendingTask->vehicle_id = $vehicle->id;
            $pendingTask->reception_id = $vehicle->lastReception->id;
            $pendingTask->task_id = Task::UBICATION;
            $pendingTask->state_pending_task_id = count($vehicle->lastReception->approvedPendingTasks) == 0 ? StatePendingTask::PENDING : null;
            $pendingTask->duration = 1;
            $pendingTask->order = count($vehicle->lastReception->approvedPendingTasks) + 1;
            $pendingTask->datetime_pending = Carbon::now();
            $pendingTask->user_id = Auth::id();
            $pendingTask->save();*/
        } else {
          /*  if ($vehicle->lastReception) {
                PendingTask::where('reception_id', $vehicle->lastReception)
                ->where('task_id', Task::UBICATION)
                ->chunk(200, function ($pendingTasks) {
                    foreach ($pendingTasks as $pendingTask) {
                        $pendingTask->update([
                            'approved' => false,
                            'order' => null,
                            'state_pending_task_id' => null,
                        ]);
                    }
                });
            }*/
            $vehicle->datetime_defleeting = null;
            $vehicle->save();
            $this->stateChangeRepository->updateSubStateVehicle($vehicle);
        }
        return $vehicle;
    }

    public function updateSubStateVehicle($id) {
        $vehicle = Vehicle::findOrFail($id);
        return $this->stateChangeRepository->updateSubStateVehicle($vehicle);
    }

    private function closeDamage($vehicleId)
    {
        Damage::where('vehicle_id', $vehicleId)
            ->where('status_damage_id', '!=', StatusDamage::CLOSED)
            ->chunk(200, function ($damages) {
                foreach ($damages as $damage) {
                    $damage->update([
                        'status_damage_id' => StatusDamage::CLOSED,
                        'datetime_close' => Carbon::now()
                    ]);
                }
            });
    }

    public function pendingOrInProgress($vehicleId)
    {
        return Vehicle::where('id', $vehicleId)
            ->with(['lastReception.pendingTasks' => function ($builder) {
                return $builder->where('state_pending_task_id', StatePendingTask::PENDING)
                    ->orWhere('state_pending_task_id', StatePendingTask::IN_PROGRESS);
            }])
            ->first();
    }

}
