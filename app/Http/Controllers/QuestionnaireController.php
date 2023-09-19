<?php

namespace App\Http\Controllers;

use App\Models\CampaUser;
use App\Models\Questionnaire;
use App\Models\User;
use App\Notifications\ValidatedCheckListNotification;
use App\Repositories\QuestionnaireRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class QuestionnaireController extends Controller
{

    protected $questionnaireRepository;
    
    public function __construct(QuestionnaireRepository $questionnaireRepository)
    {
        $this->questionnaireRepository = $questionnaireRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/questionnaire/",
    *     tags={"questionnaires"},
    *     summary="Get all questionnaires",
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
    *         @OA\Items(ref="#/components/schemas/QuestionnairePaginate")
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function index(Request $request){
        return $this->getDataResponse($this->questionnaireRepository->index($request), HttpFoundationResponse::HTTP_OK);
    }

    public function create($vehicle_id){
        return $this->createDataResponse($this->questionnaireRepository->create($vehicle_id), HttpFoundationResponse::HTTP_CREATED);
    }

    /**
    * @OA\Get(
    *     path="/api/questionnaire/{id}",
    *     tags={"questionnaires"},
    *     summary="Get questionnaire by ID",
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
    *         @OA\JsonContent(ref="#/components/schemas/Questionnaire"),
    *    ),
    *     @OA\Response(
    *         response="404",
    *         description="Questionnaire not found."
    *     )
    * )
    */
    public function getById(Request $request, $id){
        return $this->getDataResponse($this->questionnaireRepository->getById($request, $id));
    }

    /**
     * @OA\Post(
     *     path="/api/questionnaire/approved",
     *     summary="Approved available",
     *     tags={"questionnaires"},
     *     operationId="Approved",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                   property="message",
     *                   type="string",
     *                   example="Solicitud aprobada"
     *              ),
     *              @OA\Property(
     *                   property="vehicle",
     *                   type="string",
     *                   ref="#/components/schemas/Vehicle"
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entity",
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *                     required={"questionnaire_id"},
     *                      @OA\Property(
     *                         property="questionnaire_id",
     *                         type="integer",
     *                      ),
     *                   ),
     *          )
     *     )
     * )
     */

    public function approved(Request $request){
        $data = $this->questionnaireRepository->approved($request);
        if (!is_null($data)) {
             $ids = CampaUser::where('campa_id', $data['vehicle']->campa_id)
             ->get()
             ->pluck('user_id');
             $send_to = User::whereIn('id', $ids)->get();
             $res = response()->json($data);
             foreach ($send_to as $key => $value) {
                Notification::send($value, new ValidatedCheckListNotification($res));
             }
            return $this->updateDataResponse($res, HttpFoundationResponse::HTTP_OK);
        } else {
            return $this->updateDataResponse($data, HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
