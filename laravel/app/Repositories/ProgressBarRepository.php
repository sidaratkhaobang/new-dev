<?php

namespace App\Repositories;

use App\Models\Role;
use App\Repositories\SideBarLink;
use App\Repositories\SideBarSubmenu;
use Illuminate\Support\Collection;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Traits\GpsTrait;
use App\Traits\CarAuctionTrait;

class ProgressBarRepository
{
    public $progress;

    function __construct($progress)
    {
        $this->progress = $progress;
    }

    function render()
    {
        return view('admin.components.progress-bar-step', [
            'progress' => $this->progress,
        ])->render();
    }
}
