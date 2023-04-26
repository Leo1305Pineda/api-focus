<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetCode;
use App\Repositories\PasswordResetCodeRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PasswordResetCodeController extends Controller
{

    public function __construct(PasswordResetCodeRepository $passwordResetCodeRepository)
    {
        $this->passwordResetCodeRepository = $passwordResetCodeRepository;
    }


    /**
    * @OA\Get(
    *     path="/api/password-reset-code",
    *     tags={"password-reset-codes"},
    *     summary="Get all password reset codes",
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
    *         @OA\JsonContent(ref="#/components/schemas/PasswordResetCodePaginate"),
    *    ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getAll(Request $request){
        return $this->getDataResponse($this->passwordResetCodeRepository->getAll($request), Response::HTTP_OK);
    }


}
