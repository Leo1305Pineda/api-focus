<?php

namespace App\Console\Commands;

use App\Exports\DeliveryVehiclesExport;
use App\Exports\EntriesVehiclesExport;
use App\Exports\PendingTaskExport;
use App\Exports\StockVehiclesExport;

use App\Models\PeopleForReport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::debug(date('H') . 'HOUR');
        $reports = PeopleForReport::with(['typeReport', 'campa'])
            ->whereNotNull('email')
            ->whereHas('typeReport', function ($query) {
                $query->whereRaw('JSON_CONTAINS(`schedule`, JSON_ARRAY("' . date('H') . ':00"))');
            })
            ->groupBy('type_report_id', 'campa_id', 'email')
            ->get();
        ini_set("memory_limit", "-1");
        $date = microtime(true);
        $array = explode('.', $date);
        $env = env('APP_ENV');
        $content = collect([]);
        foreach ($reports as $key => $report) {
            if ($content->where('email', $report->email)->count() === 0) {
                $content->push((object) [
                    'email' => $report->email,
                    'data' => collect([])
                ]);
            }
        }
        foreach ($reports as $key => $report) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-záéíóúÁÉÍÓÚñÑ0-9-]+/', '-', strtolower($report->typeReport->name . '-' . $report->campa->name))));
            $file_name = env('FOLDER_REPORT') . $slug . '-' . date('d-m-Y') . '-' . $array[0] . '.xlsx';
            $disk =  $env == 'production' ? 's3' : 'public';
            switch ($report->typeReport->model_class) {
                case PendingTaskExport::class:
                    $request = collect([
                        'campasIds' => [$report->campa_id]
                    ]);
                    Excel::store(new PendingTaskExport($request), $file_name, $disk);
                    break;
                case StockVehiclesExport::class:
                    $request = collect([
                        'statesNotIds' => [4, 5, 10],
                        'defleetingAndDelivery' => 1,
                        'campaIds' => [$report->campa_id]
                    ]);
                    Excel::store(new StockVehiclesExport($request), $file_name, $disk);
                    break;
                case EntriesVehiclesExport::class:
                    $request = collect([
                        'whereHasVehicle' => 0,
                        'subStatesNotIds' => [10],
                        'campaIds' => [$report->campa_id],
                        'createdAtFrom' => Carbon::now('Europe/Madrid')->startOfDay()->timezone('UTC')->format('Y-m-d H:i:s'),
                        'createdAtTo' => Carbon::now('Europe/Madrid')->endOfDay()->timezone('UTC')->format('Y-m-d H:i:s')
                    ]);
                    Excel::store(new EntriesVehiclesExport($request), $file_name, $disk);
                    break;
                case DeliveryVehiclesExport::class:
                    $request = collect([
                        'pendindTaskNull' => 0,
                        'vehicleDeleted' => 0,
                        'campaIds' => [$report->campa_id],
                        'createdAtFrom' => Carbon::now('Europe/Madrid')->startOfDay()->timezone('UTC')->format('Y-m-d H:i:s'),
                        'createdAtTo' => Carbon::now('Europe/Madrid')->endOfDay()->timezone('UTC')->format('Y-m-d H:i:s')
                    ]);
                    Excel::store(new DeliveryVehiclesExport($request), $file_name, $disk);
                    break;
                default:
                    # code...
                    break;
            }
            $url=Storage::disk($disk)->url($file_name);
            $this->pushData($content, $report, $url);

        }
        $content->map(function ($item) {
            $data = [
                'title' => 'Reporte',
                'sub_title' => '',
                'body' => '<ul>'.$item->data->map(function ($value) {
                    return '<li>'.$value->type_report_name . ' de la campa ' . $value->campa_name . ':<br/><a href="' . $value->url .'">' . $value->url .'</a></li>';
                })->join('<br/>').'</ul>'
            ];
            Log::debug($data);
            $this->info(print_r($data, true));
            Mail::send('report-generic', $data, function ($message) use ($item) {
                $message->to(env('APP_ENV') == 'production' ? $item->email : env('MAIL_FROM_ADDRESS', 'focus@grupomobius.com'), 'Reporte ALD');
                $message->subject(env('APP_ENV') == 'production' ? 'Reporte ALD': 'Reporte ALD (TESTING)');
                $message->from('no-reply.focus@grupomobius.com', 'Focus');
            });
        });
    }

    function pushData(&$content, $report, $url)
    {
        $arr = $content->where('email', $report->email)->first();
        $arr->data->push((object) [
            'type_report_name' => $report->typeReport->name,
            'url' => $url,
            'campa_name' => $report->campa->name
        ]);
    }
}
