<?php

namespace App\Repositories;

use App\Repositories\SideBarLink;
use Illuminate\Support\Str;

class SideBarSubmenu
{
    public $title;
    public $menus;
    public $allow_permissions;
    public $icon;

    function __construct($title, $menus = [], $allow_permissions = [], $icon = null)
    {
        $this->title = $title;
        $this->menus = $menus;
        $this->allow_permissions = $allow_permissions;
        $this->icon = $icon;
    }

    function render()
    {
        $current_url = url()->current();
        $is_open = false;
        foreach ($this->menus as $menu) {
            if (is_a($menu, SideBarLink::class)) {
                if (Str::startsWith($current_url, $menu->link)) {
                    $is_open = true;
                    $menu->is_active = true;
                    break;
                }
            } else if (is_a($menu, SideBarSubmenu::class)) {
                foreach ($menu->menus as $_menu) {
                    if (is_a($_menu, SideBarLink::class)) {
                        if (Str::startsWith($current_url, $_menu->link)) {
                            $is_open = true;
                            $_menu->is_active = true;
                            break;
                        }
                    }
                }
            }
        }
        $user_permissions = get_user_permissions();
        $intersect = array_intersect($this->allow_permissions, $user_permissions);
        $is_allow = ((sizeof($intersect) > 0) ? true : false);
        if ((sizeof($this->allow_permissions) > 0) && (!$is_allow)) {
            return null;
        }
        return view('admin.components.sidebar-submenu', [
            'title' => $this->title,
            'menus' => $this->menus,
            'is_open' => $is_open,
            'icon' => $this->icon
        ])->render();
    }
}
