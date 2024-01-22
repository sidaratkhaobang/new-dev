<?php

namespace App\Console\Commands;

use App\Enums\InsuranceCarStatusEnum;
use App\Jobs\ExpiredVmiJob;
use App\Models\VMI;
use Illuminate\Console\Command;
use Carbon\Carbon;
class ExpiredVmi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:expired_vmi';

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
        $vmi_data = VMI::whereDate('term_end_date', '=', now())
            ->where('status_vmi',InsuranceCarStatusEnum::UNDER_POLICY)
            ->get();
        if (!empty($vmi_data)) {
            foreach ($vmi_data as $key_vmi => $value_vmi) {
                $cmi_status_update = new ExpiredVmiJob($value_vmi);
                $dateTime = Carbon::parse($value_vmi->term_end_date);
                dispatch($cmi_status_update)->delay($dateTime);
            }
        }
    }
}
