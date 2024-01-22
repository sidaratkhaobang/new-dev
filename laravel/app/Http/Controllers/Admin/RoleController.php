<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Role;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Models\Department;
use App\Models\Section;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Role);
        $list = Role::select('roles.*')
            ->addSelect('departments.name as department_name', 'sections.name as section_name')
            ->leftJoin('departments', 'departments.id', 'roles.department_id')
            ->leftJoin('sections', 'sections.id', 'roles.section_id')
            ->sortable('name')
            ->search($request->s)
            ->paginate(PER_PAGE);
        return view('admin.roles.index', [
            'list' => $list,
            's' => $request->s
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Role);
        $d = new Role();
        $department_lists = Department::select('name', 'id')->where('status', STATUS_ACTIVE)->get();
        $section_name = null;
        $page_title = __('lang.create') . __('roles.page_title');
        return view('admin.roles.form', [
            'd' => $d,
            'page_title' => $page_title,
            'department_lists' => $department_lists,
            'section_name' => $section_name,
        ]);
    }

    public function edit(Role $role)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Role);
        $department_lists = Department::select('name', 'id')->where('status', STATUS_ACTIVE)->get();
        $section_name = find_name_by_id($role->section_id, Section::class);
        $page_title = __('lang.edit') . __('roles.page_title');
        return view('admin.roles.form', [
            'd' => $role,
            'page_title' => $page_title,
            'department_lists' => $department_lists,
            'section_name' => $section_name,
        ]);
    }

    public function show(Role $role)
    {
        $this->authorize(Actions::View . '_' . Resources::Role);
        $department_lists = Department::select('name', 'id')->where('status', STATUS_ACTIVE)->get();
        $section_name = find_name_by_id($role->section_id, Section::class);
        $page_title = __('lang.view') . __('roles.page_title');
        return view('admin.roles.form', [
            'd' => $role,
            'page_title' => $page_title,
            'department_lists' => $department_lists,
            'section_name' => $section_name,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('roles', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'department_id' => [
                'required'
            ],
        ], [], [
            'name' => __('roles.name'),
            'department_id' => __('users.department'),
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $role = Role::firstOrNew(['id' => $request->id]);
        $role->name = $request->name;
        $role->department_id = $request->department_id;
        $role->section_id = $request->section_id;
        $role->description = $request->description;
        $role->status = STATUS_ACTIVE;
        $role->save();

        $redirect_route = route('admin.roles.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Role);
        $role = Role::find($id);
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
