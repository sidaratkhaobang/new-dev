<?php

namespace App\Traits;

use App\Classes\StepApproveManagement;
use App\Enums\CustomerTypeEnum;
use Illuminate\Support\Facades\DB;

trait HistoryTrait
{
    public static function getHistory($model, $id, $config_enum = null)
    {
        $approve_line_logs = [];
        $approve_line_management = new StepApproveManagement();
        $approve_return = $approve_line_management->logApprove($model, $id, $config_enum);
        $approve_line_list = $approve_return['approve_line_list'];
        $approve = $approve_return['approve'];
        $approve_line_logs = $approve_line_management->getHistoryLogs();

        return ['approve_line_logs' => $approve_line_logs, 'approve_line_list' => $approve_line_list, 'approve' => $approve];
    }
}
