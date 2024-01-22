<?php

namespace App\Enums;

abstract class CarPartTypeEnum
{
    const GEAR = 'GEAR';
    const DRIVE_SYSTEM = 'DRIVE_SYSTEM';
    const CAR_SEAT = 'CAR_SEAT';
    const SIDE_MIRROR = 'SIDE_MIRROR';
    const AIR_BAG = 'AIR_BAG';
    const CENTRAL_LOCK = 'CENTRAL_LOCK';
    const FRONT_BRAKE = 'FRONT_BRAKE';
    const REAR_BRAKE = 'REAR_BRAKE';
    const ABS = 'ABS';
    const ANTI_THIFT_SYSTEM = 'ANTI_THIFT_SYSTEM';
    const OTHER = 'OTHER';

    //optional
    const BATTERY = 'BATTERY';
    const TIRE = 'TIRE';
    const WIPER = 'WIPER';
}
