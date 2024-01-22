<?php

namespace App\Classes;

use App\Enums\ApproveStatusEnum;
use App\Enums\ApproveStepEnum;
use App\Enums\ConfigApproveTypeEnum;
use App\Enums\InstallEquipmentPOStatusEnum;
use App\Models\Approve;
use App\Models\ApproveLog;
use App\Models\ApproveLine;
use App\Models\Branch;
use App\Models\ConfigApprove;
use App\Models\ConfigApproveLine;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
// use App\Models\UserDepartment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StepApproveManagement
{
    public $approve;
    public $approve_history_logs;

    public function __construct()
    {
        $this->approve = null;
        $this->approve_history_logs = [];
    }

    public function getHistoryLogs()
    {
        return $this->approve_history_logs;
    }

    function validateJob($job_id)
    {
        if (empty($job_id)) {
            return false;
        }
        $approve = Approve::where('job_id', $job_id)->first();
        if (empty($approve)) {
            return false;
        }
        return $approve;
    }

    function needApprove($job_id)
    {
        if (!$approve = $this->validateJob($job_id)) {
            return false;
        }
        return (strcmp($approve->status, ApproveStatusEnum::CONFIRM) != 0) ? $approve : false;
    }


    public function checkCanApprove($model, $id, $config_enum = null, $test_user = null)
    {
        $config_approve = ConfigApprove::where('type', $config_enum)->first();
        $approve = null;
        if ($config_approve) {
            $approve = Approve::where('job_type', $model)->where('job_id', $id)->where('config_approve_id', $config_approve->id)->first();
        } else {
            $approve = Approve::where('job_type', $model)->where('job_id', $id)->first();
        }

        if (!$approve) {
            return false;
        }
        $user = $test_user ? $test_user : Auth::user();
        $is_super_user_check = ApproveLine::where('approve_id', $approve->id)
            ->where('is_super_user', 1)
            ->where('is_pass', null)
            ->where('user_id', $user->id)
            ->where('branch_id', get_branch_id())
            ->first();

        if ($is_super_user_check) {
            return $is_super_user_check;
        }

        if (!is_null($approve->status)) {
            return false;
        }

        $approve_line_owner = $this->checkApproveLineOwner($approve, $user);
        return $approve_line_owner;
    }

    public function checkApproveLineOwner($approve, $user)
    {
        // department all
        $approve_line_owner = ApproveLine::where('approve_id', $approve->id)
            ->where('is_pass', null)
            ->where('seq', $approve->status_state)
            ->where('is_all_department', 1)
            ->where('department_id', $user->department_id)
            ->where('branch_id', get_branch_id())
            ->first();

        if ($approve_line_owner) {
            return $approve_line_owner;
        }

        // department not all, section all
        $approve_line_owner = ApproveLine::where('approve_id', $approve->id)
            ->where('is_pass', null)
            ->where('seq', $approve->status_state)
            ->where('is_all_section', 1)
            ->where('section_id', $user->section_id)
            ->where('branch_id', get_branch_id())
            ->first();

        if ($approve_line_owner) {
            return $approve_line_owner;
        }

        // department not all, section not all, role all
        $approve_line_owner = ApproveLine::where('approve_id', $approve->id)
            ->where('is_pass', null)
            ->where('seq', $approve->status_state)
            ->where('role_id', $user->role_id)
            ->where('is_all_role', 1)
            ->where('branch_id', get_branch_id())
            ->first();

        if ($approve_line_owner) {
            return $approve_line_owner;
        }

        // user
        $approve_line_owner = ApproveLine::where('approve_id', $approve->id)
            ->where('is_pass', null)
            ->where('seq', $approve->status_state)
            ->where('user_id', $user->id)
            ->where('branch_id', get_branch_id())
            ->first();
        if ($approve_line_owner) {
            return $approve_line_owner;
        }

        return $approve_line_owner;
    }



    public function logApprove($job_type, $job_id, $config_enum = null)
    {
        $config_approve = ConfigApprove::where('type', $config_enum)->first();
        $approve = null;
        if ($config_approve) {
            $approve = Approve::where('job_type', $job_type)->where('job_id', $job_id)->where('config_approve_id', $config_approve->id)->first();
        } else {
            $approve = Approve::where('job_type', $job_type)->where('job_id', $job_id)->first();
        }
        if ($approve) {
            $approve_line_all = ApproveLine::where('approve_id', $approve->id)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->count();
            $approve_line_is_pass_null = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->count();
            $approve_line_is_super_user = ApproveLine::where('approve_id', $approve->id)->whereNotNull('is_pass')->whereIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->count();
            if (strcmp($approve_line_all, $approve_line_is_pass_null) === 0 && $approve_line_is_super_user == 0) {
                $approve_line_list = ApproveLine::where('approve_id', $approve->id)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->get();
                $approve_line_list->map(function ($item) {
                    if ($item->user_id) {
                        $user = User::find($item->user_id);
                        $item->user_name = ($user && $user->name) ? $user->name : '';
                        $branch = Branch::find($user->branch_id);
                        $item->branch_name = ($branch && $branch->name) ? $branch->name : '';
                    } else {
                        $item->user_name = '';
                    }
                    if ($item->role_id) {
                        $role = Role::find($item->role_id);
                        $item->role_name = ($role && $role->name) ? $role->name : '';
                    } else {
                        $item->role_name = '';
                    }

                    if ($item->department_id) {
                        $department = Department::find($item->department_id);
                        $item->department_name = ($department && $department->name) ? $department->name : '';
                    } else {
                        $item->department_name = '';
                    }
                    return $item;
                });
                $approve_line_list = $approve_line_list->toArray();
            } else { // approve log
                $approve_log_id = ApproveLog::where('approve_id', $approve->id)->where('status_active', STATUS_ACTIVE)->orderBy('seq', 'asc')->pluck('approve_line_id');
                $log_approve_arr = [];
                foreach ($approve_log_id as $app_log) {
                    $log_arr = [];
                    $approve_line_id = ApproveLine::find($app_log);
                    $approve_log_data = ApproveLog::where('approve_line_id', $app_log)->first();
                    $log_arr['seq'] = $approve_line_id->seq;
                    $log_arr['is_pass'] = $approve_line_id->is_pass;
                    $log_arr['approved_date'] = $approve_log_data->approved_date;
                    $log_arr['status'] = $approve_log_data->status;
                    $log_arr['reason'] = ($approve_log_data->reason) ? $approve_log_data->reason : '';
                    if ($approve_log_data->user_id) {
                        $user = User::find($approve_log_data->user_id);
                        $log_arr['user_name'] = ($user && $user->name) ? $user->name : '';
                        $branch = Branch::find($user->branch_id);
                        $log_arr['branch_name'] = ($branch && $branch->name) ? $branch->name : '';
                    } else {
                        $log_arr['user_name'] = '';
                    }

                    if ($user->role_id) {
                        $role = Role::find($user->role_id);
                        $log_arr['role_name'] = ($role && $role->name) ? $role->name : '';
                    } else {
                        $log_arr['role_name'] = '';
                    }

                    if ($user->department_id) {
                        $department = Department::find($user->department_id);
                        $log_arr['department_name'] = ($department && $department->name) ? $department->name : '';
                    } else {
                        $log_arr['department_name'] = '';
                    }
                    if ($log_arr) {
                        $log_approve_arr[] = $log_arr;
                    }
                    if ($approve_line_id->seq == STATUS_DEFAULT || $approve_line_id->is_pass == STATUS_DEFAULT) {
                        $is_break = true;
                        break;
                    }
                }
                $this->approve_history_logs = $log_approve_arr;
                $approve_line_list = $log_approve_arr;

                if (!isset($is_break)) {
                    // approve line waiting
                    $approve_line = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->get();
                    foreach ($approve_line as $app_line) {
                        $line_arr = [];
                        $line_arr['seq'] = $app_line->seq;
                        $line_arr['is_pass'] = $app_line->is_pass;
                        if ($app_line->user_id) {
                            $user = User::find($app_line->user_id);
                            $line_arr['user_name'] = ($user && $user->name) ? $user->name : '';
                            $branch = Branch::find($user->branch_id);
                            $line_arr['branch_name'] = ($branch && $branch->name) ? $branch->name : '';
                        } else {
                            $line_arr['user_name'] = '';
                            $line_arr['branch_name'] = '';
                        }
                        if ($app_line->role_id) {
                            $role = Role::find($app_line->role_id);
                            $line_arr['role_name'] = ($role && $role->name) ? $role->name : '';
                        } else {
                            $line_arr['role_name'] = '';
                        }

                        if ($app_line->department_id) {
                            $department = Department::find($app_line->department_id);
                            $line_arr['department_name'] = ($department && $department->name) ? $department->name : '';
                        } else {
                            $line_arr['department_name'] = '';
                        }
                        if ($line_arr) {
                            array_push($approve_line_list, $line_arr);
                        }
                    }
                }
            }
        } else {
            $approve_line_list = null;
            $approve = null;
        }

        return [
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
        ];
    }


    public function updateApprove($model, $id, $status, $config_enum = null, $reason = null, $test_user = null)
    {
        $check_approve_line = $this->checkCanApprove($model, $id, $config_enum, $test_user);
        
        if (!$check_approve_line) {
            return ApproveStatusEnum::PENDING_REVIEW;
        }

        $super_user_approve = null;
        if ($check_approve_line) {
            $approve_line_not_check = ApproveLine::find($check_approve_line->id);
            $approve_line_not_check->is_pass = $status === ApproveStatusEnum::CONFIRM ? true : false;
            $approve_line_not_check->save();

            $approve_id = $this->saveLogApprove($check_approve_line->approve_id, $check_approve_line->id, $approve_line_not_check->is_super_user, $approve_line_not_check->is_pass, $status, $reason);
        }
        $status_install = ApproveStatusEnum::PENDING_REVIEW;
        $check_all_pass = ApproveLine::where('approve_id', $approve_id)->whereNotIn('seq', [STATUS_DEFAULT])->get();
        $check_super_user = ApproveLine::where('approve_id', $approve_id)->whereIn('seq', [STATUS_DEFAULT])->get();
        if ((count($check_all_pass) == count($check_all_pass->where('is_pass', STATUS_ACTIVE)) && count($check_all_pass) > 0)
        || (count($check_super_user) == count($check_super_user->where('is_pass', STATUS_ACTIVE)) && count($check_super_user) > 0)) {
            $status_install = ApproveStatusEnum::CONFIRM;
        } else if ($status === ApproveStatusEnum::REJECT) {
            $status_install = ApproveStatusEnum::REJECT;
        }
        return $status_install;

    }

    public function saveLogApprove($approve_id, $approve_line_id, $is_super_user, $is_pass, $status, $reason = null)
    {
        $approve = Approve::find($approve_id);
        if ($approve->id) {
            $approve_line = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->first();
            $approve_log = new ApproveLog();
            $approve_log->approve_id = $approve->id;
            $approve_log->approve_line_id = $approve_line_id;
            $approve_log_count = ApproveLog::where('approve_id', $approve->id)->count();
            if ($approve_log_count > 0) {
                $approve_log->seq = $approve_log_count + 1;
            } else {
                $approve_log->seq = 1;
            }
            $approve_log->user_id = Auth::user()->id;
            $approve_log->approved_date = Carbon::now();
            $approve_log->status = $status;
            if ($reason) {
                $approve_log->reason = $reason ? $reason : null;
            }

            $approve_log->save();
        }
        if ($approve_line) {
            $approve->status_state = $approve_line->seq;
            $approve->save();
        }

        if ($is_super_user == STATUS_ACTIVE && $is_pass == STATUS_ACTIVE) {
            $approve->status = $status;
            $approve->save();
            $super_user_approve = true;
        } else if ($is_super_user == STATUS_ACTIVE && $is_pass == STATUS_DEFAULT) {
            $approve->status = $status;
            $approve->save();
            $super_user_approve = false;
        }

        return $approve->id;

    }

    public function clearStatus($job_type_class, $id, $config_enum = null)
    {
        $config_approve = ConfigApprove::where('type', $config_enum)->first();
        $approve = null;
        if ($config_approve) {
            $approve = Approve::where('job_type', $job_type_class)->where('job_id', $id)->where('config_approve_id', $config_approve->id)->first();
        } else {
            $approve = Approve::where('job_type', $job_type_class)->where('job_id', $id)->first();
        }
        // set seq approve
        $approve_line_least = ApproveLine::where('approve_id', $approve->id)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->first();
        if ($approve_line_least) {
            $approve->status_state = $approve_line_least->seq;
            $approve->status = null;
            $approve->save();
        }

        // set approve line is pass to null
        $approve_line_is_pass_null = DB::table('approve_lines')->where('approve_id', $approve->id)->update([
            'is_pass' => null,
        ]);

        // set approve log status_active to 0
        $approve_log_status_false = DB::table('approve_logs')->where('approve_id', $approve->id)->update([
            'status_active' => STATUS_DEFAULT,
        ]);
        $super_user_approve = false;
    }


    public function logApprove2($job_type, $job_id)
    {
        $approve = Approve::where('job_type', $job_type)->where('job_id', $job_id)->first();
        if (!$approve) {
            return [
                'approve_line_list' => [],
                'approve' => [],
            ];
        }
        $approve_logs = ApproveLog::where('approve_id', $approve->id)->orderBy('seq', 'asc')->get();
        $approve_lines = ApproveLine::where('approve_id', $approve->id)->orderBy('seq', 'asc')->get();
        $approve_line_list = [];
        $is_super_user = false;
        $is_reject = false;
        foreach ($approve_logs as $key => $log) {
            $line_parent = $approve_lines->firstWhere('id', $log->approve_line_id);
            $_log = [];
            $_log['seq'] = $log->seq;
            $_log['reason'] = $log->reason;
            $_log['is_pass'] = null;
            $_log['is_super_user'] = false;
            $_log['user_name'] = null;
            $_log['role_name'] = null;
            $_log['department_name'] = null;
            $_log['approved_date'] = $log->approved_date;
            $_log['status'] = $log->status;
            if ($line_parent) {
                $_log['is_pass'] = $line_parent->is_pass;
                if ($line_parent->is_super_user) {
                    $is_super_user = true;
                    $_log['is_super_user'] = true;
                }
            }
            if (strcmp($log->status, ApproveStepEnum::REJECT) === 0) {
                $is_reject = true;
            }
            $user = $log->user_id ? User::find($log->user_id) : null;
            if ($user) {
                $_log['user_name'] = $user->name;
                $_log['role_name'] = $user->role ? $user->role->name : '';
                $_log['department_name'] = $user->department ? $user->department->name : '';
            }
            $approve_line_list[] = $_log;
        }

        if (!$is_super_user && !$is_reject) {
            $filtered_approve_lines = $approve_lines->whereNotIn('id', $approve_logs->pluck('approve_line_id')->toArray());
            foreach ($filtered_approve_lines as $key => $approve_line) {
                $_log = [];
                $_log['seq'] = $approve_line->seq;
                $_log['reason'] = '';
                $_log['is_pass'] = $approve_line->is_pass;
                $_log['is_super_user'] = $approve_line->is_super_user;
                $_log['user_name'] = null;
                $_log['role_name'] = null;
                $_log['department_name'] = null;
                $_log['reason'] = null;

                if ($approve_line->user_id) {
                    $user = User::find($approve_line->user_id);
                    $_log['user_name'] = $user ? $user->name : '';
                }

                if ($approve_line->role_id) {
                    $role = Role::find($approve_line->role_id);
                    $_log['role_name'] = $role ? $role->name : '';
                }

                if ($approve_line->department_id) {
                    $department = Department::find($approve_line->department_id);
                    $_log['department_name'] = $department ? $department->name : '';
                }
                if (!$approve_line->is_super_user) {
                    $approve_line_list[] = $_log;
                }
            }
        }

        // $approve_line_list = array_filter($approve_line_list, function ($var) {
        //     return (!$var['is_super_user']);
        // });
        return [
            'approve_line_list' => $approve_line_list,
            'approve' => $approve,
        ];
    }

    public function createModelApproval($config_approve_type, $model_type, $model_id)
    {
        $config_approve = ConfigApprove::where('type', $config_approve_type)->first();
        if (!$config_approve) {
            return false;
        }
        $approve = new Approve();
        $approve->config_approve_id = $config_approve->id;
        $approve->job_type = $model_type;
        $approve->job_id = $model_id;
        $approve->save();

        $approve_line_list = ConfigApproveLine::where('config_approve_id', $config_approve->id)
            ->where('branch_id', get_branch_id())
            ->orderBy('seq')
            ->orderBy('id')
            ->get();
        // dd($approve_line_list);
        foreach ($approve_line_list as $index => $app_line) {
            $approve_line = new ApproveLine();
            $approve_line->approve_id = $approve->id;
            $approve_line->seq = $app_line->seq;
            $approve_line->branch_id = $app_line->branch_id;
            $approve_line->department_id = $app_line->department_id;
            $approve_line->is_all_department = boolval($app_line->is_all_department);
            $approve_line->section_id = $app_line->section_id;
            $approve_line->is_all_section = boolval($app_line->is_all_section);
            $approve_line->role_id = $app_line->role_id;
            $approve_line->is_all_role = boolval($app_line->is_all_role);
            $approve_line->user_id = $app_line->user_id;
            $approve_line->is_super_user = boolval($app_line->is_super_user);

            $approve_line->save();
            // if (strcmp($index, 0) === 0) {
            //     $approve->status_state = $app_line->seq;
            //     $approve->save();
            // }
        }
        $approve_line_seq_least = ApproveLine::where('approve_id', $approve->id)->where('is_pass', null)->whereNotIn('seq', [STATUS_DEFAULT])->orderBy('seq', 'asc')->first();
        if ($approve_line_seq_least) {
            $approve->status_state = $approve_line_seq_least->seq;
            $approve->save();
        }
        return true;
    }

    public function isApproveConfigured($config_type_enum)
    {
        $approve_line_list = ConfigApproveLine::leftjoin('config_approves', 'config_approves.id', '=', 'config_approve_lines.config_approve_id')
            ->where('config_approves.type', $config_type_enum)->where('branch_id', get_branch_id())->count();
        if ($approve_line_list > 0) {
            return true;
        }
        return false;
    }
}
