<?php

namespace Database\Seeders;

use App\Models\PendingTask;
use App\Models\Questionnaire;
use App\Models\Role;
use App\Models\StatePendingTask;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class RepairDateValidateCkeckList extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('role_id', Role::CONTROL)
            ->first();
        Log::debug('ss');
        $pending_tasks = PendingTask::where('state_pending_task_id', StatePendingTask::FINISHED)
            ->where('task_id', Task::VALIDATE_CHECKLIST)
            ->get();

        foreach ($pending_tasks as $key => $pending_task) {
            $pending_task->user_id = $user->id;
            Log::debug($pending_task->reception_id);
            $questionnaire = Questionnaire::where('reception_id', $pending_task->reception_id)->first();
            if (!is_null($questionnaire)) {
                $pending_task->datetime_start = $questionnaire->created_at;
                if (is_null($pending_task->user_start_id)) {
                    $pending_task->user_start_id = $pending_task->user_id;
                }
                if ($questionnaire->datetime_appreved) {
                    $pending_task->datetime_finish = $questionnaire->datetime_appreved;
                    $pending_task->user_end_id = $user->id;
                }
            }
            $pending_task->save();
        }

        Log::debug(count($pending_tasks));
    }
}
