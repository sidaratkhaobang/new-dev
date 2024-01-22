<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchLocation;
use DateTime;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::Branch);
        $list = Branch::select('branches.*')
            ->sortable('name')
            ->search($request->s)
            ->paginate(PER_PAGE);
        return view('admin.branches.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::Branch);
        $d = new Branch();
        $page_title = __('lang.create') . __('branches.page_title');
        $yes_no_list =  $this->getYesNoList();

        return view('admin.branches.form', [
            'd' => $d,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list
        ]);
    }

    public function edit(Branch $branch)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Branch);
        $page_title = __('lang.edit') . __('branches.page_title');
        $yes_no_list =  $this->getYesNoList();
        $branch_location_list = BranchLocation::leftjoin('locations', 'locations.id', '=', 'branches_locations.location_id')
            ->leftjoin('location_groups', 'location_groups.id', '=', 'branches_locations.location_group_id')
            ->where('branches_locations.branch_id', $branch->id)
            ->select(
                'branches_locations.location_id',
                'branches_locations.location_group_id',
                'locations.name as location_text',
                'location_groups.name as location_group_text',
                'branches_locations.can_origin',
                'branches_locations.can_stopover',
                'branches_locations.can_destination',
            )->get();

        return view('admin.branches.form', [
            'd' => $branch,
            'page_title' => $page_title,
            'yes_no_list' => $yes_no_list,
            'branch_location_list' => $branch_location_list
        ]);
    }

    public function show(Branch $branch)
    {
        $this->authorize(Actions::View . '_' . Resources::Branch);
        $page_title = __('lang.view') . __('branches.page_title');
        $yes_no_list =  $this->getYesNoList();
        $branch_location_list = BranchLocation::leftjoin('locations', 'locations.id', '=', 'branches_locations.location_id')
            ->leftjoin('location_groups', 'location_groups.id', '=', 'branches_locations.location_group_id')
            ->where('branches_locations.branch_id', $branch->id)
            ->select(
                'branches_locations.location_id',
                'branches_locations.location_group_id',
                'locations.name as location_text',
                'location_groups.name as location_group_text',
                'branches_locations.can_origin',
                'branches_locations.can_stopover',
                'branches_locations.can_destination',
            )->get();
        $view = true;
        return view('admin.branches.view', [
            'd' => $branch,
            'page_title' => $page_title,
            'branch_location_list' => $branch_location_list,
            'yes_no_list' => $yes_no_list,
            'view' => $view
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Branch);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('branches', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'email' => [
                'nullable', 'email:rfc,dns',
            ],
            'document_prefix' => ['nullable', 'string', 'max:2'],
            'registered_code' => ['nullable', 'string', 'max:2'],
        ], [], [
            'name' => __('branches.name'),
            'email' => __('branches.email'),
            'document_prefix' => __('branches.document_prefix'),
            'registered_code' => __('branches.registered_code'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $branch = Branch::firstOrNew(['id' => $request->id]);
        if (!$branch->exists) {
            $latest_no = Branch::all()->count();
            $format_number = strval(sprintf('%02d', $latest_no));
            $branch->code = '05' . $format_number;
        }
        $branch->name = $request->name;
        $branch->is_main = boolval($request->is_main);
        $branch->is_head_office = boolval($request->is_head_office);
        $branch->open_time = $request->open_time;
        $branch->close_time = $request->close_time;
        $branch->tax_no = $request->tax_no;
        $branch->tel = $request->tel;
        $branch->email = $request->email;
        $branch->address = $request->address;
        $branch->lat = $request->lat;
        $branch->lng = $request->lng;
        $branch->cost_center = $request->cost_center;
        $branch->document_prefix = $request->document_prefix;
        $branch->registered_code = $request->registered_code;
        $branch->address = $request->address;
        $branch->status = STATUS_ACTIVE;
        $branch->save();

        if ($branch->id) {
            $this->saveBranchLocations($request, $branch->id);
        }

        $redirect_route = route('admin.branches.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function saveBranchLocations($request, $branch_id)
    {
        BranchLocation::where('branch_id', $branch_id)->delete();
        if (!empty($request->branch_locations)) {
            foreach ($request->branch_locations as $branch_location_item) {
                $branch_location = new BranchLocation();
                $branch_location->branch_id = $branch_id;
                $branch_location->location_id = $branch_location_item['location_id'];
                $branch_location->location_group_id = $branch_location_item['location_group_id'];
                $branch_location->can_origin = $branch_location_item['can_origin'];
                $branch_location->can_stopover = $branch_location_item['can_stopover'];
                $branch_location->can_destination = $branch_location_item['can_destination'];
                $branch_location->save();
            }
        }
        return true;
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::Branch);
        $branch = Branch::find($id);
        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }
}
