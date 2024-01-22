<?php

namespace App\Console\Commands;

use App\Enums\InsuranceCarStatusEnum;
use App\Jobs\EffectiveVmiJob;
use App\Models\VMI;
use Carbon\Carbon;
use Illuminate\Console\Command;

class EffectiveVmi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:effective_vmi';

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
        $vmi_data = VMI::whereDate('term_start_date', '<=', now())
            ->where('status_vmi', InsuranceCarStatusEnum::RENEW_POLICY)
            ->get();
        //        loop for insert queue to update VMI status to UNDER_POLICY
        if (!empty($vmi_data)) {
            foreach ($vmi_data as $key_vmi => $value_vmi) {
                $vmi_status_update = new EffectiveVmiJob($value_vmi);
                $dateTime = Carbon::parse($value_vmi->term_start_date);
                dispatch($vmi_status_update)->delay($dateTime);
            }
        }
    }
}
