<?php

namespace App\Http\Controllers;

use App\Repositories\IncidenceImageRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncidenceImageController extends Controller
{

    public function __construct(IncidenceImageRepository $incidenceImageRepository)
    {
        $this->incidenceImageRepository = $incidenceImageRepository;
    }
    /**
    * @OA\Get(
    *     path="/api/incidence-images",
    *     tags={"incidence-images"},
    *     summary="Get all incidence images",
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
    *         @OA\JsonContent(ref="#/components/schemas/IncidenceImagePaginate")
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    /**
     * Display a listing of the resource.
     * @param \ilumiante\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->getDataResponse($this->incidenceImageRepository->index($request), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/incidence-images",
     *     tags={"incidence-images"},
     *     summary="Create incidence image",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createIncidenceImage",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/IncidenceImage"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create incidence image object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/IncidenceImage"),
     *     )
     * )
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->createDataResponse($this->incidenceImageRepository->store($request), Response::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/incidence-images/{id}",
     *     tags={"incidence-images"},
     *     summary="Updated incidence images",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Updated incidence images object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/IncidenceImage")
     *     ),
     *     operationId="updateIncidenceImage",
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
     *         @OA\JsonContent(ref="#/components/schemas/IncidenceImage"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Incidence image not found"
     *     ),
     * )
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return $this->updateDataResponse($this->incidenceImageRepository->update($request, $id), Response::HTTP_OK);
    }

}
