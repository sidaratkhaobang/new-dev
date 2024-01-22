<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RolePermission;
use App\Models\Role;
use App\Classes\Permissions;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Models\Department;
use App\Models\Section;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Permission);
        $permissions = get_all_permissions();
        $s = $request->s;
        if (!empty($s)) {
            $permissions = collect($permissions)->filter(function ($d, $permission) use ($s) {
                $permission_name = __('permissions.' . $permission);
                if ((strpos($permission, $s) !== false) || (strpos($permission_name, $s) !== false)) {
                    return true;
                } else {
                    return false;
                }
            })->toArray();
        }
        return view('admin.permissions.index', [
            'list' => $permissions,
            's' => $s,
        ]);
    }

    function edit(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Permission);
        $permission = $request->permission;
        $roles = Role::select('roles.id', 'roles.name', 'roles.department_id')
            ->addSelect('departments.name as department_name', 'sections.name as section_name')
            ->addSelect('a.permission as view_permission', 'b.permission as manage_permission')
            ->leftJoin('departments', 'departments.id', 'roles.department_id')
            ->leftJoin('sections', 'sections.id', 'roles.section_id')
            ->leftJoin('roles_permissions as a', function ($join) use ($permission) {
                $join->on('a.role_id', '=', 'roles.id')->where('a.permission', 'like', 'view_' . $permission);
            })
            ->leftJoin('roles_permissions as b', function ($join) use ($permission) {
                $join->on('b.role_id', '=', 'roles.id')->where('b.permission', 'like', 'manage_' . $permission);
            })
            ->orderBy('departments.name')
            ->orderBy('roles.name')
            ->get();
        $departments = Department::select('id', 'name')->where('status', STATUS_ACTIVE)->get();
        return view('admin.permissions.form', [
            'roles' => $roles,
            'permission' => $permission,
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $permission = $request->permission;
        $role_view = $request->role_view;
        $role_manage = $request->role_manage;
        if (!empty($request->permission)) {
            RolePermission::whereIn('permission', ['view_' . $permission, 'manage_' . $permission])->delete();
            if (is_array($role_view) && (sizeof($role_view) > 0)) {
                $tempData = [];
                foreach ($role_view as $role_id => $value) {
                    $tempData[] = [
                        'role_id' => $role_id,
                        'permission' => $value
                    ];
                }
                RolePermission::insert($tempData);
            }
            if (is_array($role_manage) && (sizeof($role_manage) > 0)) {
                $tempData = [];
                foreach ($role_manage as $role_id => $value) {
                    $tempData[] = [
                        'role_id' => $role_id,
                        'permission' => $value
                    ];
                }
                RolePermission::insert($tempData);
            }
            return response()->json([
                'success' => true,
                'message' => 'ok',
                'redirect' => route('admin.permissions.index')
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => __('lang.not_found')
            ], 422);
        }
    }
}
