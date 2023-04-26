<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class UserController extends Controller
{
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
    * @OA\Get(
    *     path="/api/users/getall",
    *     tags={"users"},
    *     summary="Get all users",
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
    *           @OA\Items(ref="#/components/schemas/UserWithCampasRoleAndCompany")
    *         ),
    *     ),
    *     @OA\Response(
    *         response="500",
    *         description="An error has occurred."
    *     )
    * )
    */
    public function getAll(Request $request){
        return $this->getDataResponse($this->userRepository->getAll($request), HttpFoundationResponse::HTTP_OK);
    }

    /**
    * @OA\Get(
    *     path="/api/users/{id}",
    *     tags={"users"},
    *     summary="Get user by ID",
    *     security={
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
    *     @OA\Response(
    *         response=200,
    *         description="Successful operation",
    *         value=@OA\JsonContent(
    *                  allOf = {
    *                      @OA\Schema(ref="#/components/schemas/User"),
    *                      @OA\Schema(
    *                          @OA\Property(
    *                              property="campas",
    *                              type="array",
    *                              @OA\Items(ref="#/components/schemas/Campa")
    *                           ),
    *                        ),
    *                  }
    *         )
    *    ),
    *     @OA\Response(
    *         response="404",
    *         description="User not found."
    *     )
    * )
    */

    public function getById(Request $request, $id){
        return $this->getDataResponse($this->userRepository->getById($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"users"},
     *     summary="Create user",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createUser",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     */

    public function create(Request $request){

        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string'
        ]);

        return $this->createDataResponse($this->userRepository->create($request), HttpFoundationResponse::HTTP_CREATED);
    }

    /**
     * @OA\Post(
     *     path="/api/users/create-without-password",
     *     tags={"users"},
     *     summary="Create user without password",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     operationId="createUserWithoutPassword",
     *     @OA\Response(
     *         response="201",
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     *     @OA\RequestBody(
     *         description="Create user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     )
     * )
     */

    public function createUserWithoutPassword(Request $request){

        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users'
        ]);

        return $this->createDataResponse($this->userRepository->createUserWithoutPassword($request), HttpFoundationResponse::HTTP_CREATED);
    }

    /**
     * @OA\Put(
     *     path="/api/users/update/{id}",
     *     tags={"users"},
     *     summary="Updated user",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         description="Updated user object",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     operationId="updateUser",
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
     *         @OA\JsonContent(ref="#/components/schemas/User"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found"
     *     ),
     * )
     */

    public function update(Request $request, $id){
        return $this->updateDataResponse($this->userRepository->update($request, $id), HttpFoundationResponse::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/delete/{id}",
     *     summary="Delete user",
     *     tags={"users"},
     *     operationId="deleteUser",
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
     *                  example="User deleted",
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *     )
     * )
     */

    public function delete($id){
        return $this->deleteDataResponse($this->userRepository->delete($id), HttpFoundationResponse::HTTP_OK);
    }
    /**
     * @OA\Post(
     *     path="/api/users/role/{role_id}",
     *     summary="get Users By Role",
     *     tags={"users"},
     *     operationId="getUsersByRole",
     *     security={
     *          {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="role_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="",
     *         value=@OA\JsonContent(
     *                  allOf = {
     *                      @OA\Schema(ref="#/components/schemas/User"),
     *                      @OA\Schema(
     *                          @OA\Property(
     *                              property="campas",
     *                              type="array",
     *                              @OA\Items(ref="#/components/schemas/Campa")
     *                           ),
     *                        ),
     *                      @OA\Schema(
     *                          @OA\Property(
     *                              property="company",
     *                              ref="#/components/schemas/Company"
     *                          )
     *                      ),
     *                  },
     *          ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *     ),
     *     @OA\RequestBody(
     *         description="",
     *         required=true,
     *         value = @OA\JsonContent(
     *              @OA\Property(
     *                  property="with[]",
     *                  example={"relationship_name:campas,company"},
     *                  type="array",
     *                  @OA\Items(
     *                      type="string",
     *                  ),
     *              ),
     *              @OA\Property(
     *                  property="campas",
     *                  type="array",
     *                  @OA\Items(
     *                      type="integer",
     *                  ),
     *              ),
     *         )
     *     )
     * )
     */
    public function getUsersByRole(Request $request, $role_id){
        return $this->getDataResponse($this->userRepository->getUsersByRole($request, $role_id), HttpFoundationResponse::HTTP_OK);
    }
    /**
    * @OA\Get(
    *     path="/api/users/notifications",
    *     tags={"users"},
    *     summary="Get user notification",
    *     security={
    *          {"bearerAuth": {}}
    *     },
    *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/UserPaginate"),
     *     ),
    *     @OA\Response(
    *         response="404",
    *         description="User not found."
    *     )
    * )
    */
    public function notifications(Request $request){
        return $this->getDataResponse($this->userRepository->notifications($request), HttpFoundationResponse::HTTP_OK);
    }
}
