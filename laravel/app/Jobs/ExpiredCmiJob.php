<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Enums\InsuranceCarStatusEnum;
use App\Models\CMI;

class ExpiredCmiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $cmi_data;
    public function __construct(CMI $cmi_data)
    {
        $this->cmi_data =$cmi_data;
        $this->onConnection('database');
        $this->onQueue('ExpiredCmi');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->cmi_data->status_cmi = InsuranceCarStatusEnum::END_POLICY;
        $this->cmi_data->save();
        return 'success';
    }
}
