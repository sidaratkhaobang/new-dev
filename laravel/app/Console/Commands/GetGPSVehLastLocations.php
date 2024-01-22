<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Classes\GPSService;

class GetGPSVehLastLocations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tls:get_gps_veh_last_locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'GetGPSVehLastLocations';

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
        return 0;
    }
}
