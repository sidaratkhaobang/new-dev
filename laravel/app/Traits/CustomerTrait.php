<?php

namespace App\Traits;

use App\Enums\CustomerTypeEnum;
use Illuminate\Support\Facades\DB;

trait CustomerTrait
{
    public static function getCustomerType()
    {
        $customer_type = collect([
            (object) [
                'id' => CustomerTypeEnum::GOVERNMENT,
                'name' => __('customers.type_' . CustomerTypeEnum::GOVERNMENT),
                'value' => CustomerTypeEnum::GOVERNMENT,
            ],
            (object) [
                'id' => CustomerTypeEnum::CORPORATION,
                'name' => __('customers.type_' . CustomerTypeEnum::CORPORATION),
                'value' => CustomerTypeEnum::CORPORATION,
            ],
            (object) [
                'id' => CustomerTypeEnum::PERSONAL,
                'name' => __('customers.type_' . CustomerTypeEnum::PERSONAL),
                'value' => CustomerTypeEnum::PERSONAL,
            ],
        ]);
        return $customer_type;
    }

    public static function getCustomerGrade()
    {
        $customer_grade = collect([
            (object) [
                'id' => 1,
                'name' => __('customers.grade_1'),
                'value' => 1,
            ],
            (object) [
                'id' => 2,
                'name' => __('customers.grade_2'),
                'value' => 2,
            ],
            (object) [
                'id' => 3,
                'name' => __('customers.grade_3'),
                'value' => 3,
            ],
            (object) [
                'id' => 4,
                'name' => __('customers.grade_4'),
                'value' => 4,
            ],
        ]);
        return $customer_grade;
    }
}
