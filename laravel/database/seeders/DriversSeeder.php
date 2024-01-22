<?php

namespace Database\Seeders;

use App\Enums\EmployeeStatusEnum;
use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\Position;
use App\Models\Province;
use Illuminate\Support\Str;

class DriversSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBDriver.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 46) {
                continue;
            }
            $id = trim($col[0]);
            $name = trim($col[2]);
            $code = trim($col[1]);
            $_position_id = trim($col[4]);
            $_province_id = trim($col[6]);
            $tel = trim($col[9]);
            $phone = trim($col[10]);
            $citizen_id = trim($col[21]);
            $start_working_time = trim($col[11]);
            $end_working_time = trim($col[12]);
            $_emp_status = trim($col[38]);
            $status = trim($col[39]);
            $is_driver = trim($col[46]);

            if (empty($name)) {
                continue;
            }

            if (!boolval($is_driver)) {
                continue;
            }

            $start_working_time = date('H:i', strtotime($start_working_time));
            $end_working_time = date('H:i', strtotime($end_working_time));
            $status = ((strcmp($status, '1') == 0) ? STATUS_ACTIVE : STATUS_INACTIVE);
            $emp_status = EmployeeStatusEnum::FULL_TIME;
            if (strcmp($_emp_status, '1000') == 0) {
                $emp_status = EmployeeStatusEnum::NOT_SPECIFIED;
            } else if (strcmp($_emp_status, '1001') == 0) {
                $emp_status = EmployeeStatusEnum::PART_TIME;
            } else if (strcmp($_emp_status, '1002') == 0) {
                $emp_status = EmployeeStatusEnum::FULL_TIME;
            } else if (strcmp($_emp_status, '1003') == 0) {
                $emp_status = EmployeeStatusEnum::CONTRACT;
            }

            $exists = Driver::where('ref_id', $id)->exists();
            if (!$exists) {
                $position = Position::where('ref_id', $_position_id)->first();
                $province = Province::where('ref_id', $_province_id)->first();
                $d = new Driver();
                $d->name = $name;
                $d->code = $code;
                $d->emp_status = $emp_status;
                $d->position_id = $position ? $position->id : null;
                $d->province_id = $province ? $province->id : null;
                $d->tel = Str::limit($tel, 20, '');
                $d->phone = Str::limit($phone, 20, '');
                $d->citizen_id = $citizen_id;
                $d->start_working_time = $start_working_time;
                $d->end_working_time = $end_working_time;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
