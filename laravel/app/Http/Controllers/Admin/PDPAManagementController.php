<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ConsentType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pdpa;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Enums\Actions;
use App\Enums\Resources;

class PDPAManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Pdpa);
        $list = Pdpa::select('pdpas.*')
            ->sortable(['version' => 'desc'])
            ->search($request->s)
            ->paginate(PER_PAGE);
        return view('admin.pdpas.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Pdpa);
        $d = new Pdpa();
        $consent_type_list = $this->getConsentType();
        $page_title = __('lang.create') . ' ' . __('pdpas.page_title');
        return view('admin.pdpas.form', compact('d', 'page_title','consent_type_list'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $version_check = PDPA::where('consent_type',$request->consent_type)
        ->where('version',$request->version)->exists();
        $validator = Validator::make($request->all(), [
            'version' => [
                'required', 'string', 'max:10',
                // Rule::when($request->consent_type == STATUS_ACTIVE, ['required']),
                // Rule::unique('pdpas', 'version')->whereNull('deleted_at')->ignore($request->id),
            ],
            'description_th' => [
                'required', 'string',
            ],
            'description_en' => [
                'required', 'string',
            ],
            'consent_type' => [
                'required',
            ],
        ], [], [
            'version' => __('pdpas.version'),
            // 'version_exist' => __('pdpas.version'),
            'description_th' => __('pdpas.description_th'),
            'description_en' => __('pdpas.description_en'),
            'consent_type' => __('pdpas.consent_type'),
        ]);


        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        if ($version_check) {
            return $this->responseWithCode(true,'เวอร์ชัน ต้องไม่ซ้ำกัน',null,422);
        }
        $pdpa = Pdpa::firstOrNew(['id' => $request->id]);
        // $pdpa_latest = Pdpa::select('version')->orderBy('created_at', 'desc')->first();
        // $pdpa->version = $request->version;
        // if (!empty($pdpa_latest)) {
        //     if (($pdpa->version == $pdpa_latest->version) || ($pdpa->version > $pdpa_latest->version)) {
        //         $customer = Customer::withTrashed()->where('is_accept_pdpa', 1)->update(array('is_accept_pdpa' => 0));
        //     }
        // }
        $pdpa->version = $request->version;
        $pdpa->description_th = $request->description_th;
        $pdpa->description_en = $request->description_en;
        $pdpa->consent_type = $request->consent_type;
        $pdpa->save();

        $redirect_route = route('admin.pdpa-managements.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Pdpa $pdpa_management)
    {
        $this->authorize(Actions::View . '_' . Resources::Pdpa);
        $page_title = __('lang.view') . ' ' . __('pdpas.page_title');
        $view = true;
        $consent_type_list = $this->getConsentType();
        return view('admin.pdpas.form', [
            'd' => $pdpa_management,
            'view' => $view,
            'page_title' => $page_title,
            'consent_type_list' => $consent_type_list
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Pdpa $pdpa_management)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Pdpa);
        $page_title = __('lang.edit') . ' ' . __('pdpas.page_title');
        $consent_type_list = $this->getConsentType();
        return view('admin.pdpas.form', [
            'd' => $pdpa_management,
            'page_title' => $page_title,
            'consent_type_list' => $consent_type_list,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getConsentType()
    {
        $status = collect([
            (object) [
                'id' => ConsentType::MARKETING,
                'name' => 'MARKETING',
                'value' => ConsentType::MARKETING,
            ],
            (object) [
                'id' => ConsentType::PRIVACY,
                'name' => 'PRIVACY',
                'value' => ConsentType::PRIVACY,
            ],
            (object) [
                'id' => ConsentType::SENSITIVE,
                'name' => 'SENSITIVE',
                'value' => ConsentType::SENSITIVE,
            ],
        ]);
        return $status;
    }
}
