<?php

namespace App\Enums;

abstract class CaseAccidentEnum
{
    const BUMP_FALL = 'BUMP_FALL';
    const MALICIOUS = 'MALICIOUS';
    const OVERTURNING = 'OVERTURNING';
    const CRASH = 'CRASH';
    const LOSS = 'LOSS';
    const FALL_WAYSIDE = 'FALL_WAYSIDE';
    const UNKNOWN_CRASH = 'UNKNOWN_CRASH';
    const STONE_THROWN = 'STONE_THROWN';
    const STONE_THROWN_CAR = 'STONE_THROWN_CAR';
    const STONE_THROWN_TAIL_LAMP = 'STONE_THROWN_TAIL_LAMP';
    const OTHER = 'OTHER';
}
