<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CheckDistance;
use App\Models\CheckDistanceLine;
use App\Enums\CheckDistanceTypeEnum;
use App\Models\CarClass;
use App\Models\RepairList;
use Illuminate\Support\Facades\Log;

class CheckdistancesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Start time : ' . date('Y-m-d H:i:s'));
        $periods = $this->getPeriods();
        //Log::info('periods : ' . sizeof($periods), $periods); // 134/168

        $handle = fopen(storage_path('olddb/MT_ClassTime.csv'), "r");
        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 3) {
                continue;
            }
            $ref_id = trim($col[0]);
            $car_class_ref_id = trim($col[1]);
            $period_ref_id = trim($col[2]);

            if (empty($ref_id)) {
                continue;
            }

            $car_class = CarClass::where('ref_id', $car_class_ref_id)->first();
            if (empty($car_class)) {
                continue;
            }

            if (!isset($periods['id_' . $period_ref_id])) {
                continue;
            }
            $period = $periods['id_' . $period_ref_id];

            $exists = CheckDistance::where('ref_id', $ref_id)->exists();
            if (!$exists) {
                $d = new CheckDistance();
                $d->car_class_id = $car_class->id;
                $d->distance = $period['distance'];
                $d->month = $period['month'];
                //$d->amount = 0;
                $d->ref_id = $ref_id;
                $d->save();
            }
        }
        fclose($handle);

        // insert line

        $checklistMethods = $this->getChecklistMethods();
        //Log::info('ChecklistMethods : ' . sizeof($checklistMethods), $checklistMethods); // 11

        $checklists = $this->getChecklists();
        foreach ($checklists as $checklist) {
            $exists = RepairList::where('ref_id', $checklist['id'])->exists();
            if (!$exists) {
                $d = new RepairList();
                $d->code = $checklist['code'];
                $d->name = $checklist['name'];
                $d->price = floatval($checklist['price']);
                $d->ref_id = $checklist['id'];
                $d->save();
            }
        }
        $this->command->info('Insert RepairList finished : ' . date('Y-m-d H:i:s'));
        //Log::info('checklists : ' . sizeof($checklists)); // 77741

        $index = 0;
        $handle = fopen(storage_path('olddb/MT_ClassDetail.csv'), "r");
        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }
            $ref_id = trim($col[0]);
            $check_distance_ref_id = trim($col[1]);
            $checklist_ref_id = trim($col[2]);
            $price = floatval(trim($col[3]));
            $_check = intval(trim($col[4]));
            $remark = trim($col[5]);

            if (empty($ref_id)) {
                continue;
            }

            $check_distance = CheckDistance::where('ref_id', $check_distance_ref_id)->first();
            if (empty($check_distance)) {
                continue;
            }

            if (!isset($checklists['id_' . $checklist_ref_id])) {
                continue;
            }
            $checklist = $checklists['id_' . $checklist_ref_id];
            $repair_list = RepairList::where('ref_id', $checklist_ref_id)->first();
            if (empty($repair_list)) {
                continue;
            }

            $check = null;
            if (isset($checklistMethods['id_' . $_check])) {
                $check = $checklistMethods['id_' . $_check];
            }

            /* $exists = CheckDistanceLine::where('ref_id', $ref_id)->exists();
            if (!$exists) {
                //
            } */
            $d = new CheckDistanceLine();
            $d->check_distance_id = $check_distance->id;
            $d->repair_list_id = $repair_list->id;
            $d->price = $price;
            $d->check = $check;
            $d->remark = $remark;
            $d->ref_id = $ref_id;
            $d->save();

            $index++;
            if ($index % 1000 == 0) {
                $this->command->info('insert at ' . $index . ' : ' . date('Y-m-d H:i:s'));
            }
        }
        $this->command->info('End time : ' . date('Y-m-d H:i:s'));
        fclose($handle);
    }

    function insertLines($d)
    {
        $line = new CheckDistanceLine($d);
    }

    function getChecklistMethods()
    {
        $results = [];
        $mapping = [
            'ซ่อม' => CheckDistanceTypeEnum::REPAIR,
            'เปลี่ยน' => CheckDistanceTypeEnum::CHANGE,
            'ค่าบริการ' => CheckDistanceTypeEnum::SERVICE_CHARGE,
            'ตรวจเช็ค' => CheckDistanceTypeEnum::CHECK,
            'ปรับตั้ง' => CheckDistanceTypeEnum::ADJUST,
            'ทำความสะอาด' => CheckDistanceTypeEnum::CLEAN,
            'แก้ไข' => CheckDistanceTypeEnum::MODIFY,
            'ดับไฟ' => CheckDistanceTypeEnum::PUTTER_OUT,
            'ปะยาง' => CheckDistanceTypeEnum::RECAP,
            'บริการฟรี' => CheckDistanceTypeEnum::FREE_SERVICE,
            'ฟรีค่าแรง' => CheckDistanceTypeEnum::FREE_WAGE,
        ];
        $handle = fopen(storage_path('olddb/MT_ChkListMethod.csv'), "r");
        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 4) {
                continue;
            }
            $id = trim($col[0]);
            $name = trim($col[2]);

            if (empty($name)) {
                continue;
            }

            $results['id_' . $id] = $mapping[$name];
        }
        fclose($handle);
        return $results;
    }

    function getPeriods()
    {
        $results = [];
        $handle = fopen(storage_path('olddb/MT_Period.csv'), "r");
        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }
            $id = trim($col[0]);
            $distance = trim($col[1]);
            $month = trim($col[3]);
            $status = trim($col[4]);

            if (empty($status)) {
                continue;
            }

            if (empty($distance)) {
                continue;
            }

            $results['id_' . $id] = [
                'distance' => $distance,
                'month' => $month,
            ];
        }
        fclose($handle);
        return $results;
    }

    function getChecklists()
    {
        $results = [];
        $handle = fopen(storage_path('olddb/MT_ChkList.csv'), "r");
        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }
            $id = trim($col[0]);
            $code = trim($col[1]);
            $name = trim($col[3]);
            $price = trim($col[6]);

            if (empty($name)) {
                continue;
            }

            $results['id_' . $id] = [
                'code' => $code,
                'name' => $name,
                'price' => $price,
                'id' => $id,
            ];
        }
        fclose($handle);
        return $results;
    }
}
