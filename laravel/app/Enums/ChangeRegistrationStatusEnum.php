<?php

namespace App\Enums;

abstract class ChangeRegistrationStatusEnum
{
    const WAITING_DOCUMENT = 'WAITING_DOCUMENT'; 
    const WAITING_SEND_DLT = 'WAITING_SEND_DLT'; 
    const PROCESSING = 'PROCESSING';
    const SUCCESS = 'SUCCESS';
}

