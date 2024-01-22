<?php

namespace Database\Seeders;

use App\Enums\ConditionGroupEnum;
use App\Models\ConditionGroup;
use Illuminate\Database\Seeder;

class ConditionGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $enums = [
            ConditionGroupEnum::SHORT_TERM_RENTAL => 'เช่าสั้น',
            ConditionGroupEnum::LONG_TERM_RENTAL => 'เช่ายาว',
            ConditionGroupEnum::REPAIR_SERVICE => 'ส่งซ่อมศูนย์บริการ',
        ];

        foreach ($enums as $group => $name) {
            $exists = ConditionGroup::where('condition_group', $group)->exists();
            if (!$exists) {
                $d = new ConditionGroup();
                $d->name = $name;
                $d->condition_group = $group;
                $d->status = STATUS_ACTIVE;
                $d->save();
            }
        }
    }
}
