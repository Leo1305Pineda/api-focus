<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\State;
use App\Models\StatePendingTask;
use App\Models\SubState;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PendingTaskExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Vehicle::with(['pendingTasks' => function($query){
            return $query->where('approved', true)
                ->whereIn('state_pending_task_id', [StatePendingTask::IN_PROGRESS, StatePendingTask::FINISHED]);
        }])
        ->where('company_id', Company::ALD)
        ->get();
    }

    public function map($vehicle): array
    {
        $array = [];
        if(count($vehicle->pendingTasks) > 0) {
            foreach($vehicle->pendingTasks as $pendingTask){
                $line = [
                    $vehicle->plate,
                    $pendingTask->reception ? date('d/m/Y', strtotime($pendingTask->reception->created_at)) : null,
                    $vehicle->kms,
                    $vehicle->vehicleModel->brand->name ?? null,
                    $vehicle->vehicleModel->name ?? null,
                    $vehicle->color->name ?? null,
                    $pendingTask->task->subState->state->name ?? null,
                    $pendingTask->task->subState->name ?? null,
                    $pendingTask->observations,
                    $vehicle->accessoriesTypeAccessory->pluck('name')->implode(', ') ?? null,
                    $vehicle->has_environment_label == true ? 'Si' : 'No',
                    $pendingTask->campa->name ?? null,
                    $vehicle->category->name ?? null,
                    $pendingTask->task->name ?? null,
                    $pendingTask->statePendingTask->name ?? null,
                    $pendingTask->datetime_start ? date('d/m/Y H:i:s', strtotime($pendingTask->datetime_start)) : null,
                    $pendingTask->datetime_finish ? date('d/m/Y H:i:s', strtotime($pendingTask->datetime_finish)) : null,
                    round(($pendingTask->total_paused / 60), 2),
                    $vehicle->typeModelOrder->name ?? null,
                    $vehicle->lastDeliveryVehicle?->created_at ? date('d/m/Y H:i:s', strtotime($vehicle->lastDeliveryVehicle->created_at)) : null,
                    $pendingTask->estimatedDates?->pluck('estimated_date')->implode(',') ?? null,
                ];
                array_push($array, $line);
            }
        } else {
            $line = [
                $vehicle->plate,
                $vehicle->lastReception ? date('d/m/Y', strtotime($vehicle->lastReception->created_at)) : null,
                $vehicle->kms,
                $vehicle->vehicleModel->brand->name ?? null,
                $vehicle->vehicleModel->name ?? null,
                $vehicle->color->name ?? null,
                $vehicle->subState->state->name ?? null,
                $vehicle->subState->name ?? null,
                null,
                $vehicle->accessoriesTypeAccessory->pluck('name')->implode(', ') ?? null,
                $vehicle->has_environment_label == true ? 'Si' : 'No',
                $vehicle->campa->name ?? null,
                $vehicle->category->name ?? null,
                $vehicle->task->name ?? null,
                null,
                null,
                null,
                null,
                $vehicle->typeModelOrder->name ?? null,
                $vehicle->lastDeliveryVehicle?->created_at ? date('d/m/Y H:i:s', strtotime($vehicle->lastDeliveryVehicle->created_at)) : null,
                null,
            ];
            array_push($array, $line);
        }

        return $array;
    }

    public function headings(): array
    {
        return [
            'Matrícula',
            'Fecha de recepción',
            'Kilómetros',
            'Marca',
            'Modelo',
            'Color',
            'Estado',
            'Sub-estado',
            'Observaciones',
            'Accesorios',
            'Etiqueta M.A.',
            'Campa',
            'Categoría',
            'Tarea',
            'Estado tarea',
            'Fecha inicio tarea',
            'Fecha fin tarea',
            'Tiempo (horas)',
            'Negocio',
            'última salida',
            'Fecha estimada'
        ];
    }
}