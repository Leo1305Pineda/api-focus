<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Role;
use App\Models\Campa;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="User model",
 *     description="User model",
 * )
 */

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{

    /**
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     format="int64",
     *     description="ID",
     *     title="ID",
     * )
     *
     * @OA\Property(
     *     property="role_id",
     *     type="integer",
     *     format="int64",
     *     description="Role ID",
     *     title="Role ID",
     * )
     *
     * @OA\Property(
     *     property="type_user_app_id",
     *     type="integer",
     *     format="int64",
     *     description="Type of the user in the app ID",
     *     title="Type of User ID",
     * )
     *
     * @OA\Property(
     *     property="workshop_id",
     *     type="integer",
     *     format="int64",
     *     description="Workshop of the user ID",
     *     title="Workshop ID",
     * )
     *
     *
     * @OA\Property(
     *     property="name",
     *     type="string",
     *     description="Name of the user",
     *     title="Name",
     * )
     *
     *
     *
     * @OA\Property(
     *     property="surname",
     *     type="string",
     *     description="Surname of the user",
     *     title="Surname",
     * )
     *
     *
     * @OA\Property(
     *     property="password",
     *     type="string",
     *     format="password",
     *     description="Password of the user",
     *     title="Password",
     * )
     *
     *
     * @OA\Property(
     *     property="email",
     *     type="string",
     *     format="email",
     *     description="Email of the user",
     *     title="Email",
     * )
     *
     *
     * @OA\Property(
     *     property="avatar",
     *     type="string",
     *     description="Avatar of the user",
     *     title="Avatar",
     * )
     *
     *
     *
     * @OA\Property(
     *     property="phone",
     *     type="integer",
     *     format="int32",
     *     description="Phone of the user",
     *     title="Phone",
     * )
     *
     * @OA\Property(
     *     property="first_login",
     *     type="boolean",
     *     description="First login of the user",
     *     title="First login",
     * )
     *
     *
     * @OA\Property(
     *     property="active",
     *     type="boolean",
     *     description="Status of the user",
     *     title="Active",
     * )
     *
     *
     * @OA\Property(
     *     property="created_at",
     *     type="string",
     *     format="date-time",
     *     description="When was created",
     *     title="Created at",
     * )
     *
     *
     * @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     format="date-time",
     *     description="When was last updated",
     *     title="Updated at",
     * )
     *
     *
     * @OA\Property(
     *     property="company_id",
     *     type="integer",
     *     format="int64",
     *     description="Company of the user",
     *     title="Company ID",
     * )
     *
     *
     * @OA\Property(
     *     property="deleted_at",
     *     type="string",
     *     format="date-time",
     *     description="When was deleted",
     *     title="Deleted at",
     * )
     *
     * @OA\Schema(
     *      schema="UserWithCampasAndRole",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/User"),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="role",
     *                   ref="#/components/schemas/Role"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="campas",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Campa")
     *              ),
     *          )
     *      }
     * )
     *
     * @OA\Schema(
     *      schema="UserWithCampasAndCompany",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/User"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="campas",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Campa")
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="company",
     *                   ref="#/components/schemas/Company"
     *              )
     *          ),
     *      },
     * ),
     * @OA\Schema(
     *      schema="UserWithCampasRoleAndCompany",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/User"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="campas",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Campa")
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="company",
     *                   ref="#/components/schemas/Company"
     *              )
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                   property="role",
     *                   ref="#/components/schemas/Role"
     *              )
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="UserPaginate",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Paginate"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Notification"),
     *              ),
     *          ),
     *      },
     * )
     */

    use Authenticatable, Authorizable, HasFactory, Notifiable;

    protected $fillable = [
        'company_id',
        'role_id',
        'type_user_app_id',
        'name',
        'surname',
        'email',
        'avatar',
        'phone',
        'first_login',
        'active'
    ];

    protected $hidden = [
        'password',
    ];

    protected $dates = [
        'deleted_at'
    ];

    public function campas(){
        return $this->belongsToMany(Campa::class);
    }

    public function role(){
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function type_user_app(){
        return $this->belongsTo(TypeUserApp::class);
    }

    public function peopleForReports(){
        return $this->hasMany(PeopleForReport::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

}
