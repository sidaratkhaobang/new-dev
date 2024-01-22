<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class StepApproveManagement extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'step-approve.management';
    }
}
