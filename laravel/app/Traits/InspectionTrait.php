<?php

namespace App\Traits;

use App\Enums\InspectionTypeEnum;
use App\Enums\InspectionFormEnum;
use App\Models\LongTermRental;
use App\Models\PurchaseOrder;
use App\Models\Rental;
use App\Models\InspectionFormSection;
use App\Models\InspectionJob;
use App\Models\InspectionJobStep;
use App\Models\InspectionJobChecklist;
use App\Models\Driver;
use App\Models\User;

trait InspectionTrait
{
    public static function getModelClassByInspectionType($inspection_type, $is_model = false)
    {
        $purchase_order_class_list = [InspectionTypeEnum::NEW_CAR, InspectionTypeEnum::CHANGE_TYPE];
        $short_term_rental_class_list = [InspectionTypeEnum::SELF_DRIVE, InspectionTypeEnum::MINI_COACH, InspectionTypeEnum::LIMOUSINE, InspectionTypeEnum::CARGO_TRUCK, InspectionTypeEnum::BOAT, InspectionTypeEnum::BUS, InspectionTypeEnum::SLIDE_FORKLIFT];
        $long_term_rental_class_list = [InspectionTypeEnum::LONG_TERM_RENTAL];

        if (in_array($inspection_type, $purchase_order_class_list)) {
            return ($is_model) ? PurchaseOrder::class : 'PURCHASE_ORDER';
        }
        if (in_array($inspection_type, $short_term_rental_class_list)) {
            return ($is_model) ? Rental::class : 'SHORT_TERM_RENTAL';
        }
        if (in_array($inspection_type, $long_term_rental_class_list)) {
            return ($is_model) ? LongTermRental::class : 'LONG_TERM_RENTAL';
        }
        return NULL;
    }

    public static function saveInspectionJobStep($step_form_detail, $inspection_job_id, $inspection_flow)
    {
        foreach ($step_form_detail as $step_form_data) {
            $inspection_job_step = new InspectionJobStep();
            $inspection_job_step->inspection_job_id = $inspection_job_id;
            $inspection_job_step->inspection_step_id = $step_form_data->inspection_step_id;
            $inspection_job_step->inspection_form_id = $step_form_data->inspection_form_id;
            $inspection_job_step->transfer_type = $step_form_data->transfer_type;
            $inspection_job_step->transfer_reason = $step_form_data->transfer_reason;
            $inspection_job_step->inspection_department_id = $step_form_data->inspection_department_id;
            $inspection_job_step->is_need_images = $step_form_data->is_need_images;
            $inspection_job_step->is_need_inspector_sign = $step_form_data->is_need_inspector_sign;
            $inspection_job_step->is_need_send_mobile = $step_form_data->is_need_send_mobile;
            $inspection_job_step->is_need_dpf = $step_form_data->is_need_dpf;
            $inspection_job_step->inspection_role_id = $step_form_data->inspection_role_id;
            $inspection_job_step->inspector_type = $step_form_data->is_need_send_mobile == STATUS_ACTIVE ? Driver::class : User::class;
            $inspection_job_step->inspector_id = '';
            $inspection_job_step->save();

            $inspection_section_data = InspectionFormSection::leftjoin('inspection_form_checklists', 'inspection_form_checklists.inspection_form_section_id', '=', 'inspection_form_sections.id')
                ->where('inspection_form_sections.inspection_form_id', $inspection_job_step->inspection_form_id)
                ->select(
                    'inspection_form_sections.*',
                    'inspection_form_checklists.id as checklist_id',
                    'inspection_form_checklists.name as checklist_name',
                    'inspection_form_checklists.car_part as checklist_car_part',
                )
                ->get();
            $section_count = count($inspection_section_data);
            $is_inspection_type_new_car = strcmp($inspection_flow->inspection_type, InspectionTypeEnum::NEW_CAR) == 0;
            $is_form_type_new_car = strcmp($step_form_data->form_type, InspectionFormEnum::NEWCAR) == 0;
            $is_form_type_equiment = strcmp($step_form_data->form_type, InspectionFormEnum::EQUIPMENT) == 0;
            foreach ($inspection_section_data as $inspection_section_key => $inspection_section_data) {
                $inspection_job = InspectionJob::find($inspection_job_id);
                // add accessory to check list
                if ($is_inspection_type_new_car && $is_form_type_new_car) {
                    $accessory_list = (new static)->getAccessoriesByPO($inspection_job->item_id);
                    if (sizeof($accessory_list) > 0) {
                        foreach ($accessory_list as $key => $accessory) {
                            $inspection_job_checklist = new InspectionJobChecklist();
                            $inspection_job_checklist->inspection_job_step_id = $inspection_job_step->id;
                            $inspection_job_checklist->inspection_form_section_id = $inspection_section_data->id;
                            $inspection_job_checklist->inspection_form_section_name = __('inspection_cars.form_status_' . InspectionFormEnum::ACCESSORY);
                            $inspection_job_checklist->inspection_form_checklist_id = NULL;
                            $inspection_job_checklist->inspection_form_checklist_name = $accessory->name;
                            $inspection_job_checklist->save();
                        }
                    }
                } else {
                    if ($is_inspection_type_new_car && $is_form_type_equiment && strcmp($inspection_section_key, $section_count - 1) == 0) {
                        $inspection_job = InspectionJob::find($inspection_job_id);
                        $accessory_list = (new static)->getAccessoriesByPO($inspection_job->item_id);
                        if (sizeof($accessory_list) > 0) {
                            foreach ($accessory_list as $key => $accessory) {
                                $inspection_job_checklist = new InspectionJobChecklist();
                                $inspection_job_checklist->inspection_job_step_id = $inspection_job_step->id;
                                $inspection_job_checklist->inspection_form_section_id = $inspection_section_data->id;
                                $inspection_job_checklist->inspection_form_section_name = __('inspection_cars.form_status_' . InspectionFormEnum::ACCESSORY);
                                $inspection_job_checklist->inspection_form_checklist_id = NULL;
                                $inspection_job_checklist->inspection_form_checklist_name = $accessory->name;
                                $inspection_job_checklist->save();
                            }
                        }
                    } else {
                        $inspection_job_checklist = new InspectionJobChecklist();
                        $inspection_job_checklist->inspection_job_step_id = $inspection_job_step->id;
                        $inspection_job_checklist->inspection_form_section_id = $inspection_section_data->id;
                        $inspection_job_checklist->inspection_form_section_name = $inspection_section_data->name;
                        $inspection_job_checklist->inspection_form_checklist_id = $inspection_section_data->checklist_id;
                        $inspection_job_checklist->inspection_form_checklist_name = $inspection_section_data->checklist_name;
                        if (!empty($inspection_section_data->checklist_car_part)) {
                            $car_id = $inspection_job->car_id;
                            $car_part = (new static)->getCarPartName($inspection_section_data->checklist_car_part, $car_id);
                            $inspection_job_checklist->inspection_form_checklist_name = $inspection_section_data->checklist_name . ' : ' . $car_part->car_part_name;
                        }
                        $inspection_job_checklist->save();
                    }
                }
            }
        }
        return true;
    }

    static function getInspectionJob($model = null, $id)
    {
        $inpsection_job = InspectionJob::where(function ($query) use ($model, $id) {
            if (!empty($model)) {
                $query->where('item_type', $model);
            }
            if (!empty($id)) {
                $query->where('item_id', $id);
            }
        })->first();
        return $inpsection_job;
    }
}
