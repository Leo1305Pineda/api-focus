<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\State;
use App\Models\SubState;
use App\Models\SubStateTypeUserApp;
use App\Models\Task;
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

        SubStateTypeUserApp::updateOrCreate([ "sub_state_id" => 31 ], [ "type_user_app_id" => 2]);
        SubStateTypeUserApp::updateOrCreate([ "sub_state_id" => 32 ], [ "type_user_app_id" => 1]);
    }
}
