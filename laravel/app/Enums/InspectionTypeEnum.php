<?php

namespace App\Enums;

abstract class InspectionTypeEnum
{
    const NEW_CAR = 'NEW_CAR';
    const EQUIPMENT = 'EQUIPMENT';
    const LONG_TERM_RENTAL = 'LONG_TERM_RENTAL';
    const BOAT = 'BOAT';
    const SELF_DRIVE = 'SELF_DRIVE';
    const MINI_COACH = 'MINI_COACH';
    const BUS = 'BUS';
    const SPRINTER = 'SPRINTER';
    const LIMOUSINE = 'LIMOUSINE';
    const CARGO_TRUCK = 'CARGO_TRUCK';
    const SLIDE_FORKLIFT = 'SLIDE_FORKLIFT';
    const BORROWED = 'BORROWED';
    const REPLACEMENT_SD = 'REPLACEMENT_SD';
    const REPLACEMENT = 'REPLACEMENT';
    const ACCIDENT_DC = 'ACCIDENT_DC'; // DC => Delivery to customer
    const ACCIDENT_RC = 'ACCIDENT_RC'; // RC => Receive from Customer
    const ACCIDENT_DG = 'ACCIDENT_DG'; // G => Garage
    const ACCIDENT_RG = 'ACCIDENT_RG';
    const MAINTENANCE = 'MAINTENANCE';
    const MAINTENANCE_DC = 'MAINTENANCE_DC';
    const MAINTENANCE_DG = 'MAINTENANCE_DG';
    const MAINTENANCE_RC = 'MAINTENANCE_RC';
    const MAINTENANCE_RG = 'MAINTENANCE_RG';
    const TRANSFER = 'TRANSFER';
    const CHANGE_TYPE = 'CHANGE_TYPE';
}
