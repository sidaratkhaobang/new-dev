<?php

namespace App\Traits;

use App\Enums\CustomerTypeEnum;
use Illuminate\Support\Facades\DB;

trait DayTrait
{
    function getHoursDiff($start_date_time, $end_date_time)
    {
        /* $date_1 = new DateTime($start_date_time);
        $date_2 = new DateTime($end_date_time);
        $diff = $date_1->diff($date_2);
        $hours_h = $diff->h;
        $hours_d = $diff->d * 24;
        $hours_m = $diff->m * 24 * 30;
        $hours_y = $diff->y * 24 * 365;
        $hours = $hours_h + $hours_d + $hours_m + $hours_y; */

        $hours = intval(ceil((strtotime($end_date_time) - strtotime($start_date_time)) / 60 / 60));
        return $hours;
    }

    function getDaysDiff($start_date_time, $end_date_time)
    {
        $days = intval(ceil((strtotime($end_date_time) - strtotime($start_date_time)) / 60 / 60 / 24));
        return $days;
    }

    function isDateLessThan($start_date_time, $end_date_time)
    {
        return strtotime($end_date_time) > strtotime($start_date_time);
    }
}
