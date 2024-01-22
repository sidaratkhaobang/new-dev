<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Car;

class GPSUpdateVID implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $chassis_no;
    public $vid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($chassis_no, $vid)
    {
        $this->chassis_no = $chassis_no;
        $this->vid = $vid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Car::where('chassis_no', $this->chassis_no)->update([
            'vid' => $this->vid
        ]); // ->limit(1);
    }
}
