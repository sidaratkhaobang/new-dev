<?php

namespace App\Factories;

use App\Models\InspectionJob;
use App\Models\InspectionStep;
use App\Models\InspectionFlow;
use App\Traits\InspectionTrait;
use App\Enums\TransferTypeEnum;
use Exception;
use Carbon\Carbon;

class InspectionJobFactory implements FactoryInterface
{
    public $inspection_type;
    public $item_type;
    public $item_id;
    public $car_id;
    public $transfer_type;
    public $worksheet_no;
    public $branch_id;
    public $inspection_flow;
    public $open_date;
    public $inspection_must_date;
    public $inspection_must_date_in;
    public $inspection_must_date_out;

    public function __construct($inspection_type, $item_type, $item_id, $car_id, $optionals = [])
    {
        $this->worksheet_no = null;
        $this->inspection_type = $inspection_type;
        $this->item_type = $item_type;
        $this->item_id = $item_id;
        $this->car_id = $car_id;
        $this->transfer_type = (isset($optionals['transfer_type']) ? $optionals['transfer_type'] : null);
        $this->branch_id = get_branch_id();
        $this->inspection_flow = InspectionFlow::where('inspection_type', $this->inspection_type)->first();
        $this->open_date = (isset($optionals['open_date']) ? $optionals['open_date'] : date('Y-m-d H:i:s'));
        $this->inspection_must_date = (isset($optionals['inspection_must_date']) ? $optionals['inspection_must_date'] : null);
        $this->inspection_must_date_in = (isset($optionals['inspection_must_date_in']) ? $optionals['inspection_must_date_in'] : null);
        $this->inspection_must_date_out = (isset($optionals['inspection_must_date_out']) ? $optionals['inspection_must_date_out'] : null);
    }

    function generateWorkSheetNo()
    {
        $this->worksheet_no = generate_worksheet_no(InspectionJob::class);
    }

    function create()
    {
        $this->generateWorkSheetNo();
        $this->validate();

        if (empty($this->item_type)) {
            $this->item_type = InspectionTrait::getModelClassByInspectionType($this->inspection_flow->inspection_type, true);
        }

        $step_form = InspectionFlow::leftjoin('inspection_steps', 'inspection_steps.inspection_flow_id', '=', 'inspection_flows.id')
            ->select('inspection_steps.transfer_type', 'inspection_flows.id')
            ->groupBy('inspection_steps.transfer_type', 'inspection_flows.id')
            ->orderBy('inspection_steps.transfer_type', 'DESC')
            ->where('inspection_flows.inspection_type', $this->inspection_type)
            ->when(!is_null($this->transfer_type), function ($query) {
                $query->where('inspection_steps.transfer_type', $this->transfer_type);
            })
            ->get();

        foreach ($step_form as $step_form_new) {
            // transfer_type to check transfer_reason
            $inspection_step = InspectionStep::where('inspection_flow_id', $step_form_new->id)->where('transfer_type', $step_form_new->transfer_type)->select('transfer_reason')->first();
            if ($step_form_new->transfer_type != null) {
                $inspection_job = new InspectionJob();
                $inspection_job->worksheet_no = $this->worksheet_no;
                $inspection_job->branch_id = $this->branch_id;
                $inspection_job->item_type = $this->item_type;
                $inspection_job->item_id = $this->item_id;
                $inspection_job->open_date = $this->open_date;
                $inspection_job->transfer_type = $step_form_new->transfer_type;
                $inspection_job->inspection_flow_id = $this->inspection_flow->id;
                $inspection_job->inspection_type = $this->inspection_flow->inspection_type;
                $inspection_job->transfer_reason = $inspection_step->transfer_reason; // transfer_reason first
                $inspection_job->is_need_customer_sign_in = $this->inspection_flow->is_need_customer_sign_in;
                $inspection_job->is_need_customer_sign_out = $this->inspection_flow->is_need_customer_sign_out;
                $inspection_job->car_id = $this->car_id;
                if (!empty($this->inspection_must_date)) {
                    $inspection_job->inspection_must_date = $this->inspection_must_date;
                } else {
                    if ($step_form_new->transfer_type == TransferTypeEnum::OUT) { // ขาออก
                        $inspection_job->inspection_must_date = $this->inspection_must_date_out;
                    } else {
                        $inspection_job->inspection_must_date = $this->inspection_must_date_in;
                    }
                }
                $inspection_job->save();

                $step_form_detail = InspectionFlow::leftjoin('inspection_steps', 'inspection_steps.inspection_flow_id', '=', 'inspection_flows.id')
                    ->leftjoin('inspection_forms', 'inspection_forms.id', '=', 'inspection_steps.inspection_form_id')
                    ->leftjoin('inspection_jobs', 'inspection_jobs.inspection_flow_id', '=', 'inspection_flows.id')
                    ->where('inspection_jobs.id', $inspection_job->id)
                    ->where('inspection_steps.transfer_type', $step_form_new->transfer_type)
                    ->select(
                        'inspection_steps.id as inspection_step_id',
                        'inspection_steps.inspection_form_id',
                        'inspection_steps.transfer_type',
                        'inspection_steps.transfer_reason',
                        'inspection_steps.inspection_department_id',
                        'inspection_forms.form_type',
                        'inspection_steps.is_need_images',
                        'inspection_steps.is_need_inspector_sign',
                        'inspection_steps.is_need_send_mobile',
                        'inspection_steps.inspection_role_id',
                        'inspection_steps.is_need_dpf',
                    )
                    ->get();

                if ($step_form_detail[0]->inspection_step_id != null) {
                    InspectionTrait::saveInspectionJobStep($step_form_detail, $inspection_job->id, $this->inspection_flow);
                }
            }
        }
    }

    function validate()
    {
        if (empty($this->worksheet_no)) {
            throw new Exception('empty worksheet_no');
        }
        if (empty($this->inspection_flow)) {
            throw new Exception('inspection_flow not found');
        }
    }
}
