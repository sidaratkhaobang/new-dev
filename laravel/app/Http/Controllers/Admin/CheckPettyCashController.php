<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\Resources;

class CheckPettyCashController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CheckPettyCash);
        $list = [];
        return view('admin.check-petty-cashes.index', [
            'list' => $list,
        ]);
    }
}
