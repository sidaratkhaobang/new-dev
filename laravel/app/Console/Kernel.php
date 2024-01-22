<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        //default
//        Command For Run Queue CMI And VMI Check Status
//         php artisan queue:work database --queue=EffectiveCmi,EffectiveVmi,ExpiredCmi,ExpiredVmi
//        Check CMI Status
        $schedule->command('command:expired_cmi')->daily()->at('01:00');
        $schedule->command('command:effective_cmi')->daily()->at('01:00');
//        Check VMI Status
        $schedule->command('command:expired_vmi')->daily()->at('01:00');
        $schedule->command('command:effective_vmi')->daily()->at('01:00');

        $schedule->command('command:close_car_park')->daily()->at('01:00');
        $schedule->command('command:booking_car_park')->daily()->at('01:00');

        // test run every minute
        // $schedule->command('command:close_car_park')->everyMinute();
        // $schedule->command('command:booking_car_park')->everyMinute();

        //run command expired coupon
        $schedule->command('command:expired_coupon')->daily()->at('01:00');

        //run command check expired contract
        $schedule->command('command:check_contract_expire')->daily()->at('01:00');

        //run command update status repair order
        $schedule->command('command:repair_order_status')->daily()->at('01:00');

        //run gps service
        $schedule->command('tls:get_gps_master_datas')->daily()->at('02:00');
        $schedule->command('tls:get_gps_veh_events')->hourly();
        
        // run ownership transfer change status to WAITING_DOCUMENT_TRANSFER
        $schedule->command('tls:change_status_ownership_transfer')->daily()->at('02:00');
          // run ownership transfer change status to WAITING_DOCUMENT_TRANSFER
        $schedule->command('tls:create_tax_renewal')->daily()->at('02:00');
        /* $schedule->call(function () {
            Log::channel('sentry')->error('test cron ' . date('Y-m-d H:i:s'), [
                'time' => date('Y-m-d H:i:s')
            ]);
        })->everyMinute(); */
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
