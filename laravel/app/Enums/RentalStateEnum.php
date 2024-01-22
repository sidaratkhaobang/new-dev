<?php

namespace App\Enums;

abstract class RentalStateEnum
{
    // short term rental
    const SERVICE_TYPE = 'SERVICE_TYPE';
    const INFO = 'INFO';
    const ASSET = 'ASSET'; // car + boat
    const DRIVER = 'DRIVER';
    const PROMOTION = 'PROMOTION';
    const SUMMARY = 'SUMMARY';
    
    // edit
    const INFO_EDIT = 'INFO_EDIT';
    const ASSET_EDIT = 'ASSET_EDIT';
    const DRIVER_EDIT = 'DRIVER_EDIT';
    const PROMOTION_EDIT = 'PROMOTION_EDIT';
    const SUMMARY_EDIT = 'SUMMARY_EDIT';

    // long term rental
    // TODO
}
