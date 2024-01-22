<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use App\Models\DrivingSkill;
use App\Models\DrivingSkillServiceType;
use Illuminate\Http\Request;
use App\Models\ServiceType;
use App\Models\RentalCategory;
use App\Models\RentalCategoryServiceTypes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DrivingSkillController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::DrivingSkill);
        $s = $request->s;
        $service_type_list = DB::table('driving_skills_service_types')
        ->leftJoin('service_types', 'service_types.id', '=', 'driving_skills_service_types.service_type_id')
        ->select('driving_skills_service_types.driving_skill_id',DB::raw("group_concat(service_types.name SEPARATOR ', ') as service_type_name"))
        ->groupBy('driving_skills_service_types.driving_skill_id');

        $list = DrivingSkill::select('driving_skills.name','driving_skills.id','service_type_list.service_type_name')
        ->leftJoin('driving_skills_service_types as driving_service', 'driving_service.driving_skill_id', '=', 'driving_skills.id')
        ->leftJoin('service_types as st2', 'st2.id', '=', 'driving_service.service_type_id')
        ->leftjoinSub($service_type_list, 'service_type_list', function ($join) {
            $join->on('service_type_list.driving_skill_id', '=', 'driving_skills.id');
        })
        ->when($s, function ($query) use ($s) {
            $query->where('st2.name', 'like', '%' . $s . '%');
            $query->orWhere('driving_skills.name', 'like', '%' . $s . '%');
        }) 
        ->sortable('name')
        ->groupBy('driving_skills.name',
                'driving_skills.id',
                'service_type_list.service_type_name')
        ->paginate(PER_PAGE);
        
        return view('admin.driving-skills.index', [
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
        $this->authorize(Actions::Manage . '_' . Resources::DrivingSkill);
        $d = new RentalCategory();
        $service_type_list = ServiceType::all();
        $service_type = [];
        $page_title = __('lang.create') . __('driving_skills.page_title');
        return view('admin.driving-skills.form', [ 
        'd' => $d,
        'page_title' => $page_title,
        'service_type_list' => $service_type_list,
        'service_type' => $service_type
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DrivingSkill);
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255',
            Rule::unique('driving_skills', 'name')->whereNull('deleted_at')->ignore($request->id),
        ],
            'service_type' => ['required'],
        ], [], [
            'name' => __('driving_skills.name'),
            'service_type' => __('driving_skills.service_category')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $driving_skills = DrivingSkill::firstOrNew(['id' => $request->id]);
        $driving_skills->name = $request->name;
        $driving_skills->save();
       
        if ($driving_skills->id) {
            $driving_skill_relation = $this->saveDrivingSkillRelation($request, $driving_skills->id);
        }

        $redirect_route = route('admin.driving-skills.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveDrivingSkillRelation($request, $driving_skill_id)
    {
        DrivingSkillServiceType::where('driving_skill_id', $driving_skill_id)->delete();
        if (!empty($request->service_type)) {
            foreach ($request->service_type as $service_type) {
                $driving_skill_relation = new DrivingSkillServiceType();
                $driving_skill_relation->driving_skill_id = $driving_skill_id;
                $driving_skill_relation->service_type_id = $service_type;
                $driving_skill_relation->save();
            }
        }
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DrivingSkill $driving_skill)
    {
        $this->authorize(Actions::View . '_' . Resources::DrivingSkill);
        $service_type = $this->getServiceTypeArray($driving_skill->id);
        $service_type_list = ServiceType::all();
        $page_title = __('lang.view') . __('driving_skills.page_title');
        $view = true;
        return view('admin.driving-skills.form', [
            'd' => $driving_skill,
            'page_title' => $page_title,
            'view' => $view,
            'service_type_list' => $service_type_list,
            'service_type' => $service_type,
        ]);
    }

    public function getServiceTypeArray($driving_skill)
    {
        return DrivingSkillServiceType::leftJoin('service_types', 'service_types.id', '=', 'driving_skills_service_types.service_type_id')
        ->select('service_types.id as id','service_types.name as name')
        ->where('driving_skills_service_types.driving_skill_id', $driving_skill)
        ->pluck('driving_skills.id')
        ->toArray();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(DrivingSkill $driving_skill)
    {
        $this->authorize(Actions::Manage . '_' . Resources::DrivingSkill);
        $service_type = $this->getServiceTypeArray($driving_skill->id);
        $service_type_list = ServiceType::all();
        $page_title = __('lang.edit') . __('driving_skills.page_title');
        return view('admin.driving-skills.form', [
            'd' => $driving_skill,
            'page_title' => $page_title,
            'service_type_list' => $service_type_list,
            'service_type' => $service_type,
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
        $this->authorize(Actions::Manage . '_' . Resources::DrivingSkill);
        $driving_skill = DrivingSkill::find($id);
        $driving_skill->delete();

        return $this->responseComplete();
    }
}
