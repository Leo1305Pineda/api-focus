<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\User;
use App\Repositories\QuestionRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class QuestionController extends Controller
{

    public function __construct(QuestionRepository $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/questions/getall",
    *     tags={"questions"},
    *     summary="Get all type questions",
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
    *           @OA\Items(ref="#/components/schemas/Question")
    *         ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getAll(Request $request){
        return $this->getDataResponse($this->questionRepository->getAll($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/questions",
     *     tags={"questions"},
     *     summary="Create question",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createQuestion",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Question"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create defleet variable object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Question"),
     *     )
     * )
     */

    public function create(Request $request){

        $this->validate($request, [
            'question' => 'required|string',
            'description' => 'required|string'
        ]);

        return $this->createDataResponse($this->questionRepository->create($request), HttpFoundationResponse::HTTP_CREATED);
    }

    public function filter(Request $request){
        return $this->getDataResponse($this->questionRepository->filter($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/questions/{id}",
     *     summary="Delete question",
     *     tags={"questions"},
     *     operationId="deleteQuestion",
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
     *         description="Question not found",
     *     )
     * )
     */

    public function delete($id){
        return Question::where('id', $id)
                        ->delete();

    }
}
