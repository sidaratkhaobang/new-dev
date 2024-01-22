<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Department;
use App\Models\Section;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Enums\Actions;
use App\Enums\Resources;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::User);
        $list = User::leftJoin('departments', 'departments.id', '=', 'users.department_id')
            ->leftJoin('sections', 'sections.id', '=', 'users.section_id')
            ->leftJoin('branches', 'branches.id', '=', 'users.branch_id')
            ->select('users.*', 'departments.name as department_name', 'sections.name as section_name', 'branches.name as branch_name')
            ->sortable('username')
            ->search($request->s, $request)->paginate(PER_PAGE);
        $department_lists = Department::select('name', 'id')->where('status', STATUS_ACTIVE)->get();
        $section_lists = Section::select('name', 'id')->where('status', STATUS_ACTIVE)->get();
        $branch_lists = Branch::select('name', 'id')->where('status', STATUS_ACTIVE)->get();
        return view('admin.users.index', [
            'list' => $list,
            's' => $request->s,
            'department_lists' => $department_lists,
            'section_lists' => $section_lists,
            'branch_lists' => $branch_lists,
            'department_id' => $request->department_id,
            'section_id' => $request->section_id,
            'branch_id' => $request->branch_id,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::User);
        $d = new User();
        $user_department_lists = Department::all();
        $role_list = Role::all();
        $branch_list = Branch::all();
        $department_name = null;
        $section_name = null;
        $role_name = null;
        $page_title = __('lang.create') . __('users.page_title');
        return view('admin.users.form', [
            'd' => $d,
            'page_title' => $page_title,
            'user_department_lists' => $user_department_lists,
            'role_list' => $role_list,
            'branch_list' => $branch_list,
            'department_name' => $department_name,
            'section_name' => $section_name,
            'role_name' => $role_name,
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
        if ($request->id == null) {
            $validator = Validator::make($request->all(), [
                'username' => [
                    'required', 'string',
                    Rule::unique('users', 'username')->whereNull('deleted_at')->ignore($request->id),
                ],
                'password' => [
                    'required', 'string', 'min:8',
                    // Rule::unique('car_types', 'name')->ignore($request->id),
                ],
                'name' => [
                    'required', 'string', 'max:100',
                ],
                'email' => [
                    'required', 'string', 'max:255', 'email',
                    Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($request->id),
                ],
                'branch_id' => [
                    'required'
                ],
                'department_id' => [
                    'required'
                ],
                'role_id' => [
                    'required'
                ]
            ], [], [
                'username' => __('users.username'),
                'password' => __('users.password'),
                'name' => __('users.name'),
                'email' => __('users.email'),
                'branch_id' => __('users.branch'),
                'department_id' => __('users.department'),
                'role_id' => __('users.role'),
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        } else if ($request->id != null) {
            if ($request->password != null) {
                $validator2 = Validator::make($request->all(), [
                    'username' => [
                        'required', 'string', 'max:255',
                        Rule::unique('users', 'username')->whereNull('deleted_at')->ignore($request->id),
                    ],
                    'password' => [
                        'required', 'string', 'min:8',
                        // Rule::unique('car_types', 'name')->ignore($request->id),
                    ],
                    'name' => [
                        'required', 'string', 'max:255',
                    ],
                    'email' => [
                        'required', 'string', 'max:255', 'email',
                        Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($request->id),
                    ],
                    'branch_id' => [
                        'required'
                    ],
                    'department_id' => [
                        'required'
                    ],
                    'role_id' => [
                        'required'
                    ]
                ], [], [
                    'username' => __('users.username'),
                    'password' => __('users.password'),
                    'name' => __('users.name'),
                    'email' => __('users.email'),
                    'branch_id' => __('users.branch'),
                    'department_id' => __('users.department'),
                    'role_id' => __('users.role'),
                ]);
                if ($validator2->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator2);
                }
            } else {
                $validator3 = Validator::make($request->all(), [
                    'username' => [
                        'required', 'string', 'max:255',
                        Rule::unique('users', 'username')->whereNull('deleted_at')->ignore($request->id),
                    ],
                    'name' => [
                        'required', 'string', 'max:255',
                    ],
                    'email' => [
                        'required', 'string', 'max:255', 'email',
                        Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($request->id),
                    ],
                    'branch_id' => [
                        'required'
                    ],
                    'department_id' => [
                        'required'
                    ],
                    'role_id' => [
                        'required'
                    ]
                ], [], [
                    'username' => __('users.username'),
                    'password' => __('users.password'),
                    'name' => __('users.name'),
                    'email' => __('users.email'),
                    'branch_id' => __('users.branch'),
                    'department_id' => __('users.department'),
                    'role_id' => __('users.role'),
                ]);
                if ($validator3->stopOnFirstFailure()->fails()) {
                    return $this->responseValidateFailed($validator3);
                }
            }
        }

        $user = User::firstOrNew(['id' => $request->id]);
        $user->username = $request->username;
        if ($request->id != null && $request->password == null) {
            //
        } else {
            $user->password =  Hash::make($request->password);
        }
        $user->name = $request->name;
        $user->email = $request->email;
        $user->tel = $request->tel;
        $user->user_department_id = null;
        $user->department_id = $request->department_id;
        $user->section_id = $request->section_id;
        $user->role_id = $request->role_id;
        $user->branch_id = $request->branch_id;
        $user->status = STATUS_ACTIVE;
        $user->save();

        $redirect_route = route('admin.users.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize(Actions::View . '_' . Resources::User);
        $page_title = __('lang.view') . __('users.page_title');
        $user_department_lists = Department::all();
        $role_list = Role::all();
        $branch_list = Branch::all();
        $department_name = find_name_by_id($user->department_id, Department::class);
        $section_name = find_name_by_id($user->section_id, Section::class);
        $role_name = find_name_by_id($user->role_id, Role::class);
        $view = true;
        return view('admin.users.form', [
            'd' => $user,
            'view' => $view,
            'page_title' => $page_title,
            'user_department_lists' => $user_department_lists,
            'role_list' => $role_list,
            'branch_list' => $branch_list,
            'department_name' => $department_name,
            'section_name' => $section_name,
            'role_name' => $role_name,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $this->authorize(Actions::Manage . '_' . Resources::User);
        $page_title = __('lang.edit') . __('users.page_title');
        $user_department_lists = Department::all();
        $role_list = Role::all();
        $branch_list = Branch::all();
        $department_name = find_name_by_id($user->department_id, Department::class);
        $section_name = find_name_by_id($user->section_id, Section::class);
        $role_name = find_name_by_id($user->role_id, Role::class);
        return view('admin.users.form', [
            'd' => $user,
            'page_title' => $page_title,
            'user_department_lists' => $user_department_lists,
            'role_list' => $role_list,
            'branch_list' => $branch_list,
            'department_name' => $department_name,
            'section_name' => $section_name,
            'role_name' => $role_name,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::User);
        $user = User::find($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
