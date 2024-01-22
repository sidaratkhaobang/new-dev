<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecordPettyCashController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::RecordPettyCash);
        $list = [];
        return view('admin.record-petty-cashes.index', [
            'list' => $list,
        ]);
    }
}
