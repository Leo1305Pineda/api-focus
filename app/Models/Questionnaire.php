<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Questionnaire
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Questionnaire model",
 *     description="Questionnaire model",
 * )
 */

class Questionnaire extends Model
{

    /**
     * @OA\Schema(
     *      schema="LastQuestionnaire",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Questionnaire"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="question_answers",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/QuestionAnswerWithQuestionAndTask")
     *              ),
     *          ),
     *      },
     * )
     * @OA\Schema(
     *      schema="QuestionnairePaginate",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Paginate"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/Questionnaire"),
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
     *
     * @OA\Property(
     *     property="datetime_approved",
     *     type="date",
     *     format="int64",
     *     description="Datetime Approved",
     *     title="Datetime Approved",
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
     *     property="vehicle_id",
     *     type="integer",
     *     format="int64",
     *     description="Vehicle ID",
     *     title="Vehicle ID",
     * )
     *
     * @OA\Property(
     *     property="file",
     *     type="string",
     *     description="File",
     *     title="File",
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

    use HasFactory, Filterable, SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'datetime_approved',
        'file',
        'user_id_updated'
    ];

    public function vehicle(){
        return $this->belongsTo(Vehicle::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function userUpdated(){
        return $this->belongsTo(User::class, 'user_id_updated');
    }

    public function reception(){
        return $this->belongsTo(Reception::class);
    }

    public function questionAnswers(){
        return $this->hasMany(QuestionAnswer::class);
    }

    public function groupTask(){
        return $this->hasOne(GroupTask::class);
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }
}
