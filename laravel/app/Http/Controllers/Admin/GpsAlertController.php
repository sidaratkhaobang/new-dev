<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class GpsAlertController extends Controller
{
    public function index(Request $request)
    {
        $license_plate = $request->license_plate;
        $chassis_no = $request->chassis_no;
        $vid = $request->vid;
        $date = $request->date;
        $s = null;

        $list = Car::select(
            'id',
            'license_plate',
            'chassis_no',
            'vid',
            'current_location',
            'gps_event_timestamp',
        )
            ->search($s, $request)
            ->when($vid, function ($query) use ($vid) {
                return $query->where('vid', 'like', '%' . $vid . '%');
            })
            ->when($date, function ($query) use ($date) {
                return $query->where('gps_event_timestamp', $date);
            })
            ->whereNotNull('gps_event_timestamp')
            ->paginate(PER_PAGE);

        $license_plate_list = Car::select('license_plate as name', 'id')->whereNotNull('gps_event_timestamp')->orderBy('created_at', 'desc')->get();
        $chassis_no_list = Car::select('chassis_no as name', 'id')->whereNotNull('gps_event_timestamp')->orderBy('created_at', 'desc')->get();
        $vid_list = Car::select('vid as name', 'vid as id')->whereNotNull('gps_event_timestamp')->whereNotNull('vid')->orderBy('created_at', 'desc')->get();
        return view('admin.gps-alerts.index', [
            'list' => $list,
            'license_plate_list' => $license_plate_list,
            'chassis_no_list' => $chassis_no_list,
            'vid_list' => $vid_list,
            'license_plate' => $license_plate,
            'chassis_no' => $chassis_no,
            'vid' => $vid,
            'date' => $date,
        ]);
    }
}
