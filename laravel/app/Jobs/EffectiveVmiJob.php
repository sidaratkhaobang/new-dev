<?php

namespace App\Jobs;

use App\Enums\InsuranceCarStatusEnum;
use App\Models\VMI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EffectiveVmiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $vmi_data;
    public function __construct(VMI $vmi_data)
    {
        $this->vmi_data =$vmi_data;
        $this->onConnection('database');
        $this->onQueue('EffectiveVmi');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->vmi_data->status_vmi = InsuranceCarStatusEnum::UNDER_POLICY;
        $this->vmi_data->save();
        return 'success';
    }
}
