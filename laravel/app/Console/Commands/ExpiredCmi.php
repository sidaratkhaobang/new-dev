<?php

namespace App\Console\Commands;

use App\Enums\InsuranceCarStatusEnum;
use Illuminate\Console\Command;
use App\Models\CMI;
use App\Jobs\ExpiredCmiJob;
use Carbon;

class ExpiredCmi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:expired_cmi';

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
        $cmi_data = CMI::whereDate('term_end_date', '=', now())
            ->where('status_cmi',InsuranceCarStatusEnum::UNDER_POLICY)
            ->get();
        if (!empty($cmi_data)) {
            foreach ($cmi_data as $key_cmi => $value_cmi) {
                $cmi_status_update = new ExpiredCmiJob($value_cmi);
                $dateTime = Carbon::parse($value_cmi->term_end_date);
                dispatch($cmi_status_update)->delay($dateTime);
            }
        }
        return [
            'res_code' => 200,
            'res_text' => 'success',
            'res_result' => 'job_success'
        ];
    }
}
