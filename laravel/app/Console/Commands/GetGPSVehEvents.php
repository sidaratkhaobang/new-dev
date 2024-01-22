<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\GPSService;
use App\Jobs\GPSUpdateEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GetGPSVehEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tls:get_gps_veh_events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GetGPSVehEvents';

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
        $start_date_time = date('Y-m-d H:i:s', strtotime('-1 hours'));
        $end_date_time = date('Y-m-d H:i:s');

        $gpsService = new GPSService();
        $res = $gpsService->getVehEvents($start_date_time, $end_date_time);
        if ($res['successful'] && (strcmp($res['status'], '200') == 0)) {
            $data = $res['data'];
            if (is_array($data)) {
                foreach ($data as $d) {
                    $vehicle_id = $d['Vehicle_ID'];
                    $event_th = $d['Event_TH'];
                    $event_en = $d['Event_EN'];
                    $location_th = $d['Location_TH'];
                    $timestamp = $d['Timestamp'];
                    $timestamp = date('Y-m-d H:i:s', strtotime($timestamp));

                    $event_th_truncated = Str::limit($event_th, 95);
                    $event_en_truncated = Str::limit($event_en, 95);
                    $location_th_truncated = Str::limit($location_th, 495);

                    if ((!empty($event_th)) && (!empty($location_th))) {
                        GPSUpdateEvent::dispatch($vehicle_id, $event_th_truncated, $event_en_truncated, $location_th_truncated, $timestamp);
                    }
                }
            }
        } else {
            Log::channel('sentry')->alert('GetGPSVehEvents failed', [
                'successful' => $res['successful'],
                'status' => $res['status'],
            ]);
        }
        return 0;
    }
}
