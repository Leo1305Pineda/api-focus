<?php

namespace App\Http\Controllers;

use App\Mail\DownloadVehicles;
use App\Models\PendingDownload;
use App\Models\SubState;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Repositories\VehicleRepository;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{

    public function __construct(VehicleRepository $vehicleRepository, DownloadVehicles $downloadVehicles)
    {
        $this->vehicleRepository = $vehicleRepository;
        $this->downloadVehicles = $downloadVehicles;
    }


    /**
    * @OA\Get(
    *     path="/api/vehicles/download",
    *     tags={"vehicles"},
    *     summary="Download",
    *     security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         value= @OA\JsonContent(
    *              @OA\Property(
    *                  property="message",
    *                  type="string",
    *                  example="Documento generandose, en cuanto esté listo, le llegará a su correo"
    *              ),
    *          ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function download(Request $request)
    {
        $pendingDownload = new PendingDownload();
        $pendingDownload->user_id = Auth::id();
        $pendingDownload->type_document = 'vehicles';
        $pendingDownload->save();
        return $this->createDataResponse('Documento generandose, en cuanto esté listo, le llegará a su correo', HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/vehicles",
    *     tags={"vehicles"},
    *     summary="Get all vehicles",
    *     security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Parameter(
    *       name="with[]",
    *       in="query",
    *       description="A list of relatonship",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           example={"relationship1","relationship2"},
    *           @OA\Items(type="string")
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="per_page",
    *       in="query",
    *       description="Items per page",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=5,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="page",
    *       in="query",
    *       description="Page",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=1,
    *       )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\Items(ref="#/components/schemas/VehiclePaginate")
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getAll(Request $request)
    {

        return $this->getDataResponse($this->vehicleRepository->getAll($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/vehicles/{id}",
    *     tags={"vehicles"},
    *     summary="Get vehicle by ID",
    *    security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Parameter(
    *       name="with[]",
    *       in="query",
    *       description="A list of relatonship",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           example={"relationship1","relationship2"},
    *           @OA\Items(type="string")
    *       )
    *     ),
    *     @OA\Parameter(
    *         name="id",
    *         in="path",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/VehiclesByID"),
    *    ),
    *     @OA\Response(
    *         response="404",
    *         description="Vehicle not found."
    *     )
    * )
    */

    public function getById(Request $request, $id)
    {

        return $this->getDataResponse($this->vehicleRepository->getById($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles",
     *     tags={"vehicles"},
     *     summary="Create vehicle",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createVehicle",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Vehicle"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create Vehicle object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Vehicle"),
     *     )
     * )
     */

    public function create(Request $request)
    {

        $this->validate($request, [
            'campa_id' => 'required|integer',
            'plate' => 'required|string',
            'vehicle_model_id' => 'nullable|integer',
            'first_plate' => 'nullable|date',
        ]);
        $data = $this->vehicleRepository->create($request);
        if (!is_null($data['code'])) {
            return $this->failResponse(['message' => 'Esta matrícula ya está registrada', 'vehicle' => $data['vehicle']], $data['code']);
        }
        return $this->createDataResponse(['vehicle' => $data], HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/vehicles/update/{id}",
     *     tags={"vehicles"},
     *     summary="Updated vehicle",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Updated vehicle object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Vehicle")
     *     ),
     *     operationId="updateVehicle",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id that to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         value=@OA\JsonContent(
     *                     @OA\Property(
     *                         property="vehicle",
     *                         type="object",
     *                         ref="#/components/schemas/Vehicle"
     *                     ),
     *                     @OA\Property(
     *                         property="with[]",
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                     )
     *          ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Vehicle not found"
     *     ),
     * )
     */

    public function update(Request $request, $id)
    {
        return $this->updateDataResponse(['vehicle' => $this->vehicleRepository->update($request, $id)], HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles/verify-plate",
     *     summary="Verify plate",
     *     tags={"vehicles"},
     *     operationId="verifyPlate",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value=@OA\JsonContent(
     *                     @OA\Property(
     *                         property="vehicle",
     *                         type="object",
     *                         ref="#/components/schemas/Vehicle"
     *                     ),
     *                     @OA\Property(
     *                         property="with[]",
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                     )
     *          ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value=@OA\JsonContent(
     *                     @OA\Property(
     *                         property="vehicle",
     *                         type="string",
     *                         ref="#/components/schemas/Vehicle"
     *                     ),
     *                     @OA\Property(
     *                         property="defleet",
     *                         type="boolean",
     *                         example=true
     *                     ),
     *                     @OA\Property(
     *                         property="registered",
     *                         type="boolean",
     *                         example=true
     *                     ),
     *                     @OA\Property(
     *                         property="vehicle_delivery",
     *                         type="string",
     *                         ref="#/components/schemas/Vehicle"
     *                     ),
     *                     @OA\Property(
     *                         property="order_tasks",
     *                         type="array",
     *                         @OA\Items(type="integer"),
     *                         example="[39, 11, 2, 3, 4, 41, 5, 6, 7, 8]"
     *                     )
     *          ),
     *     )
     * )
     */

    public function verifyPlate(Request $request)
    {

        $this->validate($request, [
            'plate' => 'required|string',
        ]);

        return $this->getDataResponse($this->vehicleRepository->verifyPlate($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/vehicles/defleet",
    *     tags={"vehicles"},
    *     summary="Get vehicle defleet ",
    *     security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Parameter(
    *       name="with[]",
    *       in="query",
    *       description="A list of relatonship",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           example={"relationship1","relationship2"},
    *           @OA\Items(type="string")
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="per_page",
    *       in="query",
    *       description="Items per page",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=5,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="page",
    *       in="query",
    *       description="Page",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=1,
    *       )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\Items(ref="#/components/schemas/VehiclePaginate")
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred"
    *     )
    * )
    */

    public function vehicleDefleet(Request $request)
    {

        return $this->getDataResponse(['vehicles' => $this->vehicleRepository->vehicleDefleet($request)], HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/vehicles/delete/{id}",
     *     summary="Delete vehicle",
     *     tags={"vehicles"},
     *     operationId="deleteVehicle",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The id that needs to be deleted",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="",
     *         value = @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Vehicle not found",
     *     ),
     * ),
     */

    public function delete($id)
    {

        return $this->deleteDataResponse($this->vehicleRepository->delete($id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles/return/{id}",
     *     tags={"vehicles"},
     *     summary="Return vehicle",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     operationId="returnVehicle",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *        @OA\JsonContent(ref="#/components/schemas/Vehicle")
     *     ),
     *     @OA\RequestBody(
     *         description="ID of Vehicle",
     *         required=true,
     *         value=@OA\JsonContent(
     *              @OA\Property(
     *                         property="states",
     *                         type="array",
     *                         @OA\Items(type="integer"),
     *                         ref="#/components/schemas/State"
     *               ),
     *               @OA\Property(
     *                         property="date_start",
     *                         type="string",
     *                         format="date-time"
     *               ),
     *               @OA\Property(
     *                         property="date_end",
     *                         type="string",
     *                         format="date-time"
     *               ),
     *               @OA\Property(
     *                         property="campas",
     *                         type="array",
     *                         @OA\Items(type="integer"),
     *                         ref="#/components/schemas/Campa"
     *               ),
     *          ),
     *     )
     * )
     */

    public function returnVehicle(Request $request, $id)
    {
        $data = $this->vehicleRepository->returnVehicle($request, $id);
        return $this->deleteDataResponse($data, HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles/delete-massive",
     *     tags={"vehicles"},
     *     summary="Delete Massive",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="deleteMassive",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         value = @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Vehicles deleted!"
     *              ),
     *          ),
     *     ),
     *     @OA\RequestBody(
     *         description="Plates",
     *         required=true,
     *         value= @OA\JsonContent(
     *              @OA\Property(
     *                  property="plates",
     *                  type="array",
     *                  @OA\Items(type="string"),
     *              ),
     *         )
     *     )
     * )
     */

    public function deleteMassive(Request $request)
    {
        return $this->deleteDataResponse($this->vehicleRepository->deleteMassive($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles/create-from-excel",
     *     summary="Create from Excel",
     *     tags={"vehicles"},
     *     operationId="createFromExcel",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=201,
     *         description="",
     *         value = @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Vehicles created!"
     *              ),
     *          ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/Vehicle")
     *         ),
     *     )
     * )
     */

    public function createFromExcel(Request $request)
    {

        $this->validate($request, [
            'vehicles' => 'required'
        ]);

        return $this->createDataResponse($this->vehicleRepository->createFromExcel($request), HttpFoundationResponse::HTTP_CREATED);
    }

    /**
    * @OA\Get(
    *     path="/api/vehicle-with-reservation-without-order/campa",
    *     tags={"vehicles"},
    *     summary="get Vehicles With Reservation Without Order Campa",
    *     security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         value= @OA\JsonContent(
    *           type="array",
    *           @OA\Items(ref="#/components/schemas/Vehicle")
    *         ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description=""
    *     )
    * )
    */

    public function getVehiclesWithReservationWithoutOrderCampa(Request $request)
    {

        return $this->getDataResponse($this->vehicleRepository->getVehiclesWithReservationWithoutOrderCampa($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicle-with-reservation-without-contract/campa",
     *     summary="get vehicle with reservation without contract campa",
     *     tags={"vehicles"},
     *     operationId="getVehiclesWithReservationWithoutContractCampa",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=201,
     *         description="",
     *         value = @OA\JsonContent(
     *              @OA\Property(
     *                  property="vehicles",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Vehicle")
     *              ),
     *          ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *           @OA\Property(
     *              property="with[]",
     *              type="array",
     *              @OA\Items(type="string")
     *           )
     *         ),
     *     )
     * )
     */

    public function getVehiclesWithReservationWithoutContractCampa(Request $request)
    {

        $this->validate($request, [
            'campas' => 'required'
        ]);

        return $this->getDataResponse($this->vehicleRepository->getVehiclesWithReservationWithoutContractCampa($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/vehicles/filter",
    *     tags={"vehicles"},
    *     summary="Get vehicle filter",
    *     security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Parameter(
    *       name="with[]",
    *       in="query",
    *       description="A list of relatonship",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           @OA\Items(type="string")
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="per_page",
    *       in="query",
    *       description="Items per page",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=15,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="page",
    *       in="query",
    *       description="Page",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=1,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="campaNull",
    *       in="query",
    *       description="Campa null",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=0,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="withUbication",
    *       in="query",
    *       description="With Ubication",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=1,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="approvedPendingTasksNotNull",
    *       in="query",
    *       description="approved pending tasks not null",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=1,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="plate",
    *       in="query",
    *       description="Plate",
    *       required=false,
    *       @OA\Schema(
    *           type="string",
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="defleetingAndDelivery",
    *       in="query",
    *       description="defleetingAndDelivery",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=1,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="campas[]",
    *       in="query",
    *       description="A list of campas",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           example={1,2},
    *           @OA\Items(type="integer")
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="stateNotIds[]",
    *       in="query",
    *       description="A list of stateNotIds",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           example={10,4,5},
    *           @OA\Items(type="integer")
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="campaIds[]",
    *       in="query",
    *       description="A list of campaIDs",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           example={1,2},
    *           @OA\Items(type="integer")
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="states[]",
    *       in="query",
    *       description="A list of states",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           example={1,2},
    *           @OA\Items(type="integer")
    *       )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         value= @OA\JsonContent(
    *           @OA\Property(
    *                  property="vehicles",
    *                  type="object",
    *                  ref="#/components/schemas/VehiclePaginate"
    *            )
    *         ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred",
    *     )
    * )
    */

    public function filterVehicle(Request $request)
    {
        return $this->getDataResponse($this->vehicleRepository->filterVehicle($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/vehicles/reserved",
    *     tags={"vehicles"},
    *     summary="get vehicle reserved",
    *     security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         value= @OA\JsonContent(
    *           type="array",
    *           @OA\Items(ref="#/components/schemas/Vehicle")
    *         ),
    *     ),
    *     @OA\Response(
    *         response="404",
    *         description="Vehicle not found."
    *     )
    * )
    */

    public function vehicleReserved(Request $request)
    {
        return $this->getDataResponse($this->vehicleRepository->vehicleReserved($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/vehicles/totals/by-state",
    *     tags={"vehicles"},
    *     summary="get total vehicle by state",
    *     security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         value= @OA\JsonContent(
    *           type="array",
    *           @OA\Items(ref="#/components/schemas/Vehicle")
    *         ),
    *     ),
    *     @OA\Response(
    *         response="404",
    *         description="Vehicle not found."
    *     )
    * )
    */

    public function vehicleTotalsState(Request $request)
    {
        return $this->getDataResponse($this->vehicleRepository->vehicleTotalsState($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles/verify-plate-reception",
     *     summary="Verify plate reception",
     *     tags={"vehicles"},
     *     operationId="verifyPlateReception",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value = @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *              ),
     *          ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *           @OA\Property(
     *                  property="plate",
     *                  type="array",
     *                  @OA\Items(type="string"),
     *            )
     *         )
     *     )
     * )
     */

    public function verifyPlateReception(Request $request)
    {

        $this->validate($request, [
            'plate' => 'required|string',
        ]);

        return $this->vehicleRepository->verifyPlateReception($request);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles/by-state-date",
     *     summary="Vehicles by state",
     *     tags={"vehicles"},
     *     operationId="vehicleByState",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value= @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/Vehicle")
     *         ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *           @OA\Property(
     *                  property="plate",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/State")
     *            )
     *         )
     *     )
     * )
     */

    public function vehicleByState(Request $request)
    {

        $this->validate($request, [
            'states' => 'required',
            'date_start' => 'required|date',
            'date_end' => 'required|date',
            'campas' => 'required'
        ]);

        return $this->getDataResponse($this->vehicleRepository->vehiclesByState($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles/change-sub-state",
     *     tags={"vehicles"},
     *     summary="Change substate",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="changeSubStateVehicle",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/DeliveryNote"),
     *     ),
     *     @OA\RequestBody(
     *         description="Change Vehicle Sub State",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/VehicleChangeSubState"),
     *     )
     * )
     */

    public function changeSubState(Request $request)
    {
        return $this->updateDataResponse($this->vehicleRepository->changeSubState($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/vehicles/update-sub-state/{id}",
     *     tags={"vehicles"},
     *     summary="Update sub state",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="updateSubStateVehicle",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="id that to be updated",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\Items(ref="#/components/schemas/Vehicle")
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Vehicle not found"
     *     ),
     * )
     */

    public function updateSubStateVehicle($id)
    {
        return $this->updateDataResponse($this->vehicleRepository->updateSubStateVehicle($id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles/set-vehicle-rented",
     *     tags={"vehicles"},
     *     summary="Set vehicle renteed",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="setVehicleRented",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         value = @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Done!"
     *              ),
     *          ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value= @OA\JsonContent(
     *              @OA\Property(
     *                  property="vehicles",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Vehicle"),
     *              ),
     *         )
     *     )
     * )
     */

    public function setVehicleRented(Request $request)
    {
        return $this->updateDataResponse($this->vehicleRepository->setVehicleRented($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/defleeting",
     *     summary="Defleeting",
     *     tags={"vehicles"},
     *     operationId="defleeting",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=201,
     *         description="",
     *         value = @OA\JsonContent(
     *              @OA\Property(
     *                  property="vehicles",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Vehicle")
     *              ),
     *          ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *           @OA\Property(
     *              property="plates",
     *              type="array",
     *              @OA\Items(type="string")
     *           )
     *         ),
     *     )
     * )
     */

    public function defleeting($id)
    {
        $data = $this->vehicleRepository->defleeting($id);
        if (is_array($data) && $data['status'] === FALSE){
            return $this->updateDataResponse($data, HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        return $this->updateDataResponse($data, HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/vehicles/comments",
     *     tags={"vehicles"},
     *     summary="Create vehicle comments",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createVehicleComments",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/VehicleCommentWithUser"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create Vehicle object",
     *         required=true,
     *         value = @OA\JsonContent(
     *           @OA\Property(
     *                  property="description",
     *                  type="string",
     *            ),
     *            @OA\Property(
     *                  property="vehicle_id",
     *                  type="integer",
     *                  format="int64"
     *            )
     *         ),
     *     )
     * )
     */

    public function comments(Request $request) {
        $data = $this->vehicleRepository->comments($request->all());
        return $this->updateDataResponse($data, HttpFoundationResponse::HTTP_OK);
    }
}
