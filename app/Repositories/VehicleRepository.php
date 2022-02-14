<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\DeliveryNote;
use App\Models\SubState;
use App\Models\TradeState;
use App\Models\Vehicle;
use App\Models\Square;
use App\Models\VehicleExit;
use App\Models\StatePendingTask;
use App\Models\TypeDeliveryNote;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CategoryRepository;
use App\Repositories\DefleetVariableRepository;
use App\Repositories\GroupTaskRepository;
use App\Repositories\StateRepository;
use App\Repositories\BrandRepository;
use App\Repositories\VehicleModelRepository;
use App\Repositories\UserRepository;
use App\Repositories\TypeModelOrderRepository;
use App\Repositories\DeliveryVehicleRepository;
use App\Repositories\VehicleExitRepository;
use App\Repositories\CampaRepository;
use Illuminate\Support\Facades\DB;

class VehicleRepository extends Repository {

    public function __construct(
        UserRepository $userRepository,
        CategoryRepository $categoryRepository,
        DefleetVariableRepository $defleetVariableRepository,
        StateRepository $stateRepository,
        GroupTaskRepository $groupTaskRepository,
        BrandRepository $brandRepository,
        VehicleModelRepository $vehicleModelRepository,
        TypeModelOrderRepository $typeModelOrderRepository,
        DeliveryVehicleRepository $deliveryVehicleRepository,
        VehicleExitRepository $vehicleExitRepository,
        CampaRepository $campaRepository,
        DeliveryNoteRepository $deliveryNoteRepository,
        SquareRepository $squareRepository)
    {
        $this->userRepository = $userRepository;
        $this->categoryRepository = $categoryRepository;
        $this->defleetVariableRepository = $defleetVariableRepository;
        $this->stateRepository = $stateRepository;
        $this->groupTaskRepository = $groupTaskRepository;
        $this->brandRepository = $brandRepository;
        $this->vehicleModelRepository = $vehicleModelRepository;
        $this->campaRepository = $campaRepository;
        $this->typeModelOrderRepository = $typeModelOrderRepository;
        $this->deliveryVehicleRepository = $deliveryVehicleRepository;
        $this->vehicleExitRepository = $vehicleExitRepository;
        $this->squareRepository = $squareRepository;
        $this->deliveryNoteRepository = $deliveryNoteRepository;
    }
    
    public function getAll($request){
        $user = $this->userRepository->getById($request, Auth::id());
        $vehicles = Vehicle::with($this->getWiths($request->with))
                    ->byCampasOfUser($user->campas->pluck('id')->toArray())
                    ->paginate($request->input('per_page'));
        return [ 'vehicles' => $vehicles ];
    }

    public function getById($request, $id){
        return Vehicle::with($this->getWiths($request->with))->findOrFail($id);
    }

    public function filterVehicle($request) {
        $query = Vehicle::with($this->getWiths($request->with))
                    ->filter($request->all());

        if ($request->input('noPaginate')) {
            $vehicles = [
                'data' => $query->get()
            ];
        } else {
            $vehicles =  $query->paginate($request->input('per_page'));
        }
        return [ 'vehicles' => $vehicles ];
    }

    public function createFromExcel($request) {
        $vehicles = $request->input('vehicles');
        foreach($vehicles as $vehicle){
            $existVehicle = Vehicle::where('plate', $vehicle['plate'])
                        ->first();
            if(!$existVehicle){
                $new_vehicle = Vehicle::create($vehicle);
                $campa = $vehicle['campa'] ? $this->campaRepository->getByName($vehicle['campa']) : null;
                $typeModelOrder = $vehicle['channel'] ? $this->typeModelOrderRepository->getByName($vehicle['channel']) : null;
                $new_vehicle->campa_id = $campa ? $campa['id'] : null;
                $category = $this->categoryRepository->searchCategoryByName($vehicle['category']);
                if($category) $new_vehicle->category_id = $category['id'];
                $brand = $vehicle['brand'] ? $this->brandRepository->getByNameFromExcel($vehicle['brand']) : null;
                $vehicle_model = $brand ? $this->vehicleModelRepository->getByNameFromExcel($brand['id'], $vehicle['vehicle_model']) : null;
                $new_vehicle->type_model_order_id = $typeModelOrder ? $typeModelOrder['id'] : null;
                $new_vehicle->sub_state_id = $vehicle['ubication'] ? SubState::CAMPA : null;
                $new_vehicle->vehicle_model_id = $vehicle_model ? $vehicle_model['id'] : null;
                $new_vehicle->company_id = Company::ALD;
                $new_vehicle->save();
            } else {
                $vehicle['square'] ? $this->squareRepository->assignVehicle($vehicle['street'], intval($vehicle['square']), $existVehicle['id']) : null;
                if($vehicle['channel'] !== 'ALD Flex' && $vehicle['campa'] == 'Campa Leganes') $existVehicle->sub_state_id = SubState::CAMPA;
                if($vehicle['campa'] === 'Campa Leganes' && $vehicle['sub_state'] === null) $existVehicle->sub_state_id = SubState::ALQUILADO;
                $typeModelOrder = $vehicle['channel'] ? $this->typeModelOrderRepository->getByName($vehicle['channel']) : null;
                $category = $this->categoryRepository->searchCategoryByName($vehicle['category']);
                if($category) $existVehicle->category_id = $category['id'];
                $existVehicle->type_model_order_id = $typeModelOrder ? $typeModelOrder['id'] : null;
                $existVehicle->save();
            }
        }
        return ['message' => 'Vehicles created!'];
    }

    public function getByPlate($request) {
        return Vehicle::where('plate', $request->json()->get('plate'))
                       ->first();
    }

    public function create($request) {
        $existVehicle = Vehicle::where('plate', $request->input('plate'))
                        ->first();
        if($existVehicle){
            return response()->json(['message' => 'Esta matrícula ya está registrada']);
        }
        $vehicle = Vehicle::create($request->all());
        if (is_null($vehicle->company_id)) {
            $user = Auth::user();
            $vehicle->company_id = $user->company_id;
        }
        $vehicle->save();
        return response()->json(['vehicle' => $vehicle], 200);
    }

    public function update($request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $data = $request->all();
        Square::where('vehicle_id', $vehicle->id)->update([
            'vehicle_id' => null
        ]);
        if (isset($data['ubication'])) {
            $square = Square::find($data['ubication']);
            $square->vehicle_id = $vehicle->id;
            $square->save();
        }
        return $vehicle->update($data);
    }

    public function updateBack($request)
    {
        $vehicle = Vehicle::findOrFail($request->input('vehicle_id'));
        $vehicle->update($request->all());
        return response()->json([ 'vehicle' => $vehicle ]);
    }

    public function updateCampa($vehicle_id, $campa){
        $vehicle = Vehicle::findOrFail($vehicle_id);
        $vehicle->campa_id = $campa;
        $vehicle->save();
        return $vehicle;
    }

    public function updateSubState($vehicle_id, $sub_state_id) {
        $vehicle = Vehicle::findOrFail($vehicle_id);
        //$vehicle->sub_state_id = $sub_state_id;
        // $enc = $vehicle->sub_state_id == SubState::ALQUILADO || $vehicle->sub_state_id == SubState::WORKSHOP_EXTERNAL;
        if (is_null($vehicle->lastGroupTask)) {
            $vehicle->sub_state_id = null;
        } else {
            $count = count($vehicle->lastGroupTask->approvedPendingTasks);
            if ($count == 0) {
                $vehicle->sub_state_id = SubState::CAMPA;
            } else if ($count > 0) {
                $vehicle->sub_state_id = $vehicle->lastGroupTask->approvedPendingTasks[0]->task->sub_state_id;
            }
        }
        $vehicle->save();
        return response()->json(['vehicle' => $vehicle]);
    }

    public function updateTradeState($vehicle_id, $trade_state_id) {
        $vehicle = Vehicle::findOrFail($vehicle_id);
        $vehicle->trade_state_id = $trade_state_id;
        $vehicle->save();
        return response()->json(['vehicle' => $vehicle]);
    }

    public function verifyPlate($request) {
        $vehicleDefleet = Vehicle::with($this->getWiths($request->with))
            ->byPlate($request->input('plate'))
            ->byPendingRequestDefleet()
            ->filter($request->all())
            ->first();
        if($vehicleDefleet){
            return ['defleet' => true, 'vehicle' => $vehicleDefleet];
        }

        $vehicle = Vehicle::with($this->getWiths($request->with))
                    ->where('plate', $request->input('plate'))
                    ->first();

        if($vehicle){
            return ['vehicle' => $vehicle, 'registered' => true];
        } else {
            return ['registered' => false];
        }
    }

public function verifyPlateReception($request){

        $vehicle = Vehicle::with($this->getWiths($request->with))
                    ->where('plate', $request->input('plate'))
                    ->first();

        if($vehicle){
            $variables_defleet = $this->defleetVariableRepository->getVariablesByCompany($vehicle['campa']['company']['id']);
            $date_first_plate = new DateTime($vehicle->first_plate);
            $date = date("Y-m-d H:i:s");
            $today = new DateTime($date);
            $diff = $date_first_plate->diff($today);
            $year = $diff->format('%Y');
            if($variables_defleet){
                if(($vehicle->kms > $variables_defleet->kms || $year > $variables_defleet->years)){
                    //Si el vehículo cumple con los kpis de defleet se cambia el estado a solicitado por defleet a espera de que lleven el vehículo a la zona pendiente de venta V.O.
                    $this->updateTradeState($vehicle->id, TradeState::REQUEST_DEFLEET);
                    return response()->json(['defleet' => true,'message' => 'Vehículo para defletar'], 200);
                }
            }
            return response()->json(['vehicle' => $vehicle, 'registered' => true], 200);
        } else {
            return response()->json(['registered' => false], 200);
        }
    }

    public function vehicleDefleet($request) {
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

    public function delete($id) {
        $vehicle = Vehicle::findOrFail($id);
        $vehicle->deleted_user_id = Auth::id();
        $vehicle->save();
        $vehicle->delete();
        return ['message' => 'Vehicle deleted'];
    }


    public function getVehiclesWithReservationWithoutOrderCampa($request) {
        $vehicles = Vehicle::with($this->getWiths($request->with))
            ->thathasReservationWithoutOrderWithoutDelivery()
            ->filter($request->all())
            ->get();
        return ['vehicles' => $vehicles];
    }

    public function getVehiclesWithReservationWithoutContractCampa($request) {
        $vehicles = Vehicle::with($this->getWiths($request->with))
                    ->byWithOrderWithoutContract()
                    ->filter($request->all())
                    ->get();
        return ['vehicles' => $vehicles];
    }

    public function vehicleReserved($request){
        $user = $this->userRepository->getById($request, Auth::id());
        return Vehicle::with(['reservations' => fn ($query) => $query->where('active', true)])
            ->whereHas('reservations', fn (Builder $builder) => $builder->where('active', true))
            ->byCampasOfUser($user->campas->pluck('id')->toArray())
            ->get();
    }

    public function vehicleTotalsState($request) {
        return Vehicle::with($this->getWiths($request->with))
            ->filter($request->all())
            ->select(DB::raw('sub_state_id, COUNT(*) AS count'))
            ->groupBy('sub_state_id')
            ->get();
    }

    public function vehicleRequestDefleet($request){
        $user = $this->userRepository->getById($request, Auth::id());
        $vehicles = Vehicle::with($this->getWiths($request->with))
            ->withRequestDefleetActive()
            ->where('trade_state_id', TradeState::REQUEST_DEFLEET)
            ->where('sub_state_id', '<>' ,SubState::SOLICITUD_DEFLEET)
            ->byCampasOfUser($user->campas->pluck('id')->toArray())
            ->get();
        return ['vehicles' => $vehicles];
    }

    public function vehiclesByState($request){
        return Vehicle::with($this->getWiths($request->with))
            ->stateIds($request->input('states'))
            ->defleetBetweenDateApproved($request->input('date_start'), $request->input('date_end'))
            ->campasIds($request->input('campas'))
            ->get();
    }

    public function changeSubState($request){
        $vehicles = $request->input('vehicles');
        $deliveryNote = null;
        Vehicle::whereIn('id', collect($vehicles)->pluck('id')->toArray())
                ->chunk(200, function ($vehicles) use($request) {
                    foreach($vehicles as $vehicle){
                        if($request->input('sub_state_id') == SubState::ALQUILADO){
                            $deliveryNote = $this->deliveryNoteRepository->create($request->input('data'), TypeDeliveryNote::DELIVERY);
                            $this->deliveryVehicleRepository->createDeliveryVehicles($vehicle['id'], $request->input('data'), $deliveryNote->id);
                            if (!is_null($vehicle->lastGroupTask)) {
                                foreach ($vehicle->lastGroupTask->pendingTasks as $key => $pending_task) {
                                    $pending_task->state_pending_task_id = StatePendingTask::FINISHED;
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
                            $square = $vehicle->square()->first();
                            if (!is_null($square)) {
                                $square->vehicle_id = null;
                                $square->save();
                            }
                            $vehicle->update(['sub_state_id' => SubState::ALQUILADO]);
                        }
                        if($request->input('sub_state_id') == SubState::WORKSHOP_EXTERNAL){
                            $deliveryNote = $this->deliveryNoteRepository->create($request->input('data'), TypeDeliveryNote::EXIT);
                            $this->vehicleExitRepository->registerExit($vehicle['id'], $deliveryNote->id);
                            $vehicle->update(['sub_state_id' => SubState::WORKSHOP_EXTERNAL]);
                        }
                    }
                });
        return [
            'data' => $deliveryNote
        ];
    }

    public function setVehicleRented($request){
        $vehicles = $request->input('vehicles');
        foreach($vehicles as $vehicle){
            $updateVehicle = Vehicle::where('plate', $vehicle)
                ->first();
            if($updateVehicle){
                $updateVehicle->sub_state_id = SubState::ALQUILADO;
                $updateVehicle->save();
            }
        }
        
        return response()->json(['message' => 'Done!']);
    }

}
