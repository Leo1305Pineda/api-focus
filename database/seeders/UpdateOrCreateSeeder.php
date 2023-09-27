<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\State;
use App\Models\SubState;
use App\Models\SubStateTypeUserApp;
use App\Models\Task;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UpdateOrCreateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Role::updateOrCreate([
            "id" => 12
        ], [
            "description" => "Técnico Ald"
        ]);

        SubState::updateOrCreate([
            "id" => 31
        ], [
            "state_id" => 2,
            "name" => "Revisión Incidencia Entrega",
            "display_name" => "Revisión Incidencia Entrega"
        ]);

        Task::updateOrCreate([
            "id" => 72
        ], [
            "company_id" => 1,
            "sub_state_id" => 31,
            "type_task_id" => 2,
            "need_authorization" => 0,
            "name" => "Revisión Incidencia Entrega",
            "duration" => 0
        ]);

        State::updateOrCreate([
            'id' => 19
        ], [
            "name" => "Tarea automatica",
            "company_id" => 1,
            "type" => 1
        ]);

        SubState::updateOrCreate([
            "id" => 32
        ], [
            "state_id" => 19,
            "name" => "Tarea automatica",
            "display_name" => "Tarea automatica"
        ]);

        Task::updateOrCreate([
            "id" => 73
        ], [
            "company_id" => 1,
            "sub_state_id" => 32,
            "type_task_id" => 2,
            "need_authorization" => 0,
            "name" => "Cambio de canal a VO",
            "duration" => 0
        ]);

        $model = SubStateTypeUserApp::where('sub_state_id', 31)->where('type_user_app_id', 2)->first();
        if (!$model) {
            SubStateTypeUserApp::create(["sub_state_id" => 31, "type_user_app_id" => 2]);
        }

        $model = SubStateTypeUserApp::where('sub_state_id', 32)->where('type_user_app_id', 1)->first();
        if (!$model) {
            SubStateTypeUserApp::create(["sub_state_id" => 32, "type_user_app_id" => 1]);
        }


        $vehicles = Vehicle::all();
        foreach ($vehicles as $key => $vehicle) {
            if ($vehicle->lastReception) {
                $vehicle->lastReception->update([
                    'remote_id' => $vehicle->remote_id,
                    'company_id' => $vehicle->company_id,
                    "campa_id" => $vehicle->campa_id,
                    'category_id' => $vehicle->category_id,
                    'sub_state_id' => $vehicle->sub_state_id,
                    'plate' => $vehicle->plate,
                    'vehicle_model_id' => $vehicle->vehicle_model_id,
                    'type_model_order_id' => $vehicle->type_model_order_id,
                    'kms' => $vehicle->kms,
                    'next_itv' => $vehicle->next_itv,
                    'has_environment_label' => $vehicle->has_environment_label,
                    'observations' => $vehicle->observations,
                    'priority' => $vehicle->priority,
                    'version' => $vehicle->version,
                    'vin' => $vehicle->vin,
                    'first_plate' => $vehicle->first_plate,
                    'latitude' => $vehicle->latitude,
                    'longitude' => $vehicle->longitude,
                    'trade_state_id' => $vehicle->trade_state_id,
                    'documentation' => $vehicle->documentation,
                    'ready_to_delivery' => $vehicle->ready_to_delivery,
                    'deleted_user_id' => $vehicle->deleted_user_id,
                    'seater' => $vehicle->seater
                ]);
            }
        }
    }
}
