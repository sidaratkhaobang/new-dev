<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Classes\Sap\SapProcess;
// use Illuminate\Support\Facades\Log;

class CheckExpiredCoupon implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $promotion_code_arr;

    public function __construct($promotion_code_arr)
    {
        $this->promotion_code_arr = $promotion_code_arr;
    }

    public function handle()
    {
        $sap = new SapProcess();
        $sap->afterExpiredCoupon($this->promotion_code_arr);
    }
}
