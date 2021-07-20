<?php

namespace App\Repositories;
use App\Models\Request as RequestVehicle;
use App\Repositories\TaskReservationRepository;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\PendingTaskRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class RequestRepository {

    public function __construct(
        TaskReservationRepository $taskReservationRepository,
        PendingTaskRepository $pendingTaskRepository,
        ReservationRepository $reservationRepository,
        UserRepository $userRepository,
        VehicleRepository $vehicleRepository)
    {
        $this->taskReservationRepository = $taskReservationRepository;
        $this->pendingTaskRepository = $pendingTaskRepository;
        $this->reservationRepository = $reservationRepository;
        $this->userRepository = $userRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function create($request){
        try {
            $array_request = [];
            $vehicles = $request->input('vehicles');
            $request_active = false;
            foreach($vehicles as $vehicle){
                $request_vehicle = new RequestVehicle();
                $vehicle_request_active = RequestVehicle::where('vehicle_id', $vehicle['vehicle_id'])
                                                ->where('state_request_id', 1)
                                                ->get();
                if(count($vehicle_request_active) > 0){
                    $request_active = true;
                } else {
                    $request_vehicle->vehicle_id = $vehicle['vehicle_id'];
                    $request_vehicle->state_request_id = 1;
                    $request_vehicle->customer_id = $request->input('customer_id');
                    $request_vehicle->type_request_id = $vehicle['type_request_id'];
                    $request_vehicle->datetime_request = date('Y-m-d H:i:s');
                    $request_vehicle->save();
                    if($vehicle['type_request_id'] == 2){
                        $this->taskReservationRepository->create($request_vehicle->id, $request->input('tasks'), $vehicle['vehicle_id']);
                        //Estado comercial cambia a pre-reservado (Es reservado pero con tareas pendientes)
                        $tasks = $this->taskReservationRepository->getByRequest($request_vehicle->id);
                        if(count($tasks) > 0) {
                            //Si hay tareas pasamos el vehículo al estado pre-reservado
                            $this->vehicleRepository->updateTradeState($vehicle['vehicle_id'], 2);
                            $this->vehicleRepository->updateState($vehicle['vehicle_id'], 4);
                        } else {
                            //Si no hay tareas pasamos el vehículo al estado reservado
                            $this->vehicleRepository->updateTradeState($vehicle['vehicle_id'], 1);
                            $this->vehicleRepository->updateState($vehicle['vehicle_id'], 4);
                        }
                        //Creamos la reserva con active 1
                        $this->reservationRepository->create($request_vehicle['id'], $vehicle['vehicle_id'], $request->input('reservation_time'), $request->input('planned_reservation'), $request->input('campa_id'), 1, $request->input('type_reservation_id'));
                    }
                    array_push($array_request, $request_vehicle);
                }
            }
            if($request_active == true){
                return [
                    'message' => 'Existen vehículos que tienen una solicitud activa',
                    'requests' => $array_request
                ];
            }
            return $array_request;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }


    public function getById($id){
        try {
            return RequestVehicle::findOrFail($id);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }


    public function update($request, $id){
        try {
            $request_vehicle = RequestVehicle::findOrFail($id);
            $request_vehicle->update($request->all());
            if($request->input('type_request_id') == 2){
                $this->reservationRepository->create($request_vehicle['id'], $request_vehicle['vehicle_id'], $request->input('reservation_time'), $request->input('planned_reservation'), $request->input('campa_id'));
                $tasks = $this->taskReservationRepository->getByRequest($request_vehicle->id);
                if(count($tasks) > 0) {
                    //Si hay tareas pasamos el vehículo al estado pre-reservado
                    $this->vehicleRepository->updateTradeState($request_vehicle['vehicle_id'], 2);
                } else {
                    //Si no hay tareas pasamos el vehículo al estado reservado
                    $this->vehicleRepository->updateTradeState($request_vehicle['vehicle_id'], 1);
                }
            }
            return $request_vehicle;
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }


    public function vehiclesRequestedDefleet($request){
        try {
            return RequestVehicle::with(['vehicle.state','vehicle.category','vehicle.campa','state_request','type_request'])
                                ->whereHas('vehicle', function(Builder $builder) use ($request){
                                    return $builder->where('campa_id', $request->input('campa_id'));
                                })
                                ->where('type_request_id', 1)
                                ->where('state_request_id', 1)
                                ->get();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function vehiclesRequestedReserve($request){
        try {
            return RequestVehicle::with(['vehicle.state','vehicle.category','vehicle.campa','state_request','type_request', 'customer','reservation'])
                                ->whereHas('vehicle', function(Builder $builder) use ($request){
                                    return $builder->where('campa_id', $request->input('campa_id'));
                                })
                                ->where('type_request_id', 2)
                                ->where('state_request_id', 1)
                                ->get();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function confirmedRequest($request){
        try {
            $request_vehicle = RequestVehicle::where('id', $request->input('request_id'))
                                        ->first();
            $request_vehicle->state_request_id = 2;
            $request_vehicle->datetime_approved = date('Y-m-d H:i:s');
            $request_vehicle->save();
            if($request_vehicle['type_request_id'] == 2){
                //Buscamos reservation
                $reservation = $this->reservationRepository->getByRequestId($request_vehicle['id']);
                if($reservation['type_reservation_id'] == 1){
                    $tasks = $this->taskReservationRepository->getByRequest($request_vehicle->id);
                if(count($tasks) > 0) {
                    //Si hay tareas pasamos el vehículo al estado pre-reservado
                    $this->vehicleRepository->updateTradeState($request_vehicle['vehicle_id'], 2);
                } else {
                    //Si no hay tareas pasamos el vehículo al estado reservado
                    $this->vehicleRepository->updateTradeState($request_vehicle['vehicle_id'], 1);
                }
                } else {
                    //Cambio de estado comercial a reservado pre-entrega
                    $this->vehicleRepository->updateTradeState($request_vehicle['vehicle_id'], 3);

                }
                //Marcamos la reserva como ejecutada con el active 1
                $this->reservationRepository->changeStateReservation($request_vehicle['id'], 1);
                //Se crean las tareas solicitadas al momento de la reserva
                return $this->pendingTaskRepository->createPendingTaskFromReservation($request_vehicle['vehicle_id'], $request_vehicle['id']);
            }
            //Si no es una solicitud de reserva lo será de defleet
            $this->vehicleRepository->updateTradeState($request_vehicle['vehicle_id'], 4);
            //Ponemos el state del vehículo en Pendiente Venta V.O.
            $this->vehicleRepository->updateState($request_vehicle['vehicle_id'], 3);
            return [
                'message' => 'Ok'
            ];
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function getConfirmedRequest($request){
        try {
            return RequestVehicle::with(['type_request','state_request','vehicle.state','vehicle.campa','vehicle.category'])
                                ->whereHas('vehicle', function (Builder $builder) use($request){
                                    return $builder->where('campa_id', $request->input('campa_id'));
                                })
                                ->where('type_request_id', $request->input('type_request_id'))
                                ->where('state_request_id', 2)
                                ->get();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function declineRequest($request){
        try {
            $request_vehicle = RequestVehicle::where('id', $request->input('request_id'))
                                        ->first();
            //Ponemos el vehículo disponible
            $this->vehicleRepository->updateTradeState($request_vehicle['vehicle_id'], null);
            $request_vehicle->state_request_id = 3;
            $request_vehicle->save();
            //Eliminamos reservation
            $this->reservationRepository->deleteReservation($request);
            return RequestVehicle::with(['state_request'])
                            ->where('id', $request->input('request_id'))
                            ->first();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function getRequestDefleetApp(){
        try {
            $user = $this->userRepository->getById(Auth::id());
            return RequestVehicle::with(['vehicle.category','type_request'])
                                ->whereHas('vehicle', function (Builder $builder) use($user){
                                    return $builder->where('campa_id', $user->campa_id);
                                })
                                ->where('type_request_id', 1)
                                ->where('state_request_id', 1)
                                ->get();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function getRequestReserveApp(){
        try {
            $user = $this->userRepository->getById(Auth::id());
            return RequestVehicle::with(['vehicle.category','type_request'])
                                ->whereHas('vehicle', function (Builder $builder) use($user){
                                    return $builder->where('campa_id', $user->campa_id);
                                })
                                ->where('type_request_id', 2)
                                ->where('state_request_id', 1)
                                ->get();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

}
