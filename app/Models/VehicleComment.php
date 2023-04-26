<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Vehicle Comment
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Vehicle Comment model",
 *     description="Vehicle Comment model",
 * )
 */
class VehicleComment extends Model
{
    /**
     * @OA\Schema(
     *      schema="VehicleCommentWithUser",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/VehicleComment"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="user",
     *                  type="object",
     *                  ref="#/components/schemas/User"
     *              ),
     *          ),
     *      },
     * )
     * @OA\Property(
     *     property="id",
     *     type="integer",
     *     format="int64",
     *     description="ID",
     *     title="ID",
     * )
     *
     * @OA\Property(
     *     property="vehicle_id",
     *     type="integer",
     *     format="int64",
     *     description="Vehicle ID",
     *     title="Vehicle ID",
     * )
     *
     * @OA\Property(
     *     property="reception_id",
     *     type="integer",
     *     format="int64",
     *     description="Reception ID",
     *     title="Reception ID",
     * )
     *
     * @OA\Property(
     *     property="user_id",
     *     type="integer",
     *     format="int64",
     *     description="User ID",
     *     title="User ID",
     * )
     *
     * @OA\Property(
     *     property="description",
     *     type="string",
     *     description="Description",
     *     title="Description",
     * )
     *
     * @OA\Property(
     *     property="created_at",
     *     type="string",
     *     format="date-time",
     *     description="When was created",
     *     title="Created at",
     * )
     *
     * @OA\Property(
     *     property="updated_at",
     *     type="string",
     *     format="date-time",
     *     description="When was last updated",
     *     title="Updated at",
     * )
     */

    use HasFactory, Filterable;

    protected $table = 'vehicle_comments';

    protected $fillable = [
        'vehicle_id',
        'reception_id',
        'user_id',
        'description'
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }

    public function reception(){
        return $this->belongsTo(Reception::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
