<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\BrandRepository;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class BrandController extends Controller
{
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/brands",
    *     tags={"brands"},
    *     summary="Get all brands",
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
    *              @OA\Property(
    *                  property="brands",
    *                  type="array",
    *                  @OA\Items(ref="#/components/schemas/Brand")
    *              ),
    *          ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function index(Request $request){
        return $this->getDataResponse($this->brandRepository->index($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/brands/{id}",
    *     tags={"brands"},
    *     summary="Get brand by ID",
    *    security={
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
    *              @OA\Property(
    *                  property="brand",
    *                  type="object",
    *                  ref="#/components/schemas/Brand"
    *              ),
    *          ),
    *    ),
    *     @OA\Response(
    *         response="404",
    *         description="Brand not found."
    *     )
    * )
    */

    public function show(Request $request, $id){
        return $this->getDataResponse($this->brandRepository->show($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/brands",
     *     tags={"brands"},
     *     summary="Create brands",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createBrand",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Brand"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create brand object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Brand"),
     *     )
     * )
     */

    public function store(Request $request){
        return $this->createDataResponse($this->brandRepository->store($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/brands/{id}",
     *     tags={"brands"},
     *     summary="Updated brand",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Updated brand object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Brand")
     *     ),
     *     operationId="updateBrand",
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
     *         @OA\JsonContent(ref="#/components/schemas/Brand"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Brand not found"
     *     ),
     * )
     */

    public function update(Request $request, $id){
        return $this->updateDataResponse($this->brandRepository->update($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    public function delete($id){
        return $this->deleteDataResponse($this->brandRepository->delete($id), HttpFoundationResponse::HTTP_OK);
    }
}
