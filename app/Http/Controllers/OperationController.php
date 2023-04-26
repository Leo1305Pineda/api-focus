<?php

namespace App\Http\Controllers;

use App\Repositories\OperationRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class OperationController extends Controller
{
    public function __construct(OperationRepository $operationRepository)
    {
        $this->operationRepository = $operationRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/operations",
    *     tags={"operations"},
    *     summary="Get all type operations",
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
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         value= @OA\JsonContent(
    *           type="array",
    *           @OA\Items(ref="#/components/schemas/Operation")
    *         ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getAll(Request $request){
        return $this->getDataResponse($this->operationRepository->getAll($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/operations/{id}",
    *     tags={"operations"},
    *     summary="Get operation by ID",
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
    *         @OA\JsonContent(ref="#/components/schemas/Operation"),
    *    ),
    *     @OA\Response(
    *         response="404",
    *         description="Operation not found."
    *     )
    * )
    */

    public function getById(Request $request, $id){
        return $this->getDataResponse($this->operationRepository->getById($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/operations",
     *     tags={"operations"},
     *     summary="Create operation",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createOperation",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Operation"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create operation object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Operation"),
     *     )
     * )
     */

    public function create(Request $request){
        $this->validate($request, [
            'vehicle_id' => 'required|integer',
            'pending_task_id' => 'required|integer',
            'operation_type_id' => 'required|integer',
            'description' => 'required|string',
        ]);
        return $this->createDataResponse($this->operationRepository->create($request), HttpFoundationResponse::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/operations/{id}",
     *     tags={"operations"},
     *     summary="Updated operation model",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Updated operation object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Operation")
     *     ),
     *     operationId="updateOperation",
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
     *         @OA\JsonContent(ref="#/components/schemas/Operation"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Operation not found"
     *     ),
     * )
     */

    public function update(Request $request, $id){
        return $this->updateDataResponse($this->operationRepository->update($request, $id), HttpFoundationResponse::HTTP_OK);
    }
}
