<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CompensationStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\Accident;
use App\Models\Compensation;
use App\Traits\CompensationTrait;
use Illuminate\Http\Request;

class LossOfBenefitApproveController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LossOfBenefit);
        $compensation_id = $request->compensation_id;
        $worksheet_no = null;
        if ($compensation_id) {
            $compensation = Compensation::find($compensation_id);
            $worksheet_no =  $compensation ? $compensation->workshhet_no : null;
        }

        $accident_id = $request->accident_id;
        $accident_worksheet_no = null;
        if ($accident_id) {
            $accident = Accident::find($accident_id);
            $accident_worksheet_no =  $accident ? $accident->workshhet_no : null;
        }
        $accident_date = $request->accident_date;
        $end_date = $request->end_date;
        $complaint_type = $request->complaint_type;
        $status = $request->status;
        $list = Compensation::search($request->s, $request)
        ->where('status', CompensationStatusEnum::PENDING_APPROVE)
        ->paginate(PER_PAGE);

        $complaint_type_list = CompensationTrait::getComplaintTypeList();
        $status_list = CompensationTrait::getStatusList();
        return view('admin.loss-of-benefit-approves.index', [
            'list' => $list,
            's' => $request->s,
            'complaint_type_list' => $complaint_type_list,
            'status_list' => $status_list, 
            'compensation_id' => $compensation_id,
            'worksheet_no' => $worksheet_no,
            'accident_id' => $accident_id,
            'accident_worksheet_no' => $accident_worksheet_no,
            'accident_date' => $accident_date,
            'end_date' => $end_date,
            'complaint_type' => $complaint_type,
            'status' => $status,
        ]);
    } 
}
