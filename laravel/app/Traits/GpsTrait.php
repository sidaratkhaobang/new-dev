<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Enums\GPSStatusEnum;
use App\Enums\GPSStopStatusEnum;
use App\Models\Branch;
use App\Enums\GPSJobTypeEnum;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\GPSHistoricalDataStatusEnum;
use App\Enums\GPSHistoricalDataTypeEnum;
use App\Models\Car;
use App\Models\GpsCheckSignal;
use App\Models\GpsHistoricalData;
use App\Models\GpsRemoveStopSignal;
use App\Models\Rental;
use App\Models\LongTermRental;
use App\Models\ReplacementCar;

trait GpsTrait
{
    static function getStatus()
    {
        $status = collect([
            (object) [
                'id' => GPSStatusEnum::PENDING,
                'name' => __('gps.status_text_' . GPSStatusEnum::PENDING),
                'value' => GPSStatusEnum::PENDING,
            ],
            (object) [
                'id' => GPSStatusEnum::CHECK_SIGNAL,
                'name' => __('gps.status_text_' . GPSStatusEnum::CHECK_SIGNAL),
                'value' => GPSStatusEnum::CHECK_SIGNAL,
            ],
            (object) [
                'id' => GPSStatusEnum::NORMAL_SIGNAL,
                'name' => __('gps.status_text_' . GPSStatusEnum::NORMAL_SIGNAL),
                'value' => GPSStatusEnum::NORMAL_SIGNAL,
            ],
            (object) [
                'id' => GPSStatusEnum::NO_SIGNAL,
                'name' => __('gps.status_text_' . GPSStatusEnum::NO_SIGNAL),
                'value' => GPSStatusEnum::NO_SIGNAL,
            ],
        ]);
        return $status;
    }

    static function getStatusApprove()
    {
        $status = collect([
            (object) [
                'id' => GPSStatusEnum::NORMAL_SIGNAL,
                'name' => __('gps.status_text_' . GPSStatusEnum::NORMAL_SIGNAL),
                'value' => GPSStatusEnum::NORMAL_SIGNAL,
            ],
            (object) [
                'id' => GPSStatusEnum::NO_SIGNAL,
                'name' => __('gps.status_text_' . GPSStatusEnum::NO_SIGNAL),
                'value' => GPSStatusEnum::NO_SIGNAL,
            ],
        ]);
        return $status;
    }

    static function getRepairList()
    {
        return collect([
            [
                'id' => BOOL_FALSE,
                'value' => BOOL_FALSE,
                'name' => __('gps.repair_pending'),
            ],
            [
                'id' => BOOL_TRUE,
                'value' => BOOL_TRUE,
                'name' => __('gps.repair_wait'),
            ],
        ]);
    }

    static function queryLicensePlateList($user_branch = null)
    {
        $query = Car::select('cars.license_plate as name', 'cars.license_plate as id')
            ->leftJoin('gps_check_signals', 'gps_check_signals.car_id', '=', 'cars.id')
            ->where('gps_check_signals.branch_id', $user_branch)
            ->distinct()
            ->get();
        return $query;
    }

    static function queryChassisNoList($user_branch = null)
    {
        $query = Car::select('cars.chassis_no as name', 'cars.chassis_no as id')
            ->leftJoin('gps_check_signals', 'gps_check_signals.car_id', '=', 'cars.id')
            ->where('gps_check_signals.branch_id', $user_branch)
            ->distinct()
            ->get();
        return $query;
    }

    static function queryVidList($user_branch = null)
    {
        $query = Car::select('cars.vid as name', 'cars.vid as id')->whereNotNull('vid')
            ->leftJoin('gps_check_signals', 'gps_check_signals.car_id', '=', 'cars.id')
            ->where('gps_check_signals.branch_id', $user_branch)
            ->distinct()
            ->get();
        return $query;
    }

    static function queryBranchList()
    {
        $query = Branch::select('id', 'name')
            ->get();
        return $query;
    }

    static function queryShortTermCount($user_branch = null)
    {
        $today = date('Y-m-d');
        $query = GpsCheckSignal::where('branch_id', $user_branch)
            ->where('job_type', Rental::class)
            ->whereDate('must_check_date', '=', $today)
            ->count();
        return $query;
    }

    static function queryShortBranchCount()
    {
        $today = date('Y-m-d');
        $query = GpsCheckSignal::where('job_type', Rental::class)
            ->where('check_main_branch', BOOL_TRUE)
            ->whereIN('status', [GPSStatusEnum::NORMAL_SIGNAL, GPSStatusEnum::NO_SIGNAL])
            ->whereDate('must_check_date', '=', $today)
            ->count();
        return $query;
    }

    static function queryLongTermCount()
    {
        $today = date('Y-m-d');
        $query = GpsCheckSignal::where('job_type', LongTermRental::class)
            ->whereDate('must_check_date', '=', $today)
            ->count();
        return $query;
    }

    static function queryReplacementCarCount()
    {
        $today = date('Y-m-d');
        $query = GpsCheckSignal::where('job_type', ReplacementCar::class)
            ->whereDate('must_check_date', '=', $today)
            ->count();
        return $query;
    }

    static function getRemoveStatus()
    {
        $status = collect([
            (object) [
                'id' => GPSStopStatusEnum::WAIT_REMOVE_GPS,
                'name' => __('gps.remove_status_text_' . GPSStopStatusEnum::WAIT_REMOVE_GPS),
                'value' => GPSStopStatusEnum::WAIT_REMOVE_GPS,
            ],
            (object) [
                'id' => GPSStopStatusEnum::ALERT_REMOVE_GPS,
                'name' => __('gps.remove_status_text_' . GPSStopStatusEnum::ALERT_REMOVE_GPS),
                'value' => GPSStopStatusEnum::ALERT_REMOVE_GPS,
            ],
            (object) [
                'id' => GPSStopStatusEnum::REMOVE_GPS,
                'name' => __('gps.remove_status_text_' . GPSStopStatusEnum::REMOVE_GPS),
                'value' => GPSStopStatusEnum::REMOVE_GPS,
            ],
            (object) [
                'id' => GPSStopStatusEnum::NOT_INSTALL,
                'name' => __('gps.remove_status_text_' . GPSStopStatusEnum::NOT_INSTALL),
                'value' => GPSStopStatusEnum::NOT_INSTALL,
            ],
        ]);
        return $status;
    }

    static function getStopStatus()
    {
        $status = collect([
            (object) [
                'id' => GPSStopStatusEnum::WAIT_STOP_SIGNAL,
                'name' => __('gps.stop_status_text_' . GPSStopStatusEnum::WAIT_STOP_SIGNAL),
                'value' => GPSStopStatusEnum::WAIT_STOP_SIGNAL,
            ],
            (object) [
                'id' => GPSStopStatusEnum::ALERT_STOP_SIGNAL,
                'name' => __('gps.stop_status_text_' . GPSStopStatusEnum::ALERT_STOP_SIGNAL),
                'value' => GPSStopStatusEnum::ALERT_STOP_SIGNAL,
            ],
            (object) [
                'id' => GPSStopStatusEnum::STOP_SIGNAL,
                'name' => __('gps.stop_status_text_' . GPSStopStatusEnum::STOP_SIGNAL),
                'value' => GPSStopStatusEnum::STOP_SIGNAL,
            ],
        ]);
        return $status;
    }

    static function getJobTypeList()
    {
        return collect([
            (object)[
                'id' => GPSJobTypeEnum::CONTRACT_EXPIRED,
                'value' => GPSJobTypeEnum::CONTRACT_EXPIRED,
                'name' => __('gps.stop_job_type_' . GPSJobTypeEnum::CONTRACT_EXPIRED),
            ],
            (object)[
                'id' => GPSJobTypeEnum::AUCTION_SALE,
                'value' => GPSJobTypeEnum::AUCTION_SALE,
                'name' => __('gps.stop_job_type_' . GPSJobTypeEnum::AUCTION_SALE),
            ],
            (object)[
                'id' => GPSJobTypeEnum::TOTAL_LOSS,
                'value' => GPSJobTypeEnum::TOTAL_LOSS,
                'name' => __('gps.stop_job_type_' . GPSJobTypeEnum::TOTAL_LOSS),
            ],
            (object)[
                'id' => GPSJobTypeEnum::CONTRACT_BRANCH,
                'value' => GPSJobTypeEnum::CONTRACT_BRANCH,
                'name' => __('gps.stop_job_type_' . GPSJobTypeEnum::CONTRACT_BRANCH),
            ],
            (object)[
                'id' => GPSJobTypeEnum::AFTER_CONTRACT_EXPIRED,
                'value' => GPSJobTypeEnum::AFTER_CONTRACT_EXPIRED,
                'name' => __('gps.stop_job_type_' . GPSJobTypeEnum::AFTER_CONTRACT_EXPIRED),
            ],
        ]);
    }

    static function getRemoveStatusList()
    {
        $status = collect([
            (object) [
                'id' => GPSStopStatusEnum::REMOVE_GPS,
                'name' => __('gps.remove_status_text_' . GPSStopStatusEnum::REMOVE_GPS),
                'value' => GPSStopStatusEnum::REMOVE_GPS,
            ],
            (object) [
                'id' => GPSStopStatusEnum::NOT_INSTALL,
                'name' => __('gps.remove_status_text_' . GPSStopStatusEnum::NOT_INSTALL),
                'value' => GPSStopStatusEnum::NOT_INSTALL,
            ],
        ]);
        return $status;
    }

    static function getStopStatusList()
    {
        $status = collect([
            (object) [
                'id' => GPSStopStatusEnum::STOP_SIGNAL,
                'name' => __('gps.stop_status_text_' . GPSStopStatusEnum::STOP_SIGNAL),
                'value' => GPSStopStatusEnum::STOP_SIGNAL,
            ],
        ]);
        return $status;
    }

    static function sideBarCheckJob()
    {
        if (user_can(Actions::View . '_' . Resources::GPSCheckSignalShortTerm)) {
            return route('admin.gps-check-signal-jobs.index');
        }
        if (user_can(Actions::View . '_' . Resources::GPSCheckSignalLongTerm)) {
            return route('admin.gps-check-signal-job-long-term.index');
        }
        if (user_can(Actions::View . '_' . Resources::GPSCheckSignalShortTermBranch)) {
            return route('admin.gps-check-signal-job-branch.index');
        }
        if (user_can(Actions::View . '_' . Resources::GPSCheckSignalKratos)) {
            return route('admin.gps-check-signal-job-kratos.index');
        }
    }

    static function sideBarRemoveJob()
    {
        if (user_can(Actions::View . '_' . Resources::GPSRemoveStopSignalAlert)) {
            return route('admin.gps-remove-stop-signal-jobs.index');
        }
        if (user_can(Actions::View . '_' . Resources::GPSRemoveSignalJob)) {
            return route('admin.gps-remove-signal-jobs.index');
        }
        if (user_can(Actions::View . '_' . Resources::GPSStopSignalJob)) {
            return route('admin.gps-stop-signal-jobs.index');
        }
    }

    static function getTypeFileList()
    {
        $status = collect([
            (object) [
                'id' => GPSHistoricalDataTypeEnum::PDF,
                'name' => __('gps.type_file_' . GPSHistoricalDataTypeEnum::PDF),
                'value' => GPSHistoricalDataTypeEnum::PDF,
            ],
            (object) [
                'id' => GPSHistoricalDataTypeEnum::EXCEl,
                'name' => __('gps.type_file_' . GPSHistoricalDataTypeEnum::EXCEl),
                'value' => GPSHistoricalDataTypeEnum::EXCEl,
            ],
        ]);
        return $status;
    }

    static function getHistoricalStatus()
    {
        $status = collect([
            (object) [
                'id' => GPSHistoricalDataStatusEnum::DRAFT,
                'name' => __('gps.data_text_' . GPSHistoricalDataStatusEnum::DRAFT),
                'value' => GPSHistoricalDataStatusEnum::DRAFT,
            ],
            (object) [
                'id' => GPSHistoricalDataStatusEnum::REQUEST,
                'name' => __('gps.data_text_' . GPSHistoricalDataStatusEnum::REQUEST),
                'value' => GPSHistoricalDataStatusEnum::REQUEST,
            ],
            (object) [
                'id' => GPSHistoricalDataStatusEnum::CONFIRM,
                'name' => __('gps.data_text_' . GPSHistoricalDataStatusEnum::CONFIRM),
                'value' => GPSHistoricalDataStatusEnum::CONFIRM,
            ],
            (object) [
                'id' => GPSHistoricalDataStatusEnum::REJECT,
                'name' => __('gps.data_text_' . GPSHistoricalDataStatusEnum::REJECT),
                'value' => GPSHistoricalDataStatusEnum::REJECT,
            ],
        ]);
        return $status;
    }

    static function getFiscalYearList()
    {
        $year = date('Y');
        $year_range = range($year + 5, $year - 3);
        $years = collect($year_range)->map(function ($item) {
            return (object)[
                'id' => $item,
                'value' => $item,
                'name' => $item,
            ];
        });
        return $years;
    }

    static function fiscalMonth()
    {
        return ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
    }

    public static function createGPSRemoveStopSignal($job_type, $car_id, $inform_date = null, $is_check_gps)
    {
        if (strcmp($job_type, GPSJobTypeEnum::AUCTION_SALE) == 0) {
            $prefix = 'AUC';
        }
        if (strcmp($job_type, GPSJobTypeEnum::TOTAL_LOSS) == 0) {
            $prefix = 'RMD';
        }

        if ($car_id) {
            $remove_stop_exists = GpsRemoveStopSignal::where('car_id', $car_id)->exists();
            if (!$remove_stop_exists) {
                $remove_stop = new GpsRemoveStopSignal();
            } else {
                $remove_stop = GpsRemoveStopSignal::where('car_id', $car_id)->first();
            }
            $remove_stop_count = GpsRemoveStopSignal::where('worksheet_no', 'like', '%' . $prefix . '%')
                ->where('job_type', $job_type)->count();
            $remove_stop->worksheet_no = generateRecordNumber($prefix, $remove_stop_count + 1);
            $remove_stop->job_type = $job_type;
            $remove_stop->car_id = $car_id;
            $remove_stop->inform_date = ($inform_date) ? $inform_date : date('Y-m-d');
            $remove_stop->is_check_gps = $is_check_gps;
            if (strcmp($is_check_gps, STATUS_ACTIVE) == 0) {
                $remove_stop->remove_status = GPSStopStatusEnum::WAIT_REMOVE_GPS;
                $remove_stop->stop_status = null;
            } else if (strcmp($is_check_gps, STATUS_INACTIVE) == 0) {
                $remove_stop->remove_status = null;
                $remove_stop->stop_status = GPSStopStatusEnum::WAIT_STOP_SIGNAL;
            }
            $remove_stop->save();
        }
    }
}
