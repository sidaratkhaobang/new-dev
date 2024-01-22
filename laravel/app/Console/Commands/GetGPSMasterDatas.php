<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\GPSService;
use App\Jobs\GPSUpdateVID;
use Illuminate\Support\Facades\Log;

class GetGPSMasterDatas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tls:get_gps_master_datas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GetGPSMasterDatas';

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
     * @return int
     */
    public function handle()
    {
        $gpsService = new GPSService();
        $res = $gpsService->getMasterDatas();
        if ($res['successful'] && (strcmp($res['status'], '200') == 0)) {
            $data = $res['data'];
            if (is_array($data)) {
                foreach ($data as $d) {
                    $vehicle_id = $d['Vehicle_ID'];
                    $registration = $d['Registration'];
                    $chassis_no = $d['Chassis_No'];

                    if ((!empty($vehicle_id)) && (!empty($chassis_no))) {
                        GPSUpdateVID::dispatch($chassis_no, $vehicle_id);
                    }
                }
            }
        } else {
            Log::channel('sentry')->alert('GetGPSMasterDatas failed', [
                'successful' => $res['successful'],
                'status' => $res['status'],
            ]);
        }
        return 0;
    }
}
