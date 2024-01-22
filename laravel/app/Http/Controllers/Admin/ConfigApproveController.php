<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\ConfigApproveTypeEnum;
use App\Models\ConfigApprove;
use App\Models\ConfigApproveLine;
use App\Models\Role;
use App\Models\Department;
use App\Models\User;
use App\Models\Branch;
use App\Models\Section;
use Illuminate\Validation\Rule;

class ConfigApproveController extends Controller
{
    function indexBranch(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ConfigApprove);
        $s = $request->s;
        $list = Branch::select('id', 'name')->where('status', STATUS_ACTIVE)->search($s)->paginate(PER_PAGE);
        return view('admin.config-approves.index-branch', [
            'list' => $list,
            's' => $s
        ]);
    }

    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ConfigApprove);
        $branch = Branch::findOrFail($request->branch_id);

        $list = ConfigApprove::all();
        return view('admin.config-approves.index', [
            'list' => $list,
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
        ]);
    }

    public function edit(ConfigApprove $config_approve, Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConfigApprove);
        $branch = Branch::findOrFail($request->branch_id);
        $config_approve_lines = ConfigApproveLine::select('config_approve_lines.*')
            ->addSelect('departments.name as department_name', 'sections.name as section_name', 'roles.name as role_name', 'users.name as user_name')
            ->leftJoin('departments', 'departments.id', '=', 'config_approve_lines.department_id')
            ->leftJoin('sections', 'sections.id', '=', 'config_approve_lines.section_id')
            ->leftJoin('roles', 'roles.id', '=', 'config_approve_lines.role_id')
            ->leftJoin('users', 'users.id', '=', 'config_approve_lines.user_id')
            ->where('config_approve_id', $config_approve->id)
            ->where('config_approve_lines.branch_id', $branch->id)
            ->orderBy('seq', 'asc')->get();

        $table_html = '';

        $config_approve_lines->map(function ($item) use (&$table_html) {
            $item->is_all_department  = boolval($item->is_all_department);
            $item->is_all_role  = boolval($item->is_all_role);
            $item->is_super_user  = boolval($item->is_super_user);

            $table_html .= view('admin.config-approves.components.row', [
                'line_id' => $item->id,
                'seq' => intval($item->seq),
                'department_id' => $item->department_id,
                'is_all_department' => $item->is_all_department,
                'department_name' => $item->department_name,
                'section_id' => $item->section_id,
                'is_all_section' => $item->is_all_section,
                'section_name' => $item->section_name,
                'role_id' => $item->role_id,
                'is_all_role' => $item->is_all_role,
                'role_name' => $item->role_name,
                'user_id' => $item->user_id,
                'user_name' => $item->user_name,
                'is_super_user' => $item->is_super_user,
            ])->render();

            return $item;
        });

        $config = ConfigApprove::select('id', 'type as name')->orderBy('name')->get();
        $user_department_list = Department::select('name', 'id')->get();
        $role_list = Role::select('name', 'id')->get();
        $user_list = User::select('name', 'id')->get();
        $page_title =   __('lang.manage') . __('config_approves.page_title') . ' > สาขา ' . $branch->name;
        return view('admin.config-approves.form', [
            'd' => $config_approve,
            'page_title' => $page_title,
            'user_department_list' => $user_department_list,
            'role_list' => $role_list,
            'user_list' => $user_list,
            'config_approve_lines' => $config_approve_lines,
            'config' => $config,
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
            'table_html' => $table_html
        ]);
    }

    public function show(ConfigApprove $config_approve, Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::ConfigApprove);
        $branch = Branch::findOrFail($request->branch_id);
        $config_approve_lines = ConfigApproveLine::select('config_approve_lines.*')
            ->addSelect('departments.name as department_name', 'sections.name as section_name', 'roles.name as role_name', 'users.name as user_name')
            ->leftJoin('departments', 'departments.id', '=', 'config_approve_lines.department_id')
            ->leftJoin('sections', 'sections.id', '=', 'config_approve_lines.section_id')
            ->leftJoin('roles', 'roles.id', '=', 'config_approve_lines.role_id')
            ->leftJoin('users', 'users.id', '=', 'config_approve_lines.user_id')
            ->where('config_approve_id', $config_approve->id)
            ->where('config_approve_lines.branch_id', $branch->id)
            ->orderBy('seq', 'asc')->get();

        $table_html = '';

        $config_approve_lines->map(function ($item) use (&$table_html) {
            $item->is_all_department  = boolval($item->is_all_department);
            $item->is_all_role  = boolval($item->is_all_role);
            $item->is_super_user  = boolval($item->is_super_user);

            $table_html .= view('admin.config-approves.components.row', [
                'line_id' => $item->id,
                'seq' => intval($item->seq),
                'department_id' => $item->department_id,
                'is_all_department' => $item->is_all_department,
                'department_name' => $item->department_name,
                'section_id' => $item->section_id,
                'is_all_section' => $item->is_all_section,
                'section_name' => $item->section_name,
                'role_id' => $item->role_id,
                'is_all_role' => $item->is_all_role,
                'role_name' => $item->role_name,
                'user_id' => $item->user_id,
                'user_name' => $item->user_name,
                'is_super_user' => $item->is_super_user,
                'view' => true
            ])->render();

            return $item;
        });

        $config = ConfigApprove::select('id', 'type as name')->orderBy('name')->get();
        $user_department_list = Department::select('name', 'id')->get();
        $role_list = Role::select('name', 'id')->get();
        $user_list = User::select('name', 'id')->get();
        $page_title =   __('lang.view') . __('config_approves.page_title') . ' > สาขา ' . $branch->name;

        return view('admin.config-approves.view', [
            'd' => $config_approve,
            'page_title' => $page_title,
            'user_department_list' => $user_department_list,
            'role_list' => $role_list,
            'user_list' => $user_list,
            'config_approve_lines' => $config_approve_lines,
            'config' => $config,
            'branch_id' => $branch->id,
            'branch_name' => $branch->name,
            'table_html' => $table_html
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ConfigApprove);
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|exists:branches,id',
            'data_config.*.seq' => [
                'required', 'integer',
            ],
            'data_config.*.department_id' => [
                'required', 'max:255',
            ],
        ], [], [
            'data_config.*.seq' => __('config_approves.seq'),
            'data_config.*.department_id' => __('config_approves.department'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $branch_id = $request->branch_id;
        $id = $request->id;
        $data_configs = $request->data_config;
        $custom_rules = [];
        if (!empty($data_configs)) {
            foreach ($data_configs as $key => $item) {
                if (($item['is_super_user'] === "true") && ((empty($item['role_id'])) && (empty($item['user_id']))) && (($item['is_all_role'] === "false") && ($item['is_all_department'] === "false"))) {
                    $custom_rules = [
                        'role_id' => [
                            'required'
                        ],
                        'user_id' => [
                            'required'
                        ],
                    ];
                }
                if (($item['is_all_role'] === "false") && ($item['is_all_department'] === "false") && (empty($item['user_id']))) {
                    $custom_rules = [
                        'user_id' => [
                            'required'
                        ],
                    ];
                }
                if (($item['is_all_department'] === "false") && (empty($item['role_id']))) {
                    $custom_rules = [
                        'role_id' => [
                            'required'
                        ],
                    ];
                }
            }
        }

        $validator = Validator::make($request->all(), $custom_rules, [], [
            'role_id' => __('config_approves.role'),
            'user_id' => __('config_approves.full_name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        //old delete
        $del_ids = $request->del_ids;
        if ($del_ids != null) {
            if (is_array($del_ids) && (sizeof($del_ids) > 0)) {
                ConfigApproveLine::whereIn('id', $del_ids)->where('branch_id', $branch_id)->delete();
            }
        }

        $line_id = $request->line_id;
        $seq = $request->seq;
        $department_id = $request->department_id;
        $section_id = $request->section_id;
        $role_id = $request->role_id;
        $user_id = $request->user_id;

        $is_all_department = $request->is_all_department;
        $is_all_section = $request->is_all_section;
        $is_all_role = $request->is_all_role;
        $is_super_user = $request->is_super_user;
        if (is_array($line_id) && (sizeof($line_id) > 0)) {
            foreach ($line_id as $index => $_line_id) {
                // check is person
                if (!empty($user_id[$index])) {
                    $user = User::find($user_id[$index]);
                    if ($user) {
                        $config_approve_line = ConfigApproveLine::firstOrNew(['id' => $_line_id]);
                        $config_approve_line->seq = $seq[$index]; //$seq[$index];
                        $config_approve_line->config_approve_id = $id;
                        $config_approve_line->branch_id = $branch_id;
                        $config_approve_line->department_id = $user->department_id;
                        $config_approve_line->is_all_department = false;
                        $config_approve_line->section_id = $user->section_id;
                        $config_approve_line->is_all_section = false;
                        $config_approve_line->role_id = $user->role_id;
                        $config_approve_line->is_all_role = false;
                        $config_approve_line->user_id = $user->id;
                        $config_approve_line->is_super_user = boolval($is_super_user[$index]);
                        $config_approve_line->save();
                    }
                } else {
                    $config_approve_line = ConfigApproveLine::firstOrNew(['id' => $_line_id]);
                    $config_approve_line->seq = $seq[$index];; //$seq[$index];
                    $config_approve_line->config_approve_id = $id;
                    $config_approve_line->branch_id = $branch_id;
                    $config_approve_line->department_id = $department_id[$index];
                    $config_approve_line->is_all_department = is_null($department_id[$index]) ? false : boolval($is_all_department[$index]);
                    $config_approve_line->section_id = $section_id[$index];
                    $config_approve_line->is_all_section = is_null($section_id[$index]) ? false : boolval($is_all_section[$index]);
                    $config_approve_line->role_id = $role_id[$index];
                    $config_approve_line->is_all_role = is_null($role_id[$index]) ? false : boolval($is_all_role[$index]);
                    $config_approve_line->user_id = null;
                    $config_approve_line->is_super_user = false;
                    $config_approve_line->save();
                }
            }
        }

        $redirect_route = route('admin.config-approves.index', ['branch_id' => $request->branch_id]);
        return $this->responseValidateSuccess($redirect_route);
    }

    function getRole(Request $request)
    {
        $data = User::find($request->id);
        return [
            'success' => true,
            'data' => $data,
        ];
    }

    function addRow(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'm_seq' => 'required|integer|min:0',
            'is_person' => 'required',
            'm_user_id' => 'required_if:is_person,1',
            'm_is_super_user' => 'required_if:is_person,1',

            'm_department_id' => 'required_if:is_person,0',
            'm_is_all_department' => 'required_with:m_department_id',
            'm_section_id' => 'required_if:m_is_all_department,0',
            'm_is_all_section' => 'required_with:m_section_id',
            'm_role_id' => 'required_if:m_is_all_section,0',
        ], [
            'required_if' => 'กรุณากรอก :attribute',
            'required_with' => 'กรุณากรอก :attribute',
        ], [
            'm_seq' => __('config_approves.seq'),
            'is_person' => __('config_approves.is_person'),
            'm_user_id' => __('config_approves.user'),
            'm_is_super_user' => __('config_approves.super_user'),
            'm_department_id' => __('config_approves.department'),
            'm_is_all_department' => __('config_approves.all_department'),
            'm_section_id' => __('config_approves.section'),
            'm_is_all_section' => __('config_approves.all_section'),
            'm_role_id' => __('config_approves.role'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        // default
        $success = false;
        $m_seq = intval($request->m_seq);
        $m_department_id = null;
        $m_section_id = null;
        $m_role_id = null;
        $m_user_id = null;
        $m_is_all_department = false;
        $m_is_all_section = false;
        $m_is_all_role = false;
        $m_is_super_user = false;

        $is_person = boolval($request->is_person);
        if ($is_person) {
            $user = User::find($request->m_user_id);
            if ($user) {
                $m_user_id = $user->id;
                $m_department_id = $user->department_id;
                $m_section_id = $user->section_id;
                $m_role_id = $user->role_id;
                $m_is_all_department = false;
                $m_is_all_section = false;
                $m_is_all_role = false;
                $m_is_super_user = boolval($request->m_is_super_user);
                $success = true;
            }
        } else {
            $m_user_id = null;
            $m_department_id = $request->m_department_id;
            $m_section_id = $request->m_section_id;
            $m_role_id = $request->m_role_id;
            $m_is_all_department = boolval($request->m_is_all_department);
            $m_is_all_section = boolval($request->m_is_all_section);
            $m_is_all_role = true; //boolval($request->m_is_all_role);
            if ($m_is_all_department) {
                $m_section_id = null;
                $m_role_id = null;
                $m_is_all_section = false;
            } else if ($m_is_all_section) {
                $m_role_id = null;
            }
            $success = true;
        }
        $html = null;
        if ($success) {
            $html = $this->formatRow(null, $m_seq, $m_department_id, $m_is_all_department, $m_section_id, $m_is_all_section, $m_role_id, $m_is_all_role, $m_user_id, $m_is_super_user);
        }
        return [
            'success' => $success,
            'data' => $request->all(),
            'html' => $html
        ];
    }

    function formatRow($line_id, $seq, $department_id, $is_all_department, $section_id, $is_all_section, $role_id, $is_all_role, $user_id, $is_super_user)
    {
        $department_name = find_name_by_id($department_id, Department::class);
        $section_name = find_name_by_id($section_id, Section::class);
        $role_name = find_name_by_id($role_id, Role::class);
        $user_name = find_name_by_id($user_id, User::class);
        $html = view('admin.config-approves.components.row', [
            'line_id' => $line_id,
            'seq' => $seq,
            'department_id' => $department_id,
            'is_all_department' => $is_all_department,
            'department_name' => $department_name,
            'section_id' => $section_id,
            'is_all_section' => $is_all_section,
            'section_name' => $section_name,
            'role_id' => $role_id,
            'is_all_role' => $is_all_role,
            'role_name' => $role_name,
            'user_id' => $user_id,
            'user_name' => $user_name,
            'is_super_user' => $is_super_user,
        ])->render();
        return $html;
    }
}
