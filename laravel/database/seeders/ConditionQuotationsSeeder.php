<?php

namespace Database\Seeders;

use App\Enums\ConditionGroupEnum;
use App\Enums\LongTermRentalApprovalTypeEnum;
use App\Models\ConditionGroup;
use Illuminate\Database\Seeder;
use App\Models\ConditionQuotation;
use App\Models\ConditionQuotationChecklist;

class ConditionQuotationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $last_condition_quotation_id = null;
        $condition_quotation_seq = 1;
        $condition_quotation_checklist_seq = 1;
        $condition_group = ConditionGroup::where('condition_group', ConditionGroupEnum::LONG_TERM_RENTAL)->first();
        $handle = fopen(storage_path('init/database/condition_quotations.csv'), "r");
        while ($col = fgetcsv($handle, 20000, ",")) {
            $condition_quotation = trim($col[0]);
            $condition_quotation_checklist = trim($col[1]);
            if (!empty($condition_quotation)) {
                $exists = ConditionQuotation::where('name', $condition_quotation)->exists();
                if (!$exists) {
                    $d = new ConditionQuotation();
                    $d->name = $condition_quotation;
                    $d->seq = $condition_quotation_seq;
                    $d->status = STATUS_ACTIVE;
                    $d->condition_type = LongTermRentalApprovalTypeEnum::UNAFFILIATED;
                    if ($condition_group) {
                        $d->condition_group_id = $condition_group->id;
                    }
                    $d->save();
                    $last_condition_quotation_id = $d->id;
                    $condition_quotation_seq++;
                    $condition_quotation_checklist_seq = 1;
                }
                else {
                    $d = ConditionQuotation::where('name', $condition_quotation)->first();
                    $condition_quotation_seq++;
                    $condition_quotation_checklist_seq = 1;
                    $last_condition_quotation_id = $d->id;
                    $d->condition_type = LongTermRentalApprovalTypeEnum::UNAFFILIATED;
                    if ($condition_group) {
                        $d->condition_group_id = $condition_group->id;
                    }
                    $d->save();
                }
            }

            if (!empty($condition_quotation_checklist)) {
                $exists2 = ConditionQuotationChecklist::where('name', $condition_quotation_checklist)->where('condition_quotations_id', $last_condition_quotation_id)->exists();
                if (!$exists2) {
                    $d2 = new ConditionQuotationChecklist();
                    $d2->condition_quotations_id = $last_condition_quotation_id;
                    $d2->name = $condition_quotation_checklist;
                    $d2->seq = $condition_quotation_checklist_seq;
                    $d2->status = STATUS_ACTIVE;
                    $d2->save();
                    $condition_quotation_checklist_seq++;
                }
            }
        }
    }
}
