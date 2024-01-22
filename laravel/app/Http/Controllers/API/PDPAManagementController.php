<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pdpa;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PDPAManagementController extends Controller
{
    public function index(Request $request)
    {
        $list = Pdpa::select('id', 'version', 'consent_type')
            ->search($request->s)
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function indexByType(Request $request)
    {
        $consent_type = $request->consent_type;
        $list = Pdpa::select('id', 'version', 'consent_type')
            ->where('consent_type', strtoupper($consent_type))
            ->search($request->s)
            ->paginate(PER_PAGE);
        return response()->json($list, 200);
    }

    public function read(Request $request)
    {
        $data = Pdpa::select('id', 'version', 'consent_type', 'description_th', 'description_en')
            ->where('pdpas.id', $request->id)
            ->first();
        if (empty($data)) {
            return $this->responseWithCode(false, DATA_NOT_FOUND, null, 404);
        }
        return $this->responseWithCode(true, DATA_SUCCESS, $data, 200);
    }
}
