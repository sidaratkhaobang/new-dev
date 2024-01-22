<?php

namespace Database\Seeders;

use App\Enums\InspectionFormEnum;
use Illuminate\Database\Seeder;
use App\Models\InspectionForm;
use App\Models\InspectionFormSection;
use App\Models\InspectionFormChecklist;

class InspectionFormsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forms = [
            'equipment' => 'ฟอร์มตรวจอุปกรณ์/คุณภาพ',
            'availability' => 'ฟอร์มตรวจความสะอาด/ความพร้อม',
            'sprinter' => 'ฟอร์มตรวจ sprinter',
            'minicoach' => 'ฟอร์มตรวจ mini coach',
            'bus' => 'ฟอร์มตรวจ bus',
            'van' => 'ฟอร์มตรวจ van',
            'limousine' => 'ฟอร์มตรวจ Limousine',
            'cargo' => 'ฟอร์มตรวจรถขนส่งมีตู้สินค้า',
            'slide' => 'ฟอร์มตรวจรถสไลด์',
            'accident' => 'ฟอร์มตรวจอุบัติเหตุ',
            'newcar' => 'ฟอร์มตรวจรถใหม่',
            'install' => 'ฟอร์มอุปกรณ์ที่ติดตั้ง',
            'boat' => 'ฟอร์มตรวจเรือ',
            'pdi' => 'ฟอร์มตรวจคุณภาพรถยนต์ (PDI)',
            'selfdrive' => 'ฟอร์มตรวจ selfdrive',
            'availability_sd' => 'ฟอร์มตรวจความสะอาดความพร้อม selfdrive',
            'replace' => 'ฟอร์มตรวจอุปกรณ์รถยนตร์ทดแทน',
            'driver' => 'ฟอร์มตรวจพร้อมคนขับ',
            'transport' => 'ฟอร์มตรวจรถขนส่ง',
            'before_after' => 'ฟอร์มตรวจสภาพรถยนต์ก่อนและหลังนำรถเข้าซ่อมบำรุง',
            'accident_customer' => 'ฟอร์มตรวจอุบัติเหตุ (ลูกค้า)',
            'garage' => 'ฟอร์มตรวจรถยนต์สำหรับอู่ซ่อม',
            'repair_accident' => 'ฟอร์มตรวจสอบงานซ่อมอุบัติเหตุรถยนต์',
            'before_install' => 'ฟอร์มตรวจอุปกรณ์ก่อนติดตั้ง',
        ];
        $last_form_id = null;
        foreach ($forms as $form => $name) {
            // form
            $inspection_form = InspectionForm::where('name', $name)->first();
            if (!$inspection_form) {
                $inspection_form = new InspectionForm();
                $inspection_form->name = $name;
                $inspection_form->is_standard = true;
                $inspection_form->form_type = strtoupper($form);
                $inspection_form->status = STATUS_ACTIVE;
                $inspection_form->save();
            }
            //New Car Config
            if (strcmp(strtoupper($form), InspectionFormEnum::NEWCAR) == 0) {
                $this->saveFormSelectionNewCar($inspection_form->id);
                continue;
            }

            $last_section_id = null;
            $section_seq = 1;
            $list_seq = 1;
            $handle = fopen(storage_path('init/forms/' . $form . '.csv'), "r");
            while ($col = fgetcsv($handle, 20000, ",")) {
                $section = trim($col[0]);
                $list = trim($col[1]);

                if (!empty($section)) {
                    $inspection_form_section = InspectionFormSection::where('name', $section)->where('inspection_form_id', $inspection_form->id)->first();
                    if (!$inspection_form_section) {
                        $inspection_form_section = new InspectionFormSection();
                        $inspection_form_section->name = $section;
                        $inspection_form_section->seq = $section_seq;
                        $inspection_form_section->inspection_form_id = $inspection_form->id;
                        $inspection_form_section->save();
                        $section_seq++;
                        $list_seq = 1;
                    }
                }

                if (!empty($list)) {
                    $exists3 = InspectionFormChecklist::where('name', $list)->where('inspection_form_section_id', $inspection_form_section->id)->exists();
                    if (!$exists3) {
                        $d3 = new InspectionFormChecklist();
                        $d3->name = $list;
                        $d3->seq = $list_seq;
                        $d3->inspection_form_section_id = $inspection_form_section->id;
                        $d3->save();
                        $list_seq++;
                    }
                }
            }
        }

        $inspection_equipment_form = InspectionForm::where('form_type', 'EQUIPMENT')->first();
        if ($inspection_equipment_form) {
            $this->saveFormSelectionNewCar($inspection_equipment_form->id);
        }
    }

    public function saveFormSelectionNewCar($form_id)
    {
        $section = 'รายการของแถมตามใบสั่งซื้อ';
        $exists = InspectionFormSection::where('name', $section)->where('inspection_form_id', $form_id)->exists();
        if (!$exists) {
            $d = new InspectionFormSection();
            $d->name = $section;
            $d->seq = 9999;
            $d->inspection_form_id = $form_id;
            $d->save();
        }
        return true;
    }
}
