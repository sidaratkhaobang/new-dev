<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Http\Controllers\Controller;
use App\Models\InspectionFlow;
use App\Models\InspectionForm;
use App\Models\InspectionStep;
use Illuminate\Http\Request;
use App\Models\Province;
use App\Enums\InspectionTypeEnum;
use App\Enums\Resources;
use App\Enums\TransferReasonEnum;
use App\Enums\TransferTypeEnum;
use App\Models\Role;
use App\Models\Department;
use App\Models\Section;
use Illuminate\Support\Facades\Validator;

class InspectionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ConfigInspectionFlow);
        $inspection_form = $request->inspection_form;
        $inspection_form_list = InspectionFlow::select('id', 'name')->get();
        $list = InspectionFlow::sortable('name')
            ->where(function ($q) use ($inspection_form) {
                if (!is_null($inspection_form)) {
                    $q->where('inspection_flows.id', $inspection_form);
                }
            })
            ->search($request->s)
            ->paginate(PER_PAGE);

        $list2 = $list->pluck('id')->toArray();
        $checklist = InspectionStep::leftjoin('inspection_forms', 'inspection_forms.id', '=', 'inspection_steps.inspection_form_id')
            ->whereIn('inspection_steps.inspection_flow_id', $list2)
            ->orderBy('seq', 'asc')->get();
        $checklist->map(function ($item) {
            $item->condition  = $item->transfer_reason;
            $item->in_form  = $item->name;
            $item->photo  = $item->is_need_images;
            $item->inspector_signature  = $item->is_need_inspector_sign;
            $item->transfer_type  = $item->transfer_type;

            return $item;
        });
        $list->map(function ($item) use ($checklist) {
            $checklist2 = $checklist->where('inspection_flow_id', $item->id)->values();
            $item->subseq  = $checklist2;
            return $item;
        });

        $listSub = InspectionStep::orderBy('seq', 'asc')
            ->select('seq', 'transfer_reason as condition', 'inspection_form_id as in_form', 'is_need_images as photo', 'is_need_inspector_sign as inspector_signature', 'transfer_type')
            ->get();
        return view('admin.car-inspection-types.index', [
            'listSub' => $listSub,
            'list' => $list,
            'inspection_form_list' => $inspection_form_list,
            'inspection_form' => $inspection_form,
            's' => $request->s,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConfigInspectionFlow);
        $validator = Validator::make($request->all(), [
            'status' => [
                'required',
            ],
            'status_in' => [
                'required',
            ],
            'data2.*.seq' => [
                'required', 'integer', 'distinct'
            ],
            'data2.*.condition' => [
                'required', 'max:255',
            ],
            'data2.*.department' => [
                'required', 'max:255',
            ],
            // 'data2.*.role' => [
            //     'required', 'max:255',
            // ],
            'data2.*.in_form' => [
                'required',
            ],
            'data3.*.seq' => [
                'required', 'integer', 'distinct'
            ],
            'data3.*.condition' => [
                'required', 'max:255',
            ],
            'data3.*.department' => [
                'required', 'max:255',
            ],
            // 'data3.*.role' => [
            //     'required', 'max:255',
            // ],
            'data3.*.in_form' => [
                'required',
            ],

        ], [], [
            'status' => __('car_inspection_types.customer_signature_out'),
            'status_in' => __('car_inspection_types.customer_signature_in'),
            'data2.*.seq' => __('car_inspection_types.seq_inspection'),
            'data2.*.condition' => __('car_inspection_types.condition'),
            'data2.*.department' => __('car_inspection_types.responsible_department'),
            // 'data2.*.role' => __('car_inspection_types.role'),
            'data2.*.in_form' => __('car_inspection_types.use_form'),
            'data3.*.seq' => __('car_inspection_types.seq_inspection'),
            'data3.*.condition' => __('car_inspection_types.condition'),
            'data3.*.department' => __('car_inspection_types.responsible_department'),
            // 'data3.*.role' => __('car_inspection_types.role'),
            'data3.*.in_form' => __('car_inspection_types.use_form'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $inspection_flow = InspectionFlow::firstOrNew(['id' => $request->id]);
        $inspection_flow->is_need_customer_sign_in = $request->status_in;
        $inspection_flow->is_need_customer_sign_out = $request->status;
        $inspection_flow->save();

        //old delete
        // InspectionStep::where('inspection_flow_id', $request->id)->delete();
        if ($request->del_section != null) {
            InspectionStep::whereIn('id', $request->del_section)->delete();
        }
        if (!empty($request->data2)) {
            foreach ($request->data2 as $data) {
                $car_form_question = InspectionStep::firstOrNew(['id' => $data['id']]);
                $car_form_question->seq = $data['seq'];
                $car_form_question->transfer_reason = $data['condition'];
                $car_form_question->inspection_form_id = $data['in_form'];
                $car_form_question->inspection_department_id = $data['department'];
                $car_form_question->inspection_section_id = $data['section'];
                $car_form_question->transfer_type = $data['transfer_type'];
                $car_form_question->inspection_flow_id = $request->id;
                // $car_form_question->inspection_role_id = $data['role'];
                if ((filter_var($data['photo'], FILTER_VALIDATE_BOOLEAN)) === true) {
                    $car_form_question->is_need_images = STATUS_ACTIVE;
                } else {
                    $car_form_question->is_need_images = STATUS_DEFAULT;
                }
                if ((filter_var($data['inspector_signature'], FILTER_VALIDATE_BOOLEAN)) === true) {
                    $car_form_question->is_need_inspector_sign = STATUS_ACTIVE;
                } else {
                    $car_form_question->is_need_inspector_sign = STATUS_DEFAULT;
                }
                if ((filter_var($data['send_mobile'], FILTER_VALIDATE_BOOLEAN)) === true) {
                    $car_form_question->is_need_send_mobile = STATUS_ACTIVE;
                } else {
                    $car_form_question->is_need_send_mobile = STATUS_DEFAULT;
                }
                if ((filter_var($data['dpf_oil'], FILTER_VALIDATE_BOOLEAN)) === true) {
                    $car_form_question->is_need_dpf = STATUS_ACTIVE;
                } else {
                    $car_form_question->is_need_dpf = STATUS_DEFAULT;
                }
                $car_form_question->save();
            }
        }
        if (!empty($request->data3)) {
            foreach ($request->data3 as $data) {
                $car_form_question = InspectionStep::firstOrNew(['id' => $data['id']]);
                $car_form_question->seq = $data['seq'];
                $car_form_question->transfer_reason = $data['condition'];
                $car_form_question->inspection_form_id = $data['in_form'];
                $car_form_question->inspection_department_id = $data['department'];
                $car_form_question->inspection_section_id = $data['section'];
                $car_form_question->transfer_type = $data['transfer_type'];
                $car_form_question->inspection_flow_id = $request->id;
                // $car_form_question->inspection_role_id = $data['role'];
                if ((filter_var($data['photo'], FILTER_VALIDATE_BOOLEAN)) === true) {
                    $car_form_question->is_need_images = STATUS_ACTIVE;
                } else {
                    $car_form_question->is_need_images = STATUS_DEFAULT;
                }
                if ((filter_var($data['inspector_signature'], FILTER_VALIDATE_BOOLEAN)) === true) {
                    $car_form_question->is_need_inspector_sign = STATUS_ACTIVE;
                } else {
                    $car_form_question->is_need_inspector_sign = STATUS_DEFAULT;
                }
                if ((filter_var($data['send_mobile'], FILTER_VALIDATE_BOOLEAN)) === true) {
                    $car_form_question->is_need_send_mobile = STATUS_ACTIVE;
                } else {
                    $car_form_question->is_need_send_mobile = STATUS_DEFAULT;
                }
                if ((filter_var($data['dpf_oil'], FILTER_VALIDATE_BOOLEAN)) === true) {
                    $car_form_question->is_need_dpf = STATUS_ACTIVE;
                } else {
                    $car_form_question->is_need_dpf = STATUS_DEFAULT;
                }
                $car_form_question->save();
            }
        }

        $redirect_route = route('admin.car-inspection-types.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(InspectionFlow $car_inspection_type)
    {
        $this->authorize(Actions::View . '_' . Resources::ConfigInspectionFlow);
        $list = InspectionStep::where('inspection_flow_id', $car_inspection_type->id)->orderBy('seq', 'asc')
            ->select('seq', 'transfer_reason as condition', 'inspection_form_id as in_form', 'inspection_department_id as department', 'is_need_images as photo', 'is_need_inspector_sign as inspector_signature', 'transfer_type', 'is_need_send_mobile as send_mobile', 'is_need_dpf as dpf_oil', 'inspection_role_id as role')
            ->where('transfer_type', TransferTypeEnum::OUT)->get();

        $list2 = InspectionStep::where('inspection_flow_id', $car_inspection_type->id)->orderBy('seq', 'asc')
            ->select('seq', 'transfer_reason as condition', 'inspection_form_id as in_form', 'inspection_department_id as department', 'is_need_images as photo', 'is_need_inspector_sign as inspector_signature', 'transfer_type', 'is_need_send_mobile as send_mobile', 'is_need_dpf as dpf_oil', 'inspection_role_id as role')
            ->where('transfer_type', TransferTypeEnum::IN)->get();
        //        dd($list2);
        $form_list = InspectionForm::select('id', 'name as text')->orderBy('name')->get();
        $province_list = Province::select('name_th as name', 'id')->orderBy('name_th')->get();
        $user_department_list = Department::select('name', 'id')->get();
        $section_list = Section::select('name', 'id')->get();
        $role_list = Role::select('name', 'id')->get();
        $page_title =  __('car_inspection_types.page_title');
        $listStatus = $this->getListStatus();
        $listCondition = $this->getListCondition();
        $listConditionOut = $this->getListConditionOut();

        return view('admin.car-inspection-types.form', [
            'view' => true,
            'list' => $list,
            'form_list' => $form_list,
            'question_list' => $list,
            'question_list2' => $list2,
            'page_title' => $page_title,
            'd' => $car_inspection_type,
            'listStatus' => $listStatus,
            'listCondition' => $listCondition,
            'listConditionOut' => $listConditionOut,
            'province_list' => $province_list,
            'userDepartmentList' => $user_department_list,
            'section_list' => $section_list,
            'role_list' => $role_list,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(InspectionFlow $car_inspection_type)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConfigInspectionFlow);
        $list = $this->getInspectionStep($car_inspection_type->id, TransferTypeEnum::OUT);
        $list2 = $this->getInspectionStep($car_inspection_type->id, TransferTypeEnum::IN);
        $form_list = InspectionForm::select('id', 'name as text')->orderBy('name')->get();
        // dd($form_list);
        $province_list = Province::select('name_th as name', 'id')->orderBy('name_th')->get();
        $user_department_list = Department::select('name', 'id')->get();
        $section_list = Section::select('name', 'id')->get();
        $role_list = Role::select('name', 'id')->get();
        $page_title =  __('car_inspection_types.page_title');
        $car_inspection = null;
        $listStatus = $this->getListStatus();
        $listCondition = $this->getListCondition();
        $listConditionOut = $this->getListConditionOut();

        return view('admin.car-inspection-types.form', [
            'list' => $list,
            'form_list' => $form_list,
            'question_list' => $list,
            'question_list2' => $list2,
            'page_title' => $page_title,
            'd' => $car_inspection_type,
            'listStatus' => $listStatus,
            'listCondition' => $listCondition,
            'listConditionOut' => $listConditionOut,
            'province_list' => $province_list,
            'userDepartmentList' => $user_department_list,
            'section_list' => $section_list,
            'role_list' => $role_list,
        ]);
    }

    function getInspectionStep($car_inspection_type_id, $transfer_type)
    {
        $list = InspectionStep::where('inspection_flow_id', $car_inspection_type_id)->orderBy('seq', 'asc')
            ->select('inspection_steps.id', 'seq', 'transfer_reason as condition', 'inspection_form_id as in_form', 'is_need_images as photo', 'is_need_inspector_sign as inspector_signature', 'transfer_type', 'is_need_send_mobile as send_mobile', 'is_need_dpf as dpf_oil')
            ->addSelect('inspection_department_id as department', 'departments.name as department_name')
            ->addSelect('inspection_section_id as section', 'sections.name as section_name')
            ->addSelect('inspection_role_id as role', 'roles.name as role_name')
            ->leftJoin('departments', 'departments.id', '=', 'inspection_department_id')
            ->leftJoin('sections', 'sections.id', '=', 'inspection_section_id')
            ->leftJoin('roles', 'roles.id', '=', 'inspection_role_id')
            ->where('inspection_steps.transfer_type', $transfer_type)
            ->get();
        return $list;
    }

    private function getListCondition()
    {
        return collect([
            [
                'id' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                'value' => TransferReasonEnum::RECEIVE_WAREHOUSE,
                'text' => __('car_inspection_types.status_condition_name_' . TransferReasonEnum::RECEIVE_WAREHOUSE),
            ],
            [
                'id' => TransferReasonEnum::RECEIVE_GARAGE,
                'value' => TransferReasonEnum::RECEIVE_GARAGE,
                'text' => __('car_inspection_types.status_condition_name_' . TransferReasonEnum::RECEIVE_GARAGE),
            ],
        ]);
    }

    private function getListConditionOut()
    {
        return collect([
            [
                'id' => TransferReasonEnum::DELIVER_CUSTOMER,
                'value' => TransferReasonEnum::DELIVER_CUSTOMER,
                'text' => __('car_inspection_types.status_condition_name_' . TransferReasonEnum::DELIVER_CUSTOMER),
            ],
            [
                'id' => TransferReasonEnum::DELIVER_GARAGE,
                'value' => TransferReasonEnum::DELIVER_GARAGE,
                'text' => __('car_inspection_types.status_condition_name_' . TransferReasonEnum::DELIVER_GARAGE),
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
