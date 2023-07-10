<?php

namespace App\Exports;

use App\Models\AccessoryVehicle;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccessoryVehicleExport implements FromCollection, WithMapping, WithHeadings
{
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return AccessoryVehicle::filter($this->request->all())->get();
    }

    public function map($deliveryVehicle): array
    {
        return [
            $this->fixTime($deliveryVehicle->created_at),
            $deliveryVehicle->accessory->name ?? null,
            $deliveryVehicle->vehicle->typeModelOrder->name ?? null,
            $deliveryVehicle->vehicle->vin ?? null,
            $deliveryVehicle->vehicle->plate ?? null,
            $deliveryVehicle->vehicle->vehicleModel->brand->name ?? null,
            $deliveryVehicle->vehicle->vehicleModel->name ?? null,
        ];
    }

    public function fixTime($date) {
        if ($date) {
            return (new  Carbon($date))->addHours(2)->format('d/m/Y H:m:i');
        }
        return $date;
    }

    public function headings(): array
    {
        return [
            'Registrado',
            'Accesorio',
            'Negocio',
            'Chasis',
            'Matrícula',
            'Marca',
            'Modelo'
        ];
    }
}
