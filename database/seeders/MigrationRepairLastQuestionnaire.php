<?php

namespace Database\Seeders;

use App\Models\Campa;
use App\Models\PendingTask;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigrationRepairLastQuestionnaire extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', '2048M');
        $campa_migracion = $this->command->ask('Campa de Migracion');
        if ($campa = Campa::find($campa_migracion)){
            $this->command->info("Iniciando reparación en migración de la campa: {$campa->name}");
        } else {
            $this->command->error("Campa de Migracion {$campa_migracion} no existe!");
            exit();
        }
        //DB::listen(function ($query) {dump("[{$query->time}ms] {$query->sql}"); if ($query->bindings) { dump($query->bindings);}});

        $vehicles = Vehicle::where([
            'campa_id' => $campa_migracion
        ])
        ->whereHas('lastReception', function($query){
            $query->whereHas('lastQuestionnaire.questionAnswers', function($q){
                $q->whereNotNull('task_id')
                ->where('response', '<>',0);
            })
            ->whereHas('allPendingTasks', function($q){
                $q->where('created_from_checklist', 1)
                ->whereNull('question_answer_id');
            });
        })->get();

        $this->command->info("Cantidad de Vehiculos: {$vehicles->count()}");
        $updated_count = 0;
        try {
            $progress = $this->command->getOutput()->createProgressBar($vehicles->count());
            DB::beginTransaction();
            $vehicle_plate = null;
            foreach ($vehicles as $vehicle) {
                $vehicle_plate = $vehicle->plate;
                $progress->advance();
                $questionAnswers = $vehicle->lastReception->lastQuestionnaire->questionAnswers;
                $paso = false;
                foreach ($questionAnswers as $questionAnswer) {
                    $exists = PendingTask::where([
                        'reception_id'=>$vehicle->lastReception->id,
                        'vehicle_id'=>$vehicle->id,
                        'task_id'=>$questionAnswer->task_id,
                        'created_from_checklist'=>1
                    ])
                    ->whereNull('question_answer_id')->exists();
                    if (!!$questionAnswer->response && !!$questionAnswer->task_id && $exists){
                        PendingTask::where([
                            'reception_id'=>$vehicle->lastReception->id,
                            'vehicle_id'=>$vehicle->id,
                            'task_id'=>$questionAnswer->task_id,
                            'created_from_checklist'=>1
                        ])
                        ->whereNull('question_answer_id')
                        ->update([
                            'question_answer_id' => $questionAnswer->id
                        ]);
                        $paso = true;
                    }
                }
                if ($paso)
                $updated_count++;
            }
            $progress->finish();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->warn("Ha ocurrido un error en la corrección del vehiculo placa \"$vehicle_plate\" y ha sido detenida!. Se actualizaron vehiculos: {$updated_count}");
            $this->command->error($e->getMessage());
            dump($e);
        }
        $this->command->info(" Vehiculos actualizadios: {$updated_count}");
    }
}
