<?php

namespace App\Repositories;

use Illuminate\Support\Str;

class SideBarLink
{
    public $title;
    public $link;
    public $is_active;
    public $allow_permissions;
    public $icon;

    function __construct($title, $link = '#', $allow_permissions = [], $icon = null)
    {
        $this->title = $title;
        $this->link = $link;
        $this->is_active = false;
        $this->allow_permissions = $allow_permissions;
        $this->icon = $icon;
    }

    function render()
    {
        $user_permissions = get_user_permissions();
        $intersect = array_intersect($this->allow_permissions, $user_permissions);
        $is_allow = ((sizeof($intersect) > 0) ? true : false);
        if ((sizeof($this->allow_permissions) > 0) && (!$is_allow)) {
            return null;
        }
        $current_url = url()->current();
        if (Str::startsWith($current_url, $this->link)) {
            $this->is_active = true;
        }
        return view('admin.components.sidebar-link', [
            'title' => $this->title,
            'link' => $this->link,
            'is_active' => $this->is_active,
            'icon' => $this->icon
        ])->render();
    }
}
