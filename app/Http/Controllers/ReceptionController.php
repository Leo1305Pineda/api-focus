<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ReceptionRepository;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class ReceptionController extends Controller
{
    protected $receptionRepository;
    public function __construct(ReceptionRepository $receptionRepository)
    {
        $this->receptionRepository = $receptionRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/reception",
    *     tags={"receptions"},
    *     summary="Get all receptions",
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
    *       name="subStatesNotIds[]",
    *       in="query",
    *       description="A list of subStatesNotIds",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           example={1,2},
    *           @OA\Items(type="integer")
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="vehiclePlate",
    *       in="query",
    *       description="Plate",
    *       required=false,
    *       @OA\Schema(
    *           type="string",
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="whereHasVehicle",
    *       in="query",
    *       description="Where has vehicle",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=0,
    *       )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\Items(ref="#/components/schemas/ReceptionPaginate")
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function index(Request $request)
    {
        return $this->getDataResponse($this->receptionRepository->index($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/reception",
     *     tags={"receptions"},
     *     summary="Create reception",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createReception",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Reception"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create reception object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Reception"),
     *     )
     * )
     */

    public function create(Request $request)
    {

        $this->validate($request, [
            'vehicle_id' => 'required|integer'
        ]);
        $data = $this->receptionRepository->create($request);
        return $this->createDataResponse($data, HttpFoundationResponse::HTTP_CREATED);
    }

    /**
    * @OA\Get(
    *     path="/api/reception/{id}",
    *     tags={"receptions"},
    *     summary="Get reception by ID",
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
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         @OA\JsonContent(ref="#/components/schemas/Reception"),
    *    ),
    *     @OA\Response(
    *         response="404",
    *         description="Reception not found."
    *     )
    * )
    */

    public function getById($id)
    {
        return $this->getDataResponse($this->receptionRepository->getById($id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/reception/{id}",
     *     tags={"receptions"},
     *     summary="Updated reception",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Updated reception object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Reception")
     *     ),
     *     operationId="updateReception",
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
     *         @OA\JsonContent(ref="#/components/schemas/Reception"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Reception not found"
     *     ),
     * )
     */

    public function updateReception(Request $request, $id)
    {
        return $this->updateDataResponse($this->receptionRepository->updateReception($request, $id), HttpFoundationResponse::HTTP_OK);
    }
}
