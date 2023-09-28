<?php

namespace App\Console\Commands;

use App\Exports\PendingTaskExport as ExportsPendingTaskExport;
use App\Models\Campa;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class PendingTaskExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pendingtask:export';

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
        ini_set("memory_limit", "-1");
        $date = microtime(true);
        $array = explode('.', $date);
        $campas = Campa::where('active', 1)->get();
        $env = env('APP_ENV');
        foreach ($campas as $key => $campa) {
            $request = request();
            $request->merge([
                'campaIds' => [$campa->id]
            ]);
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($campa->name))));
            $file_name = 'tareas-realizadas-' . $slug . '-' . date('d-m-Y') . '-' . $array[0] . '.xlsx';
          //  $url = env('AWS_URL') . '/' . $file_name;
            Excel::store(new ExportsPendingTaskExport($request), $file_name, $env == 'production' ? 's3' : 'public');
        }
    }
}
