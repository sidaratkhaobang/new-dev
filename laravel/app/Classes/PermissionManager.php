<?php

namespace App\Classes;

use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use App\Enums\Actions;
use App\Enums\Resources;

class PermissionManager
{
    public $user;
    public $role;
    public $permissions;
    public $is_init;

    public function __construct()
    {
        $this->permissions = [];
        $this->is_init = false;
        $this->user = Auth::user();
        $this->initPermissions();
    }

    function initPermissions()
    {
        if ($this->user && (!$this->is_init)) {
            if ($this->user->role) {
                $this->permissions = $this->user->role->permission->map(function ($item) {
                    return $item->permission;
                })->toArray();
                $this->is_init = true;
            }
        }
    }

    function getUserPermissions()
    {
        return $this->permissions;
    }

    function can($permission)
    {
        return in_array($permission, $this->permissions);
    }
}
