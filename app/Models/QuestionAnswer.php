<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Question Answer
 *
 * @package Focus API
 *
 *
 * @OA\Schema(
 *     title="Question Answer model",
 *     description="Question Answer model",
 * )
 */

class QuestionAnswer extends Model
{

    /**
     * @OA\Schema(
     *      schema="QuestionAnswerWithQuestionAndTask",
     *      allOf = {
     *          @OA\Schema(ref="#/components/schemas/Question"),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="question",
     *                  type="object",
     *                  ref="#/components/schemas/Question"
     *              ),
     *          ),
     *          @OA\Schema(
     *              @OA\Property(
     *                  property="task",
     *                  type="object",
     *                  ref="#/components/schemas/Task"
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
     *     property="task_id",
     *     type="integer",
     *     format="int64",
     *     description="Task ID",
     *     title="Task ID",
     * )
     *
     * @OA\Property(
     *     property="questionnaire_id",
     *     type="integer",
     *     format="int64",
     *     description="Questionnaire ID",
     *     title="Questionnaire ID",
     * )
     *
     * @OA\Property(
     *     property="question_id",
     *     type="integer",
     *     format="int64",
     *     description="Question ID",
     *     title="Question ID",
     * )
     *
     * @OA\Property(
     *     property="response",
     *     type="boolean",
     *     description="Response",
     *     title="Response",
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
     *     property="description_response",
     *     type="string",
     *     description="Description response",
     *     title="Description response",
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

    use HasFactory, LogsActivity;

    protected $fillable = [
        "task_id",
        "questionnaire_id",
        "question_id",
        "response",
        "description",
        "description_response",
        "url",
    ];

    public function questionnaire(){
        return $this->belongsTo(Questionnaire::class);
    }

    public function question(){
        return $this->belongsTo(Question::class);
    }

    public function task(){
        return $this->belongsTo(Task::class);
    }

    public function pendingTask(){
        return $this->hasOne(PendingTask::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*'])
        ->logOnlyDirty();
    }

}
