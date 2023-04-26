<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Repositories\LoginLogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginLogController extends Controller
{

    public function __construct(LoginLogRepository $loginLogRepository)
    {
        $this->loginLogRepository = $loginLogRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/login-logs",
    *     tags={"login-logs"},
    *     summary="Get all login logs",
    *     security={
    *          {"bearerAuth": {}}
    *     },
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
    *         @OA\JsonContent(ref="#/components/schemas/LoginLogPaginate"),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getAll(Request $request){
        return $this->getDataResponse($this->loginLogRepository->getAll($request), Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/login-logs",
     *     tags={"login-logs"},
     *     summary="Create login log",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createLoginLog",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LoginLog"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create login log object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LoginLog"),
     *     )
     * )
     */

    public function create(Request $request){
        $loginLog = new LoginLog();
        $loginLog->user_id = Auth::id();
        $loginLog->device_description = $request->input('device_description');
        $loginLog->save();
        return $loginLog;
    }

    /**
     * @OA\Post(
     *     path="/api/login-logs/by-user",
     *     tags={"login-logs"},
     *     summary="Get User",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="getUser",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/LoginLog"),
     *     ),
     *     @OA\RequestBody(
     *         description="Get login log",
     *         required=true,
     *         value=@OA\JsonContent(
     *                     @OA\Property(
     *                         property="user_id",
     *                         type="integer",
     *                     )
     *          ),
     *     )
     * )
     */

    public function getUser(Request $request){
        return LoginLog::where('user_id', $request->input('user_id'))
                    ->get();
    }


}
