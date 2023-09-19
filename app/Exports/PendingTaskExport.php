<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\PendingTask;
use App\Models\Task;
use App\Models\StatePendingTask;
use DateTime;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PendingTaskExport implements FromCollection, WithMapping, WithHeadings
{

    protected $request;
    
    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $sql = <<<SQL
        (
            SELECT MAX(pt.id)
            FROM pending_tasks as pt
            WHERE pt.vehicle_id = pending_tasks.vehicle_id AND pt.task_id = 38
            AND state_pending_task_id = 3 
            AND pt.reception_id = pending_tasks.reception_id
        ) as last_delivered_pending_task_id
    SQL;
        return PendingTask::select(['datetime_pending', 'datetime_start', 'datetime_finish', 'observations', 'vehicle_id', 'task_id', 'reception_id', 'state_pending_task_id'])
            ->selectRaw(DB::raw($sql))
            ->with(array(
                'vehicle' => function ($query) {
                    $query->select(['id', 'plate', 'kms', 'has_environment_label', 'company_id', 'vehicle_model_id'])
                        ->selectRaw(DB::raw('(select c.name from colors c where c.id = vehicles.color_id) as color_name'))
                        ->selectRaw(DB::raw('(select c.name from categories c where c.id = vehicles.category_id) as category_name'))
                        ->selectRaw(DB::raw('(SELECT GROUP_CONCAT((SELECT a.name from accessories a where a.id = av.accessory_id)) as accesory_name from accessory_vehicle av where av.vehicle_id = vehicles.id group By av.vehicle_id) as accesory_name'))
                        ->with([
                            'vehicleModel' => function ($q) {
                                $q->select('id', 'name', 'brand_id')
                                    ->selectRaw(DB::raw('(select b.name from brands b where b.id = vehicle_models.brand_id) as brand_name'));
                            }
                        ])
                        ->where('company_id', Company::ALD);
                },
                'task' => function ($query) {
                    $query->select(['id', 'name', 'sub_state_id'])
                        ->with(array(
                            'subState' => function ($q) {
                                $q->select('id', 'name', 'state_id')->with(['state' => function ($q) {
                                    $q->select('id', 'name');
                                }]);
                            }
                        ));
                },
                'reception' => function ($q) {
                    $q->select('id', 'created_at', 'type_model_order_id', 'campa_id')
                        ->with([
                            'typeModelOrder' => function ($query) {
                                $query->select('id', 'name');
                            },
                            'campa' => function ($query) {
                                $query->select('id', 'name');
                            },
                            'lastQuestionnaire' => function ($query) {
                                $query->select('id', 'questionnaires.reception_id', 'datetime_approved');
                            },
                        ]);
                },
                'userStart' => function ($query) {
                    $query->select('id', 'name');
                },
                'userEnd' => function ($query) {
                    $query->select('id', 'name');
                },
                'statePendingTask' => function ($query) {
                    $query->select('id', 'name');
                },
                'lastDeliveredPendingTask' => function ($query) {
                    $query->select('id', 'datetime_finish');
                },
            ))

            ->whereNotNull('reception_id')
            ->where('approved', true)
            ->whereIn('state_pending_task_id', [StatePendingTask::PENDING, StatePendingTask::IN_PROGRESS, StatePendingTask::FINISHED])
            ->whereRaw('vehicle_id NOT IN(SELECT id FROM vehicles WHERE deleted_at is not null)')
            ->filter($this->request->all())
            ->get();
    }
    public function map($data): array
    {
        $array = [];
        $line = [];
        if ($data->vehicle) {
            $time_pending = $this->diffDateTimes($data->datetime_pending, $data->state_pending_task_id === 1 ? date('Y-m-d H:i:s') : $data->datetime_finish);
            if ($data->task_id == Task::VALIDATE_CHECKLIST) {
                $time_pending = $this->diffDateTimes($data->datetime_pending, $data->lastDeliveredPendingTask ? $data->lastDeliveredPendingTask->datetime_finish : date('Y-m-d H:i:s'));
            }
            $line = [
                $data->vehicle->plate,
                $this->dateFormat($data->reception?->created_at),
                $this->dateFormat($data->reception?->lastQuestionnaire?->datetime_approved),
                $data->vehicle->kms,
                $data->vehicle->vehicleModel->brand->name ?? null,
                $data->vehicle->vehicleModel->name ?? null,
                $data->vehicle->color_name ?? null,
                $data->task->subState->state->name ?? null,
                $data->task->subState->name ?? null,
                $data->observations,
                $data->vehicle->accesory_name ?? null,
                $data->vehicle->has_environment_label == true ? 'Si' : 'No',
                $data->reception &&  $data->reception->campa ? $data->reception->campa->name : null,
                $data->vehicle->category_name ?? null,
                $data->task->name ?? null,
                $data->statePendingTask->name,
                $this->dateFormat($data->datetime_pending),
                $this->dateFormat($data->datetime_start),
                $this->dateFormat($data->datetime_finish),
                $time_pending === 0 ? '0' : $time_pending,
                $data->user_start?->name ?? null,
                $data->user_end?->name ?? null,
                $data->total_paused,
                $data->reception?->typeModelOrder?->name,
                $this->dateFormat($data->vehicle->lastDeliveryVehicle?->created_at),
                $data->estimatedDates?->pluck('estimated_date')->implode(',') ?? null,
                $this->dateFormat($data->lastDeliveredPendingTask->datetime_finish ?? date('Y-m-d H:i:s'))
            ];
            array_push($array, $line);
        }
        return $array;
    }

    public function dateFormat($date)
    {
        if ($date) {
            return date('d/m/Y H:i', strtotime($date));
        }
        return '-';
    }

    private function diffDateTimes($datetime, $datetime2)
    {
        $minutes = 0;
        if (!is_null($datetime) && !is_null($datetime2)) {
            $datetime1 = new DateTime($datetime);
            $diference = date_diff($datetime1, new DateTime($datetime2));
            $minutes = $diference->days * 24 * 60;
            $minutes += $diference->h * 60;
            $minutes += $diference->i;
        }
        return $minutes > 0 ? round($minutes / 60, 4) : 0;
    }

    public function headings(): array
    {
        return [
            'Matrícula',
            'Fecha de recepción',
            'Fecha de aprobación',
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
            'Fecha pendiente tarea',
            'Fecha inicio tarea',
            'Fecha fin tarea',
            'Tiempo pendiente',
            'Operario Inicio la Tarea',
            'Operario Finalizo la Tarea',
            'Tiempo (horas)',
            'Negocio',
            'última salida',
            'Fecha estimada',
            'last_delivered_pending_task'
        ];
    }
}
