<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Class Notification
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Notification model",
 *     description="Notification model",
 * )
 */

class Notification extends Model
{
    /**
     * @OA\Property(
     *     property="id",
     *     type="string",
     *     description="ID",
     *     title="ID",
     * )
     *
     * @OA\Property(
     *     property="type",
     *     type="string",
     *     description="Type",
     *     title="Type",
     * )
     * @OA\Property(
     *     property="notifiable_type",
     *     type="string",
     *     description="Notifiable type",
     *     title="Notifiable type",
     * )
     * @OA\Property(
     *     property="notifiable_id",
     *     type="integer",
     *     format="int64",
     *     description="Notifiable ID",
     *     title="Notifiable ID",
     * )
     *
     * @OA\Property(
     *     property="data",
     *     type="string",
     *     description="Data",
     *     title="Data",
     * )
     *
     * @OA\Property(
     *     property="read_at",
     *     type="string",
     *     format="date-time",
     *     description="When was readed",
     *     title="Read at",
     * )
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

    protected $table = "notifications";
    protected $perPage = 5;

    public function to_user()
    {
        return $this->belongsTo(User::class, 'notifiable_id');
    }

    public function notification()
    {
        return $this->morphTo();
    }
}
