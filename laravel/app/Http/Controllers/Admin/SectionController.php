<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Department;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Section);
        $list = Section::Select('sections.*', 'departments.name as department_name')
            ->leftJoin('departments', 'departments.id', '=', 'sections.department_id')
            ->sortable('name')
            ->search($request->s, $request)
            ->paginate(PER_PAGE);
        $department_lists = Department::select('name', 'id')->where('status', STATUS_ACTIVE)->get();
        return view('admin.sections.index', [
            'list' => $list,
            's' => $request->s,
            'department_id' => $request->department_id,
            'department_lists' => $department_lists
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Section);
        $d = new Section();
        $user_department_lists = Department::all();
        $page_title = __('lang.create') . __('sections.page_title');
        return view('admin.sections.form', compact('d', 'page_title', 'user_department_lists'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('sections', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
        ], [], [
            'name' => __('sections.name')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $section = Section::firstOrNew(['id' => $request->id]);
        $section->name = $request->name;
        $section->status = STATUS_ACTIVE;
        $section->save();

        $redirect_route = route('admin.sections.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Section $section)
    {
        $this->authorize(Actions::View . '_' . Resources::Section);
        $user_department_lists = Department::all();
        $page_title = __('lang.view') . __('sections.page_title');
        $view = true;
        return view('admin.sections.form', [
            'd' => $section,
            'view' => $view,
            'page_title' => $page_title,
            'user_department_lists' => $user_department_lists
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Section $section)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Section);
        $user_department_lists = Department::all();
        $page_title = __('lang.edit') . __('sections.page_title');
        return view('admin.sections.form', [
            'd' => $section,
            'page_title' => $page_title,
            'user_department_lists' => $user_department_lists
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
        $this->authorize(Actions::Manage . '_' . Resources::Section);
        $section = Section::find($id);
        $section->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
