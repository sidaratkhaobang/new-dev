<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\Resources;

class PayPremiumController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::PayPremiumApprove);
        $list = [];
        return view('admin.pay-premiums.index', [
            'list' => $list,
        ]);
    }
}
