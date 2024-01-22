<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\Resources;

class RecordOtherExpenseController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::RecordOtherExpenses);
        $list = [];
        return view('admin.record-other-expenses.index', [
            'list' => $list,
        ]);
    }
}
