<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\CarPartTypeEnum;
use App\Enums\Resources;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchLocation;
use DateTime;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\InspectionForm;
use App\Models\InspectionFormChecklist;
use App\Models\InspectionFormQuestion;
use App\Models\InspectionFormSection;


class InspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ConfigCarInspection);
        $list = InspectionForm::select('inspection_forms.*')
            ->sortable('name')
            ->search($request->s)
            ->paginate(PER_PAGE);
        return view('admin.car-inspections.index', [
            'list' => $list,
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
        $this->authorize(Actions::Manage . '_' . Resources::ConfigCarInspection);
        $d = new InspectionForm();
        $page_title = __('car_inspections.page_title');

        return view('admin.car-inspections.form', [
            'd' => $d,
            'page_title' => $page_title,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConfigCarInspection);
        $validator = Validator::make($request->all(), [
            'inspect_name' => [
                'required',
                'max:255',
                // Rule::unique('inspection_forms', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'data.*.seq' => [
                'required',
                'integer',
                'distinct'
            ],
            'data.*.name' => [
                'required',
                'max:255',
            ],
            'data.*.subseq.*.seq2' => [
                'required',
                'integer'
            ],
            'data.*.subseq.*.name2' => [
                'required',
                'max:255',
            ],
            'data2.*.seq' => [
                'required',
                'integer',
                'distinct'
            ],
            'data2.*.name' => [
                'required',
                'max:255',
            ],

        ], [], [
            'inspect_name' => __('car_inspections.name'),
            'data.*.seq' => __('car_inspections.seq'),
            'data.*.name' => __('car_inspections.section_topic'),
            'data.*.subseq.*.seq2' => __('car_inspections.seq_question'),
            'data.*.subseq.*.name2' => __('car_inspections.list_inspection'),
            'data2.*.seq' => __('car_inspections.seq_question'),
            'data2.*.name' => __('car_inspections.list_question'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        if (!empty($request->data)) {
            foreach ($request->data as $data) {
                if (!empty($data['subseq'])) {
                    $arr = [];
                    foreach ($data['subseq'] as $index => $data2) {
                        array_push($arr, $data2['seq2']);
                    }
                    if (count($arr) != count(array_unique($arr))) {
                        return $this->responseWithCode(false, 'ลำดับคำถาม ต้องไม่ซ้ำกัน', null, 422);
                    }
                }
            }
        }

        $car_inspection = InspectionForm::firstOrNew(['id' => $request->id]);
        $car_inspection->name = $request->inspect_name;
        $car_inspection->status = STATUS_ACTIVE;
        $car_inspection->save();

        if ($car_inspection->id) {
            $this->saveSection($request, $car_inspection->id);
            $this->saveQuestion($request, $car_inspection->id);
        }

        $redirect_route = route('admin.car-inspections.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveSection($request, $car_inspection_id)
    {
        // dd($request->data);
        if ($request->del_section != null) {
            InspectionFormSection::whereIn('id', $request->del_section)->delete();
        }
        if (!empty($request->data)) {
            foreach ($request->data as $data) {
                if ($data['id'] != null) {
                    $car_form_section = InspectionFormSection::firstOrNew(['id' => $data['id']]);
                } else {
                    $car_form_section = new InspectionFormSection();
                }
                $car_form_section->name = $data['name'];
                $car_form_section->seq = $data['seq'];
                $car_form_section->inspection_form_id = $car_inspection_id;
                if ($data['status_section'] === 'true') {
                    $car_form_section->status = STATUS_ACTIVE;
                } else {
                    $car_form_section->status = STATUS_INACTIVE;
                }
                $car_form_section->save();
                if ($request->del_checklist != null) {
                    InspectionFormChecklist::whereIn('id', $request->del_checklist)->delete();
                }
                if (isset($data['subseq'])) {
                    foreach ($data['subseq'] as $index => $data) {
                        if ($data['id'] != null) {
                            $car_form_list = InspectionFormChecklist::firstOrNew(['id' => $data['id']]);
                        } else {
                            $car_form_list = new InspectionFormChecklist();
                        }
                        $car_form_list->name = $data['name2'];
                        $car_form_list->seq = $data['seq2'];
                        $car_form_list->car_part = $data['car_part'];

                        if ($data['status_list'] === 'true') {
                            $car_form_list->status = STATUS_ACTIVE;
                        } else {
                            $car_form_list->status = STATUS_INACTIVE;
                        }
                        $car_form_list->inspection_form_section_id = $car_form_section->id;
                        $car_form_list->save();
                    }
                }
            }
        }

        return true;
    }

    private function saveQuestion($request, $car_inspection_id)
    {
        InspectionFormQuestion::where('inspection_form_id', $car_inspection_id)->delete();
        if (!empty($request->data2)) {
            foreach ($request->data2 as $data) {
                $car_form_question = new InspectionFormQuestion();
                $car_form_question->name = $data['name'];
                $car_form_question->seq = $data['seq'];
                $car_form_question->inspection_form_id = $car_inspection_id;
                if ($data['status_question'] === 'true') {
                    $car_form_question->status = STATUS_ACTIVE;
                } else {
                    $car_form_question->status = STATUS_INACTIVE;
                }
                $car_form_question->save();
            }
        }
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show(InspectionForm $car_inspection)
    {
        $this->authorize(Actions::View . '_' . Resources::ConfigCarInspection);
        $list = InspectionFormSection::where('inspection_form_id', $car_inspection->id)
            ->where('seq', '!=', 9999)->orderBy('seq', 'asc')->get();
        $list2 = $list->pluck('id')->toArray();
        $checklist = InspectionFormChecklist::whereIn('inspection_form_section_id', $list2)->orderBy('seq', 'asc')->get();
        $checklist->map(function ($item) {
            $item->status_list = $item->status == STATUS_INACTIVE ? false : true;
            $item->seq2 = $item->seq;
            $item->name2 = $item->name;
            return $item;
        });
        $list->map(function ($item) use ($checklist) {
            $checklist2 = $checklist->where('inspection_form_section_id', $item->id)->values();
            $item->subseq = $checklist2;
            $item->status_section = $item->status == STATUS_INACTIVE ? false : true;
            return $item;
        });

        $question_list = InspectionFormQuestion::where('inspection_form_id', $car_inspection->id)->orderBy('seq', 'asc')->get();
        $question_list->map(function ($item) {
            $item->name = $item->name;
            $item->seq = $item->seq;
            $item->status_question = $item->status == STATUS_INACTIVE ? false : true;
            return $item;
        });
        $page_title = __('car_inspections.page_title');

        return view('admin.car-inspections.form', [
            'list' => $list,
            'view' => true,
            'question_list' => $question_list,
            'page_title' => $page_title,
            'd' => $car_inspection
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(InspectionForm $car_inspection)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConfigCarInspection);
        $list = InspectionFormSection::where('inspection_form_id', $car_inspection->id)
            ->where('seq', '!=', 9999)
            ->orderBy('seq', 'asc')->get();
        $list2 = $list->pluck('id')->toArray();
        $checklist = InspectionFormChecklist::whereIn('inspection_form_section_id', $list2)->orderBy('seq', 'asc')->get();
        $checklist->map(function ($item) {
            $item->status_list = $item->status == STATUS_INACTIVE ? false : true;
            $item->seq2 = $item->seq;
            $item->name2 = $item->name;
            $item->checklist_id = $item->id;
            return $item;
        });
        $list->map(function ($item) use ($checklist) {
            $checklist2 = $checklist->where('inspection_form_section_id', $item->id)->values();
            $item->subseq = $checklist2;
            $item->status_section = $item->status == STATUS_INACTIVE ? false : true;
            $item->section_id = $item->id;
            return $item;
        });
        $question_list = InspectionFormQuestion::where('inspection_form_id', $car_inspection->id)->orderBy('seq', 'asc')->get();
        $question_list->map(function ($item) {
            $item->name = $item->name;
            $item->seq = $item->seq;
            $item->status_question = $item->status == STATUS_INACTIVE ? false : true;
            return $item;
        });
        $page_title = __('car_inspections.page_title');
        $car_part_type_list = $this->getCarPartTypeList();
        return view('admin.car-inspections.form', [
            'list' => $list,
            'question_list' => $question_list,
            'page_title' => $page_title,
            'd' => $car_inspection,
            'car_part_type_list' => $car_part_type_list
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConfigCarInspection);
        $inspection_form = InspectionForm::find($id);
        $inspection_form->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    public function copyForm(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConfigCarInspection);
        $id = $request->id;
        $car_inspection_old = InspectionForm::find($id);
        $car_inspection_new = new InspectionForm();
        $car_inspection_new->name = $car_inspection_old->name . " (คัดลอก)";
        $car_inspection_new->status = STATUS_ACTIVE;
        $car_inspection_new->save();
        if ($car_inspection_new->id) {
            $this->saveSectionCopy($id, $car_inspection_new->id);
            $this->saveQuestionCopy($id, $car_inspection_new->id);
        }
        $redirect_route = route('admin.car-inspections.index');
        return redirect()->back();
    }

    private function saveSectionCopy($car_inspection_id, $car_inspection_new)
    {
        $car_inspection_section_old = InspectionFormSection::where('inspection_form_id', $car_inspection_id)->get();
        if (!empty($car_inspection_section_old)) {
            foreach ($car_inspection_section_old as $data) {
                $car_form_section = new InspectionFormSection();
                $car_form_section->name = $data->name;
                $car_form_section->seq = $data->seq;
                $car_form_section->inspection_form_id = $car_inspection_new;
                $car_form_section->status = $data->status;
                $car_form_section->save();
                if (isset($car_form_section)) {
                    $car_inspection_checklist_old = InspectionFormChecklist::where('inspection_form_section_id', $data->id)->get();
                    foreach ($car_inspection_checklist_old as $index => $data2) {
                        $car_form_list = new InspectionFormChecklist();
                        $car_form_list->name = $data2->name;
                        $car_form_list->seq = $data2->seq;
                        $car_form_list->status = $data2->status;
                        $car_form_list->inspection_form_section_id = $car_form_section->id;
                        $car_form_list->save();
                    }
                }
            }
        }

        return true;
    }

    private function saveQuestionCopy($car_inspection_id, $car_inspection_new)
    {
        $car_inspection_question_old = InspectionFormQuestion::where('inspection_form_id', $car_inspection_id)->get();
        if (!empty($car_inspection_question_old)) {
            foreach ($car_inspection_question_old as $data) {
                $car_form_question = new InspectionFormQuestion();
                $car_form_question->name = $data->name;
                $car_form_question->seq = $data->seq;
                $car_form_question->inspection_form_id = $car_inspection_new;
                $car_form_question->status = $data->status;
                $car_form_question->save();
            }
        }
        return true;
    }

    public function getCarPartTypeList()
    {
        $car_parts = [
            CarPartTypeEnum::GEAR,
            CarPartTypeEnum::DRIVE_SYSTEM,
            CarPartTypeEnum::CAR_SEAT,
            CarPartTypeEnum::SIDE_MIRROR,
            CarPartTypeEnum::AIR_BAG,
            CarPartTypeEnum::CENTRAL_LOCK,
            CarPartTypeEnum::FRONT_BRAKE,
            CarPartTypeEnum::REAR_BRAKE,
            CarPartTypeEnum::ABS,
            CarPartTypeEnum::ANTI_THIFT_SYSTEM,
            CarPartTypeEnum::OTHER,
            CarPartTypeEnum::BATTERY,
            CarPartTypeEnum::TIRE,
            CarPartTypeEnum::WIPER,
        ];

        return collect($car_parts)->map(function ($item) {
            return (object)[
                'id' => $item,
                'name' => __('car_part_types.name_' . $item),
                'value' => $item,
                'text' => __('car_part_types.name_' . $item),
            ];
        });
    }
}
