<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOperation;
use App\Repositories\PurchaseOperationRepository;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class PurchaseOperationController extends Controller
{

    public function __construct(PurchaseOperationRepository $purchaseOperationRepository)
    {
        $this->purchaseOperationRepository = $purchaseOperationRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/purchase-operations/getall",
    *     tags={"purchase-operations"},
    *     summary="Get all purchase operatons",
    *     security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         value= @OA\JsonContent(
    *           type="array",
    *           @OA\Items(ref="#/components/schemas/PurchaseOperation")
    *         ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getAll(){
        return $this->getDataResponse(PurchaseOperation::all(), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/purchase-operations/{id}",
    *     tags={"purchase-operations"},
    *     summary="Get purchase operation by ID",
    *     security={
    *         {"bearerAuth": {}}
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
    *         @OA\JsonContent(ref="#/components/schemas/PurchaseOperation"),
    *    ),
    *     @OA\Response(
    *         response="404",
    *         description="Purchase Operation not found."
    *     )
    * )
    */

    public function getById($id){
        return $this->getDataResponse($this->purchaseOperationRepository->getById($id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/purchase-operations",
     *     tags={"purchase-operations"},
     *     summary="Create purchase operation",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createPurchaseOperation",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PurchaseOperation"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create purchase operation object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PurchaseOperation"),
     *     )
     * )
     */

    public function create(Request $request){

        $this->validate($request, [
            'task_id' => 'required|integer',
            'name' => 'required|string',
            'price' => 'required'
        ]);

        return $this->createDataResponse($this->purchaseOperationRepository->create($request), HttpFoundationResponse::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/purchase-operations/update/{id}",
     *     tags={"purchase-operations"},
     *     summary="Updated purchase operation",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Updated purchase operation object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PurchaseOperation")
     *     ),
     *     operationId="updatePurchaseOperation",
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
     *         @OA\JsonContent(ref="#/components/schemas/PurchaseOperation"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Purchase operation not found"
     *     ),
     * )
     */

    public function update(Request $request, $id){
        return $this->updateDataResponse($this->purchaseOperationRepository->update($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/purchase-operations/delete/{id}",
     *     summary="Delete purchase operation",
     *     tags={"purchase-operations"},
     *     operationId="deletePurchaseOperation",
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
     *         description="Purchase operation not found",
     *     )
     * )
     */

    public function delete($id){
        return $this->deleteDataResponse($this->purchaseOperationRepository->delete($id), HttpFoundationResponse::HTTP_OK);
    }
}
