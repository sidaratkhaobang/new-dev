<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\Position;
use App\Models\Province;
use App\Models\Branch;
use App\Enums\EmployeeStatusEnum;

class DriversSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/database/drivers2.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 20) {
                continue;
            }
            $id = trim($col[0]);
            $_name = trim($col[1]);
            $code = trim($col[2]);
            $emp_status = trim($col[3]);
            $_position_id = trim($col[4]);
            $_province_id = trim($col[5]);
            $tel = trim($col[6]);
            $phone = trim($col[7]);
            $citizen_id = trim($col[8]);
            $start_working_time = trim($col[9]);
            $end_working_time = trim($col[10]);

            $working_day_mon = trim($col[11]);
            $working_day_tue = trim($col[12]);
            $working_day_wed = trim($col[13]);
            $working_day_thu = trim($col[14]);
            $working_day_fri = trim($col[15]);
            $working_day_sat = trim($col[16]);
            $working_day_sun = trim($col[17]);

            $_branch_id = trim($col[18]);

            if (empty($_name)) {
                continue;
            }

            $citizen_id = str_replace(' ', '', $citizen_id);

            $_name = str_replace('  ', ' ', $_name);
            $_name = str_replace('  ', ' ', $_name);
            $name = explode(' ', $_name);
            if (sizeof($name) != 2) {
                $name = explode(' ณ ', $_name);
                if (sizeof($name) != 2) {
                    continue;
                } else {
                    $name[1] = 'ณ ' . $name[1];
                }
            }

            if (!in_array($emp_status, [EmployeeStatusEnum::CONTRACT, EmployeeStatusEnum::FULL_TIME, EmployeeStatusEnum::NOT_SPECIFIED, EmployeeStatusEnum::PART_TIME])) {
                $emp_status = EmployeeStatusEnum::CONTRACT;
                $this->command->warn('emp_status not found');
            }

            $position = Position::where('name', $_position_id)->first();
            if (empty($position)) {
                $this->command->warn('position not found');
            }

            $province = Province::where('id', $_province_id)->first(); // or ref_id
            if (empty($province)) {
                $this->command->warn('province not found : ' . $_province_id);
            }

            $branch = Branch::where('name', $_branch_id)->first();
            if (empty($branch)) {
                $this->command->warn('branch not found : ' . $_branch_id);
            }

            $start_working_time = date('H:i', strtotime($start_working_time));
            $end_working_time = date('H:i', strtotime($end_working_time));
            //$status = ((strcmp($status, '1') == 0) ? STATUS_ACTIVE : STATUS_INACTIVE);

            $d = Driver::where('name', ($name[0] . ' ' . $name[1]))->first();
            if (!$d) {
                $this->command->info('new driver');
                $d = new Driver();
                $d->name = $name[0] . ' ' . $name[1];
            }

            $d->code = $code;
            $d->emp_status = $emp_status;
            $d->position_id = $position ? $position->id : null;
            $d->province_id = $province ? $province->id : null;
            $d->branch_id = $branch ? $branch->id : null;
            $d->tel = $tel;
            $d->phone = $phone;
            $d->citizen_id = $citizen_id;

            $d->start_working_time = $start_working_time;
            $d->end_working_time = $end_working_time;
            $d->working_day_mon = boolval($working_day_mon);
            $d->working_day_tue = boolval($working_day_tue);
            $d->working_day_wed = boolval($working_day_wed);
            $d->working_day_thu = boolval($working_day_thu);
            $d->working_day_fri = boolval($working_day_fri);
            $d->working_day_sat = boolval($working_day_sat);
            $d->working_day_sun = boolval($working_day_sun);

            $d->status = STATUS_ACTIVE;
            $d->save();
        }
    }
}
