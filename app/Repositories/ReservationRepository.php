<?php

namespace App\Repositories;

use App\Models\Reservation;
use App\Models\StateRequest;
use App\Models\TradeState;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\TaskReservationRepository;
use App\Repositories\VehicleRepository;
use Exception;

class ReservationRepository extends Repository {

    protected $taskReservationRepository;
    protected  $vehicleRepository;
    public function __construct(
        TaskReservationRepository $taskReservationRepository,
        VehicleRepository $vehicleRepository
    )
    {
        $this->taskReservationRepository = $taskReservationRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function getByRequestId($request_id){
        return Reservation::where('request_id', $request_id)
                        ->first();
    }

    public function create($request_id, $vehicle_id, $reservation_time, $planned_reservation, $campa_id, $active, $reservation_type_id){
        $reservation = new Reservation();
        $reservation->request_id = $request_id;
        $reservation->vehicle_id = $vehicle_id;
        $reservation->reservation_time = $reservation_time;
        $reservation->planned_reservation = $planned_reservation;
        $reservation->campa_id = $campa_id;
        $reservation->active = $active;
        $reservation->type_reservation_id = $reservation_type_id;
        $reservation->save();
        return $reservation;
    }

    public function changeStateReservation($request_vehicle, $active){
        $reservation = Reservation::where('request_id', $request_vehicle)
                                ->first();
        $reservation->active = $active;
        $reservation->save();
    }

    public function getReservationActive($request){
        return Reservation::with($this->getWiths($request->with))
                    ->byCompany($request->input('company_id'))
                    ->get();
    }

    public function getReservationActiveByCampa($request){
        try {
            return Reservation::with(['vehicle.state','vehicle.category','request.type_request','request.state_request'])
                                ->whereHas('vehicle', function (Builder $builder) use($request){
                                    return $builder->where('campa_id', $request->input('campa_id'));
                                })
                                ->get();
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function update($request){
        $reservation = Reservation::findOrFail($request->input('reservation_id'));
        $reservation->update($request->all());
        if($reservation->order != null && $reservation->contract != null){
            $task_reservation = $this->taskReservationRepository->getByRequest($reservation->request_id);
            if($reservation->type_reservation_id == 2){
                $this->vehicleRepository->updateTradeState($reservation['vehicle_id'], TradeState::RESERVED_PRE_DELIVERY);
            } else {
                if(count($task_reservation) > 0){
                    $this->vehicleRepository->updateTradeState($reservation['vehicle_id'], TradeState::RESERVED);
                } else {
                    $this->vehicleRepository->updateTradeState($reservation['vehicle_id'], TradeState::PRE_RESERVED);
                }
            }
        }
        return Reservation::with(['transport'])
                    ->where('id', $request->input('reservation_id'))
                    ->first();
    }

    public function getReservationsByVehicle($request){
        return Reservation::where('vehicle_id', $request->input('vehicle_id'))
                    ->where('contract', null)
                    ->orderBy('id', 'desc')
                    ->first();
    }

    public function deleteReservation($request){
        Reservation::where('request_id', $request->input('request_id'))
            ->delete();
    }

    public function vehicleWithoutOrder($request){
        return Reservation::with($this->getWiths($request->with))
                    ->where('vehicle_id', $request->input('vehicle_id'))
                    ->where('order', null)
                    ->whereHas('request', function (Builder $builder) {
                        return $builder->where('state_request_id', StateRequest::REQUESTED);
                    })
                    ->orderBy('id','desc')
                    ->first();
    }

    public function vehicleWithoutContract($request){
        return Reservation::with($this->getWiths($request->with))
                        ->where('vehicle_id', $request->input('vehicle_id'))
                        ->where('contract', null)
                        ->whereHas('request', function (Builder $builder) {
                            return $builder->where('state_request_id', StateRequest::APPROVED);
                        })
                        ->orderBy('id','desc')
                        ->first();
    }

}
