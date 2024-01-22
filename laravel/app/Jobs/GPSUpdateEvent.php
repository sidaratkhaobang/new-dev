<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Car;

class GPSUpdateEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $vid;
    public $event_th;
    public $event_en;
    public $location_th;
    public $timestamp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($vid, $event_th, $event_en, $location_th, $timestamp)
    {
        $this->vid = $vid;
        $this->event_th = $event_th;
        $this->event_en = $event_en;
        $this->location_th = $location_th;
        $this->timestamp = $timestamp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Car::where('vid', $this->vid)->update([
            'current_location' => $this->location_th,
            'gps_event_th' => $this->event_th,
            'gps_event_en' => $this->event_en,
            'gps_event_timestamp' => $this->timestamp,
        ]); // ->limit(1);
    }
}
