<?php

namespace App\Mail;

use App\Exports\DeliveryVehiclesExport;
use App\Exports\EntriesVehiclesExport;
use App\Exports\StockVehiclesExport;
use App\Models\Campa;
use App\Models\PeopleForReport;
use App\Models\Role;
use App\Models\TypeReport;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class StockVehicles extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $campas = Campa::where('active', true)->get();
        foreach ($campas as $campa) {
            $peopleForReport = PeopleForReport::with(['user'])
                ->where('type_report_id', TypeReport::STOCK)
                ->where('campa_id', $campa->id)
                ->get();
            $data = [
                'title' => 'Stock de vehículos',
                'sub_title' => 'Adjunto se encuentra un documento con el stock de los vehículos al día ' . date('d/m/Y')
            ];
            $arr = collect([
                'statesNotIds' => [4, 5, 10],
                'defleetingAndDelivery' => 1,
                'campaIds' => [$campa->id],
              //  'lastReceptionCreatedAtFrom' => Carbon::now('Europe/Madrid')->startOfDay()->timezone('UTC')->format('Y-m-d H:i:s'),
              //  'lastReceptionCreatedAtTo' => Carbon::now('Europe/Madrid')->endOfDay()->timezone('UTC')->format('Y-m-d H:i:s')
            ]);
            $file = Excel::download(new StockVehiclesExport(collect($arr)), 'entradas.xlsx')->getFile();
            rename($file->getRealPath(), $file->getPath() . '/' . 'stock-vehículos.xlsx');
            $fileRename1 = $file->getPath() . '/stock-vehículos.xlsx';

            $file = Excel::download(new EntriesVehiclesExport($campa->id), 'entradas.xlsx')->getFile();
            rename($file->getRealPath(), $file->getPath() . '/' . 'entries.xlsx');
            $fileRename2 = $file->getPath() . '/entries.xlsx';

            $file = Excel::download(new DeliveryVehiclesExport(collect([
                'pendindTaskNull' => 0,
                'vehicleDeleted' => 0,
                'campaIds' => [$campa->id],
                'createdAtFrom' => Carbon::now('Europe/Madrid')->startOfDay()->timezone('UTC')->format('Y-m-d H:i:s'),
                'createdAtTo' => Carbon::now('Europe/Madrid')->endOfDay()->timezone('UTC')->format('Y-m-d H:i:s')
            ])), 'entradas.xlsx')->getFile();

            rename($file->getRealPath(), $file->getPath() . '/' . 'deliveries.xlsx');
            $fileRename3 = $file->getPath() . '/deliveries.xlsx';

            $attachments = [
                $fileRename1 => ['as' => 'Stock-vehículos.xlsx'],
                $fileRename2 => ['as' => 'Entradas.xlsx'],
                $fileRename3 => ['as' => 'Salidas.xlsx']
            ];

            foreach ($peopleForReport as $user) {
                if ($user['user']['role_id'] != Role::GLOBAL_MANAGER) {
                    Mail::send('report-generic', $data, function ($message) use ($user, $attachments) {
                        $env = env('APP_ENV');
                        $message->to($env == 'production' ? $user['user']['email'] : env('MAIL_FROM_ADDRESS', 'focus@grupomobius.com'), $user['user']['name']);
                        $message->subject('Stock de vehículos');
                        $message->from('no-reply.focus@grupomobius.com', 'Focus');
                        foreach ($attachments as $filePath => $fileParameters) {
                            $message->attach($filePath, $fileParameters);
                        }
                    });
                }
            }
            \unlink($file->getPath() . '/stock-vehículos.xlsx');
            \unlink($file->getPath() . '/entries.xlsx');
            \unlink($file->getPath() . '/deliveries.xlsx');
        }

        $peopleForReport = PeopleForReport::with(['user'])
            ->where('type_report_id', TypeReport::STOCK)
            ->whereHas('user', function (Builder $builder) {
                return $builder->where('role_id', Role::GLOBAL_MANAGER);
            })
            ->get();

        $arr = collect([
                'statesNotIds' => [4, 5, 10],
                'defleetingAndDelivery' => 1,
                'campaNull' => 0
              //  'lastReceptionCreatedAtFrom' => Carbon::now('Europe/Madrid')->startOfDay()->timezone('UTC')->format('Y-m-d H:i:s'),
              //  'lastReceptionCreatedAtTo' => Carbon::now('Europe/Madrid')->endOfDay()->timezone('UTC')->format('Y-m-d H:i:s')
        ]);
        $file = Excel::download(new StockVehiclesExport($arr), 'entradas.xlsx')->getFile();
        rename($file->getRealPath(), $file->getPath() . '/' . 'stock-vehículos.xlsx');
        $fileRename1 = $file->getPath() . '/stock-vehículos.xlsx';

        $file = Excel::download(new EntriesVehiclesExport(null), 'entradas.xlsx')->getFile();
        rename($file->getRealPath(), $file->getPath() . '/' . 'entries.xlsx');
        $fileRename2 = $file->getPath() . '/entries.xlsx';


        $file = Excel::download(new DeliveryVehiclesExport(collect([
            'pendindTaskNull' => 0,
            'vehicleDeleted' => 0,
            'createdAtFrom' => Carbon::now('Europe/Madrid')->startOfDay()->timezone('UTC')->format('Y-m-d H:i:s'),
            'createdAtTo' => Carbon::now('Europe/Madrid')->endOfDay()->timezone('UTC')->format('Y-m-d H:i:s')
        ])), 'entradas.xlsx')->getFile();

        rename($file->getRealPath(), $file->getPath() . '/' . 'deliveries.xlsx');
        $fileRename3 = $file->getPath() . '/deliveries.xlsx';

        $attachments = [
            $fileRename1 => ['as' => 'Stock-vehículos.xlsx'],
            $fileRename2 => ['as' => 'Entradas.xlsx'],
            $fileRename3 => ['as' => 'Salidas.xlsx']
        ];

        foreach ($peopleForReport as $user) {
            Mail::send('report-generic', $data, function ($message) use ($user, $attachments) {
                $env = env('APP_ENV');
                $message->to($env == 'production' ? $user['user']['email'] : env('MAIL_FROM_ADDRESS', 'focus@grupomobius.com'), $user['user']['name']);
                $message->subject('Stock de vehículos');
                $message->from('no-reply.focus@grupomobius.com', 'Focus');
                foreach ($attachments as $filePath => $fileParameters) {
                    $message->attach($filePath, $fileParameters);
                }
            });
        }
        \unlink($file->getPath() . '/stock-vehículos.xlsx');
        \unlink($file->getPath() . '/entries.xlsx');
        \unlink($file->getPath() . '/deliveries.xlsx');
    }
}
