<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PendingTask;
use App\Http\Controllers\TaskController;
use App\Repositories\PendingTaskRepository;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class PendingTaskController extends Controller
{
    protected $taskController;
    protected $incidenceController;
    protected $vehicleController;
    protected $pendingTaskRepository;
    public function __construct(
        TaskController $taskController,
        IncidenceController $incidenceController,
        VehicleController $vehicleController,
        PendingTaskRepository $pendingTaskRepository
        )
    {
        $this->taskController = $taskController;
        $this->incidenceController = $incidenceController;
        $this->vehicleController = $vehicleController;
        $this->pendingTaskRepository = $pendingTaskRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/pending-tasks/getall",
    *     tags={"pending-tasks"},
    *     summary="Get all pending tasks",
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
    *           @OA\Items(ref="#/components/schemas/PendingTask")
    *         ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getAll(Request $request){
        return $this->getDataResponse($this->pendingTaskRepository->getAll($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/pending-tasks/{id}",
    *     tags={"pending-tasks"},
    *     summary="Get pending task by ID",
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
    *         value= @OA\JsonContent(
    *           @OA\Property(
    *                  property="pending_task",
    *                  type="object",
    *                  ref="#/components/schemas/PendingTask",
    *              ),
    *         ),
    *    ),
    *     @OA\Response(
    *         response="404",
    *         description="Pending Task not found."
    *     )
    * )
    */

    public function getById(Request $request, $id){
        return $this->getDataResponse($this->pendingTaskRepository->getById($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/pending-tasks",
    *     tags={"pending-tasks"},
    *     summary="Get pending task or next task",
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
    *           @OA\Items(ref="#/components/schemas/PendingTask")
    *         ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */

    public function getPendingOrNextTask(Request $request){
        return $this->getDataResponse($this->pendingTaskRepository->getPendingOrNextTask($request));
    }

    /**
    * @OA\Get(
    *     path="/api/pending-tasks/filter",
    *     tags={"pending-tasks"},
    *     summary="Get pending task filter ",
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
    *           example={"vehicle","reception"},
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
    *           example=15,
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
    *     @OA\Parameter(
    *       name="approved",
    *       in="query",
    *       description="Approved",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=1,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="receptionNull",
    *       in="query",
    *       description="Reception null",
    *       required=false,
    *       @OA\Schema(
    *           type="integer",
    *           example=0,
    *       )
    *     ),
    *     @OA\Parameter(
    *       name="campaIds[]",
    *       in="query",
    *       description="A list of campaIDs",
    *       required=false,
    *       @OA\Schema(
    *           type="array",
    *           example={1,2},
    *           @OA\Items(type="integer")
    *       )
    *     ),
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *          @OA\Items(ref="#/components/schemas/PendingTaskPaginate")
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred"
    *     )
    * )
    */


    public function pendingTasksFilter(Request $request){
        return $this->getDataResponse($this->pendingTaskRepository->pendingTasksFilter($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/finish-all",
     *     tags={"pending-tasks"},
     *     summary="Finis all pending tasks",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="finishAllPendingTask",
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PendingTask"),
     *     ),
     *     @OA\RequestBody(
     *         description="Finish all",
     *         required=true,
     *         value= @OA\JsonContent(
     *              @OA\Property(
     *                  property="pending_task_ids",
     *                  type="array",
     *                  example={1,2},
     *                  @OA\Items(type="integer"),
     *              ),
     *         )
     *      )
     * )
     */

    public function finishAll(Request $request) {
        return $this->updateDataResponse($this->pendingTaskRepository->finishAll($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/pending-tasks/update/{id}",
     *     tags={"pending-tasks"},
     *     summary="Updated pending task",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Updated pending task  object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PendingTask")
     *     ),
     *     operationId="updatePendingTask",
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
     *         @OA\JsonContent(ref="#/components/schemas/PendingTask"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pending task not found"
     *     ),
     * )
     */

    public function update(Request $request, $id){
        return $this->updateDataResponse($this->pendingTaskRepository->update($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/pending-tasks/delete/{id}",
     *     summary="Delete pending task",
     *     tags={"pending-tasks"},
     *     operationId="deletePendingTask",
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
     * )
     */

    public function delete($id){
        return $this->deleteDataResponse($this->pendingTaskRepository->delete($id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/start-pending-task",
     *     summary="Start pending task",
     *     tags={"pending-tasks"},
     *     operationId="StartPendingTask",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value= @OA\JsonContent(
     *                      type="array",
     *                      @OA\Items(ref="#/components/schemas/PendingTask")
     *                 ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *                     @OA\Property(
     *                         property="pending_task_id",
     *                         type="integer",
     *                      ),
     *                   ),
     *          )
     *     )
     * )
     */

    public function startPendingTask(Request $request){

        $this->validate($request, [
            'pending_task_id' => 'required|integer'
        ]);

        return $this->updateDataResponse($this->pendingTaskRepository->startPendingTask($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/cancel-pending-task",
     *     summary="Cancel pending task",
     *     tags={"pending-tasks"},
     *     operationId="CancelPendingTask",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value= @OA\JsonContent(
     *                      type="array",
     *                      @OA\Items(ref="#/components/schemas/PendingTask")
     *                 ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *                     @OA\Property(
     *                         property="pending_task_id",
     *                         type="integer",
     *                      ),
     *                   ),
     *          )
     *     )
     * )
     */

    public function cancelPendingTask(Request $request){

        $this->validate($request, [
            'pending_task_id' => 'required|integer'
        ]);

        return $this->updateDataResponse($this->pendingTaskRepository->cancelPendingTask($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/finish-pending-task",
     *     summary="Finish pending task",
     *     tags={"pending-tasks"},
     *     operationId="finishPendingTask",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *                     @OA\Property(
     *                         property="pending_task_id",
     *                         type="integer",
     *                      ),
     *                   ),
     *          )
     *     )
     * )
     */

    public function finishPendingTask(Request $request){

        $this->validate($request, [
            'pending_task_id' => 'required|integer'
        ]);

        return $this->updateDataResponse($this->pendingTaskRepository->finishPendingTask($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/by-state/by-campa",
     *     summary="get Pending Task By StateCampa",
     *     tags={"pending-tasks"},
     *     operationId="getPendingTaskByStateCampa",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value= @OA\JsonContent(
     *                      allOf = {
     *                          @OA\Schema(ref="#/components/schemas/PendingTask"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="campas",
     *                                  type="array",
     *                                  @OA\Items(ref="#/components/schemas/Campa"),
     *                              ),
     *                          ),
     *                      },
     *                )
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *                     @OA\Property(
     *                         property="state_pending_task_id",
     *                         type="integer",
     *                     ),
     *                     @OA\Property(
     *                         property="campas",
     *                         type="array",
     *                         @OA\Items(ref="#/components/schemas/Campa")
     *                     ),
     *                   ),
     *          )
     *     )
     * )
     */

    public function getPendingTaskByStateCampa(Request $request){

        $this->validate($request, [
            'campas' => 'required',
            'state_pending_task_id' => 'required|integer'
        ]);

        return $this->getDataResponse($this->pendingTaskRepository->getPendingTaskByStateCampa($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/by-plate",
     *     summary="Pending task by plate",
     *     tags={"pending-tasks"},
     *     operationId="getPendingTaskByPlate",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value= @OA\JsonContent(
    *           type="array",
    *           @OA\Items(ref="#/components/schemas/PendingTaskWithVehicle")
    *         ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *                     @OA\Property(
     *                         property="plate",
     *                         type="string",
     *                     ),
     *                     @OA\Property(
     *                         property="with[]",
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                     )
     *                   ),
     *          )
     *     )
     * )
     */



    public function getPendingTaskByPlate(Request $request){

        $this->validate($request, [
            'plate' => 'required|string'
        ]);

        return $this->getDataResponse($this->pendingTaskRepository->getPendingTaskByPlate($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/by-vehicle",
     *     summary="Pending task by vehicle",
     *     tags={"pending-tasks"},
     *     operationId="getPendingTaskByVehicle",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value= @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/PendingTask")
     *         ),
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *                     @OA\Property(
     *                         property="with[]",
     *                         type="array",
     *                         @OA\Items(type="string"),
     *                     )
     *                   ),
     *          )
     *     )
     * )
     */

    public function getPendingTasksByPlate(Request $request){
        return $this->getDataResponse($this->pendingTaskRepository->getPendingTasksByPlate($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/order",
     *     summary="Order Pending task",
     *     tags={"pending-tasks"},
     *     operationId="orderPendingTask",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value= @OA\JsonContent(ref="#/components/schemas/PendingTask")
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *                      @OA\Property(
     *                         property="pending_tasks",
     *                         type="object",
     *                         ref="#/components/schemas/PendingTask",
     *                     ),
     *                     @OA\Property(
     *                         property="vehicle_id",
     *                         type="integer",
     *                     ),
     *                 ),
     *     )
     * )
     */

    public function orderPendingTask(Request $request){

        $this->validate($request, [
            'pending_tasks' => 'required',
            'vehicle_id' => 'required|integer'
        ]);

        return $this->pendingTaskRepository->orderPendingTask($request);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/add",
     *     tags={"pending-tasks"},
     *     summary="Create pending task",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="addPendingTask",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PendingTask"),
     *     ),
     *     @OA\RequestBody(
     *         description="Add pending task object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PendingTask"),
     *     )
     * )
     */

    public function addPendingTask(Request $request){
        return $this->createDataResponse($this->pendingTaskRepository->addPendingTask($request), HttpFoundationResponse::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/add-pending-task-finished",
     *     tags={"pending-tasks"},
     *     summary="Add pending task finished",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="AddPendingTaskFinished",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PendingTask"),
     *     ),
     *     @OA\RequestBody(
     *         description="Add pending task finished",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/PendingTask"),
     *     )
     * )
     */

    public function addPendingTaskFinished(Request $request){
        return $this->createDataResponse($this->pendingTaskRepository->addPendingTaskFinished($request), HttpFoundationResponse::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/pending-tasks/transfer",
     *     tags={"pending-tasks"},
     *     summary="Create transfer task",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *         response=201,
     *         description="",
     *         value= @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Task Transfer added!",
     *              ),
     *         )
     *     ),
     *     @OA\RequestBody(
     *         description="Create transfer task",
     *         required=true,
     *         value= @OA\JsonContent(
     *              @OA\Property(
     *                  property="vehicle_ids",
     *                  type="array",
     *                  example={1,2},
     *                  @OA\Items(type="integer"),
     *              ),
     *         )
     *      )
     * )
     */

    public function createTransferTask(Request $request){
        return $this->createDataResponse($this->pendingTaskRepository->createTransferTask($request), HttpFoundationResponse::HTTP_CREATED);
    }

}
