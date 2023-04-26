<?php

namespace App\Http\Controllers;

use App\Models\PeopleForReport;
use App\Repositories\PeopleForReportRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PeopleForReportController extends Controller
{

    public function __construct(PeopleForReportRepository $peopleForReportRepository)
    {
        $this->peopleForReportRepository = $peopleForReportRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/people-for-report",
    *     tags={"people-for-reports"},
    *     summary="Get all people for reports",
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
    *         @OA\JsonContent(ref="#/components/schemas/PeopleForReportPaginate"),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getAll(Request $request){
        return $this->getDataResponse($this->peopleForReportRepository->getAll($request), Response::HTTP_OK);
    }

}
