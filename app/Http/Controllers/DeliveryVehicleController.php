<?php

namespace App\Http\Controllers;

use App\Exports\DeliveryVehiclesExport;
use App\Repositories\DeliveryVehicleRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class DeliveryVehicleController extends Controller
{

    public function __construct(DeliveryVehicleRepository $deliveryVehicleRepository)
    {
        $this->deliveryVehicleRepository = $deliveryVehicleRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/delivery-vehicles",
    *     tags={"delivery-vehicles"},
    *     summary="Get all delivery vehicles",
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
    *       name="pendindTaskNull",
    *       in="query",
    *       description="Pending task null",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=0,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="vehicleDeleted",
    *       in="query",
    *       description="Vehicle deleted",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=0,
    *       )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\Items(ref="#/components/schemas/DeliveryVehiclePaginate")
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->getDataResponse($this->deliveryVehicleRepository->index($request), Response::HTTP_OK);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * @OA\Delete(
     *     path="/api/delivery-vehicles/{id}",
     *     summary="Delete delivery vehicle",
     *     tags={"delivery-vehicles"},
     *     operationId="deleteDeliveryVehicle",
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
     *         response=200,
     *         description="",
     *         value = @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Delivery vehicle not found",
     *     )
     * )
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       return $this->deleteDataResponse($this->deliveryVehicleRepository->delete($id), Response::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/delivery-vehicles/export",
    *     tags={"delivery-vehicles"},
    *     summary="get delivery vehicles export",
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *          @OA\MediaType(
    *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    *         ),
    *     ),
    * )
    */

    public function export(Request $request)
    {
        ini_set("memory_limit", "-1");
        ini_set('max_execution_time', '-1');
        $date = microtime(true);
        $array = explode('.', $date);
        ob_clean();
        return Excel::download(new DeliveryVehiclesExport($request->all()), 'Salidas-' . date('d-m-Y') . '-' . $array[0] . '.xlsx');
    }
}
