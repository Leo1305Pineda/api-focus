<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupTask;
use PHPUnit\TextUI\XmlConfiguration\Group;
use App\Repositories\GroupTaskRepository;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class GroupTaskController extends Controller
{

    public function __construct(GroupTaskRepository $groupTaskRepository)
    {
        $this->groupTaskRepository = $groupTaskRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/grouptasks/getall",
    *     tags={"group-tasks"},
    *     summary="Get all group tasks",
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
    *         @OA\JsonContent(ref="#/components/schemas/GroupTaskPaginate"),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getAll(Request $request){
        return $this->getDataResponse($this->groupTaskRepository->getAll($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/grouptasks/{id}",
    *     tags={"group-tasks"},
    *     summary="Get group task by ID",
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
    *         @OA\JsonContent(ref="#/components/schemas/GroupTask"),
    *    ),
    *     @OA\Response(
    *         response="404",
    *         description="Group Task not found."
    *     )
    * )
    */

    public function getById(Request $request, $id){
        return $this->getDataResponse($this->groupTaskRepository->getById($request, $id),HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/grouptasks",
     *     tags={"group-tasks"},
     *     summary="Create group task",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createGroupTask",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/GroupTask"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create group task object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GroupTask"),
     *     )
     * )
     */

    public function create(Request $request){

        $this->validate($request, [
            'vehicle_id' => 'required|integer'
        ]);

        return $this->createDataResponse($this->groupTaskRepository->create($request->all()), HttpFoundationResponse::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/grouptasks/update/{id}",
     *     tags={"group-tasks"},
     *     summary="Updated group task",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Updated group task object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/GroupTask")
     *     ),
     *     operationId="updateGroupTask",
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
     *         @OA\JsonContent(ref="#/components/schemas/GroupTask"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Group Task not found"
     *     ),
     * )
     */

    public function update(Request $request, $id){
        return $this->updateDataResponse($this->groupTaskRepository->update($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/grouptasks/delete/{id}",
     *     summary="Delete group task",
     *     tags={"group-tasks"},
     *     operationId="deleteGroupTask",
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
     *         description="Group task not found",
     *     )
     * )
     */

    public function delete($id){
        GroupTask::where('id', $id)
            ->delete();

        return [ 'message' => 'Group task deleted' ];
    }

    /**
     * @deprecate
     * @OA\Post(
     *     path="/api/grouptasks/approved-available",
     *     summary="Approved available",
     *     tags={"group-tasks"},
     *     operationId="ApprovedAvailable",
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
     *                     required={"vehicle_id","questionnaire_id"},
     *                     @OA\Property(
     *                         property="vehicle_id",
     *                         type="integer",
     *                      ),
     *                      @OA\Property(
     *                         property="questionnaire_id",
     *                         type="integer",
     *                      ),
     *                   ),
     *          )
     *     )
     * )
     */

    public function approvedGroupTaskToAvailable(Request $request){
        $data = $this->groupTaskRepository->approvedGroupTaskToAvailable($request);
        if (!is_null($data)) {
            return $this->updateDataResponse($data, HttpFoundationResponse::HTTP_OK);
        } else {
            return $this->updateDataResponse($data, HttpFoundationResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/grouptasks/decline",
     *     summary="Decline group task",
     *     tags={"group-tasks"},
     *     operationId="DeclineGroupTask",
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
     *                   example="Solicitud declinada!"
     *              )
     *          ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="",
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *                     required={"vehicle_id","group_task_id"},
     *                     @OA\Property(
     *                         property="vehicle_id",
     *                         type="integer",
     *                      ),
     *                      @OA\Property(
     *                         property="group_task_id",
     *                         type="integer",
     *                      ),
     *                   ),
     *          )
     *     )
     * )
     */

    public function declineGroupTask(Request $request){
        return $this->updateDataResponse($this->groupTaskRepository->declineGroupTask($request), HttpFoundationResponse::HTTP_OK);
    }

}
