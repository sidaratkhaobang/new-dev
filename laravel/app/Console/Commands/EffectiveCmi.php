<?php

namespace App\Console\Commands;

use App\Enums\InsuranceCarStatusEnum;
use App\Models\CMI;
use App\Jobs\EffectiveCmiJob;
use Illuminate\Console\Command;
use Carbon\Carbon;

class EffectiveCmi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:effective_cmi';

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
     * @return int
     */
    public function handle()
    {

//      get data for check end term policy
        $cmi_data = CMI::whereDate('term_start_date', '<=', now())
            ->where('status_cmi', InsuranceCarStatusEnum::RENEW_POLICY)
            ->get();
//        loop for insert queue to update CMI status to UNDER_POLICY
        if (!empty($cmi_data)) {
            foreach ($cmi_data as $key_cmi => $value_cmi) {
                $cmi_status_update = new EffectiveCmiJob($value_cmi);
                $dateTime = Carbon::parse($value_cmi->term_start_date);
                dispatch($cmi_status_update)->delay($dateTime);
            }
        }
    }
}
