<?php

namespace App\Exports;

use App\Models\Vehicle;
use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;


class KpiCheckListExport implements FromArray, WithHeadings
{
    protected $request;
    protected $header = ['Invarat'];
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function array(): array
    {

        $data = Vehicle::whereHas('lastUnapprovedReception')
            ->whereHas('campa', function (Builder $builder) {
                return $builder->where('company_id', Company::ALD);
            })
            ->select(
                DB::raw('count(*) vehiculos'),
                DB::raw('sum((select count(*) from pending_tasks where vehicle_id = vehicles.id and task_id = 2 and approved = 1 and (state_pending_task_id in (1, 2) or (state_pending_task_id is null and comment_state is null)) )) chapa'),
                DB::raw('sum((select count(*) from pending_tasks where vehicle_id = vehicles.id and task_id = 3 and approved = 1 and (state_pending_task_id in (1, 2) or (state_pending_task_id is null and comment_state is null)) )) mecanica')
            )
            ->get();

        $value[] = ['Checklist pendientes', 'Checklist pendientes (vehículos en estado pendiente check) totales.', strval($data[0]['vehiculos'])];
        $value[] = ['Mecánica', 'Número de tareas de mecánica de vehículos pendiente check', strval($data[0]['mecanica'])];
        $value[] = ['Chapa', 'Número de tareas de chapa de vehiculos pendiente check.', strval($data[0]['chapa'])];

        return $value;
    }

    public function obtenerPorcentaje($cantidad, $total)
    {
        $porcentaje = ((float)$cantidad * 100) / $total; // Regla de tres
        $porcentaje = round($porcentaje, 2);  // Quitar los decimales
        return $porcentaje;
    }

    public function headings(): array
    {
        return $this->header;
    }
}
