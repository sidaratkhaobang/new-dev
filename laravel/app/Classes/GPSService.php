<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class GPSService
{
    public $endpoint;
    public $key;
    public $header_key;

    public function __construct()
    {
        $this->endpoint = config('services.gps.endpoint');
        $this->key = config('services.gps.key');
        $this->header_key = 'Ocp-Apim-Subscription-Key';
    }

    function getMasterDatas()
    {
        $response = Http::withHeaders([
            $this->header_key => $this->key,
        ])->get($this->endpoint . '/tls/api/masterdatas');
        return $this->toArray($response);
    }

    function getVehEvents($start_date_time, $end_date_time)
    {
        $response = Http::withHeaders([
            $this->header_key => $this->key,
        ])->post($this->endpoint . '/tls/api/veh/events', [
            'StartTime' => $start_date_time,
            'EndTime' => $end_date_time,
        ]);
        return $this->toArray($response);
    }

    function getVehLastLocations(array $vehicle_list = [])
    {
        $vehicle_list_implode = implode(',', $vehicle_list);
        $response = Http::withHeaders([
            $this->header_key => $this->key,
        ])->post($this->endpoint . '/tls/api/veh/lastlocation', [
            'VehicleList' => $vehicle_list_implode,
        ]);
        return $this->toArray($response);
    }

    function toArray($response)
    {
        $successful = $response->successful();
        return [
            'successful' => $successful,
            'status' => $response->status(),
            'data' => ($successful ? $response->json() : []),
        ];
    }
}
