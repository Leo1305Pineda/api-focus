<?php

namespace App\Repositories;

use App\AditionalModels\AditionalModels;
use App\AditionalModels\AditionalsModels;
use App\EloquentFunctions\EloquentFunctions;
use App\Models\Company;
use App\Models\DefleetVariable;
use App\Models\DeliveryVehicle;
use App\Models\SubState;
use App\Models\TradeState;
use App\Models\Vehicle;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Repositories\CategoryRepository;
use App\Repositories\DefleetVariableRepository;
use App\Repositories\GroupTaskRepository;
use App\Repositories\StateRepository;
use App\Repositories\BrandRepository;
use App\Repositories\VehicleModelRepository;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;
use Symfony\Component\Console\Input\Input;

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
        CampaRepository $campaRepository)
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
        $vehicles = Vehicle::with($this->getWiths($request->with))
                    ->filter($request->all())
                    ->paginate($request->input('per_page'));
        return [ 'vehicles' => $vehicles ];
    }

    public function createFromExcel($request) {
        $vehicles = $request->input('vehicles');
        foreach($vehicles as $vehicle){
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
        }
        return ['message' => 'Vehicles created!'];
    }

    public function getByPlate($request) {
        return Vehicle::where('plate', $request->json()->get('plate'))
                       ->first();
    }

    public function create($request) {
        $vehicle = Vehicle::create($request->all());
        $vehicle->save();
        return response()->json(['vehicle' => $vehicle], 200);
    }

    public function update($request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        return $vehicle->update($request->all());
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
        $vehicle->sub_state_id = $sub_state_id;
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
        Vehicle::where('id', $id)
            ->delete();
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

    public function vehicleTotalsState($request){
        return Vehicle::with(['subState.state'])
            ->whereIn('campa_id', $request->input('campas'))
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
        Vehicle::whereIn('id', collect($vehicles)->pluck('id')->toArray())
                ->chunk(200, function ($vehicles) use($request) {
                    foreach($vehicles as $vehicle){
                        if($request->input('sub_state_id') == SubState::ALQUILADO){
                            $this->deliveryVehicleRepository->createDeliveryVehicles($vehicle['id'], $request->input('data'));
                        }
                        $vehicle->update(['sub_state_id' => $request->input('sub_state_id')]);
                    }
                });
        return [
            'message' => 'Vehicles updated!'
        ];
    }

}
