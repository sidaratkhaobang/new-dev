<?php

namespace App\Classes;

use App\Models\Driver;
use Carbon\Carbon;
use PhpParser\Node\Scalar\String_;

class DriverWagesManagement
{
    public string $driver_id;
    public string $start_date;
    public string $end_date;
    public string $actual_start_date;
    public string $actual_end_date;

    private string $validateMessageError = '';
    private bool $validateIsFailed = false;

    private int $timeInWork = 0;
    private int $timeOffWork = 0;

    public function __construct()
    {
    }

    /**
     * @param string $driver_id
     */
    public function setDriverId(string $driver_id): void
    {
        $this->driver_id = $driver_id;
    }

    /**
     * @param string $start_date
     */
    public function setStartDate(string $start_date): void
    {
        $this->start_date = $start_date;
    }

    /**
     * @param string $end_date
     */
    public function setEndDate(string $end_date): void
    {
        $this->end_date = $end_date;
    }

    /**
     * @param string $actual_start_date
     */
    public function setActualStartDate(string $actual_start_date): void
    {
        $this->actual_start_date = $actual_start_date;
    }

    /**
     * @param string $actual_end_date
     */
    public function setActualEndDate(string $actual_end_date): void
    {
        $this->actual_end_date = $actual_end_date;
    }

    private function getDriver(): Driver
    {
        return Driver::find($this->driver_id);
    }

    private function convertStringToCarbonAndGetTimeFormat(string $strValue): string
    {
        try {
            $date_time = Carbon::parse($strValue);
            return $date_time->format('H:i:s');
        } catch (\Exception $exception) {
            $this->validateMessageError = $exception->getMessage();
            $this->validateIsFailed = true;
            return '';
        }
    }

    public function execute()
    {
        if ($this->validatedValue()->validateIsFailed()) {
            return $this;
        }

        $driver = $this->getDriver();

        $start_working_time = $this->convertStringToCarbonAndGetTimeFormat($driver->start_working_time);
        $end_working_time = $this->convertStringToCarbonAndGetTimeFormat($driver->end_working_time);
        if ($start_working_time < $end_working_time) {
            $this->validateIsFailed = true;
            $this->validateMessageError = 'รูปแบบเวลา "เข้างาน/ออกงาน" ของพนักงานขับรถไม่ถูกต้อง';
            return $this;
        }

        $actual_start_date = $this->convertStringToCarbonAndGetTimeFormat($this->actual_start_date);
        $actual_end_date = $this->convertStringToCarbonAndGetTimeFormat($this->actual_end_date);
        if ($actual_start_date < $actual_end_date) {
            $this->validateIsFailed = true;
            $this->validateMessageError = 'รูปแบบเวลา "เข้างาน/ออกงาน" ของใบงานคนขับไม่ถูกต้อง';
            return $this;
        }

        if (($actual_start_date < $start_working_time && $actual_end_date < $start_working_time) || $actual_start_date > $end_working_time && $actual_end_date > $end_working_time) {
            $this->timeOffWork = $actual_start_date->diffInHours($actual_end_date);
        } elseif ($actual_start_date->lessThan($start_working_time) && $actual_end_date->between($start_working_time, $end_working_time)) {
            $this->timeOffWork = $actual_start_date->diffInHours($start_working_time);
            $this->timeInWork = $actual_end_date->diffInHours($start_working_time);
        } elseif ($actual_start_date->between($start_working_time, $end_working_time) && $actual_end_date->greaterThan($end_working_time)) {
            $this->timeOffWork = $actual_end_date->diffInHours($end_working_time);
            $this->timeInWork = $actual_start_date->diffInHours($end_working_time);
        } elseif ($actual_start_date->between($start_working_time, $end_working_time) && $actual_end_date->between($start_working_time, $end_working_time)) {
            $this->timeInWork = $actual_start_date->diffInHours($actual_end_date);
        } elseif ($actual_start_date->lessThan($start_working_time) && $actual_end_date->greaterThan($end_working_time)) {
            $this->timeOffWork = $actual_start_date->diffInHours($start_working_time) + $actual_end_date->diffInHours($end_working_time);

            $start_working_time = Carbon::parse($driver->start_working_time);
            $this->timeInWork = $start_working_time->diffInHours($end_working_time);
        }

        return $this;
    }

    public function validatedValue(): DriverWagesManagement
    {
        if (!isset($this->actual_start_date)) {
            $this->validateIsFailed = true;
            $this->validateMessageError = 'ไม่พบข้อมูล "เวลาเข้างาน" ของพนักงานขับรถ';
        } elseif (!isset($this->actual_end_date)) {
            $this->validateIsFailed = true;
            $this->validateMessageError = 'ไม่พบข้อมูล "เวลาออกงาน" ของพนักงานขับรถ';
        } elseif (!isset($this->start_date)) {
            $this->validateIsFailed = true;
            $this->validateMessageError = 'ไม่พบข้อมูล "วันที่เริ่มงาน" ของใบงานคนขับ';
        } elseif (!isset($this->end_date)) {
            $this->validateIsFailed = true;
            $this->validateMessageError = 'ไม่พบข้อมูล "วันที่สิ้นสุดงาน" ของใบงานคนขับ';
        } else {
            $this->validateIsFailed = false;
            $this->validateMessageError = '';
        }

        return $this;
    }

    public function validateMessageError(): string
    {
        return $this->validateMessageError;
    }

    public function validateIsFailed(): bool
    {
        return !isset($this->validateIsFailed) ? true : $this->validateIsFailed;
    }

}
