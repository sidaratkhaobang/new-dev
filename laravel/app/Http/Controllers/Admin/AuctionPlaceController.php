<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Models\AuctionPlace;
use Illuminate\Support\Facades\Validator;

class AuctionPlaceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::AuctionPlace);
        $name = $request->name;
        $contact_name = $request->contact_name;
        $status = $request->status;
        $list = AuctionPlace::select('auction_places.*')
            ->sortable('name')
            ->search($request)
            ->paginate(PER_PAGE);

        $status_list =  $this->getStatusList();
        $name_list = AuctionPlace::select('name as id', 'name')->get();
        $contact_list = AuctionPlace::select('contact_name as id', 'contact_name as name')->get();
        $page_title =  __('auction_places.page_title');
        return view('admin.auction-places.index', [
            'list' => $list,
            'page_title' => $page_title,
            'name' => $name,
            'name_list' => $name_list,
            'contact_name' => $contact_name,
            'contact_list' => $contact_list,
            'status' => $status,
            'status_list' => $status_list,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::AuctionPlace);
        $d = new AuctionPlace();
        $d->status = STATUS_ACTIVE;
        $status_list =  $this->getStatusActive();

        $page_title = __('lang.create') . __('auction_places.page_title');
        return view('admin.auction-places.form', [
            'd' => $d,
            'page_title' => $page_title,
            'status_list' => $status_list,
        ]);
    }

    public function edit(AuctionPlace $auction_place)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AuctionPlace);
        $status_list =  $this->getStatusActive();

        $page_title = __('lang.edit') . __('auction_places.page_title');
        return view('admin.auction-places.form', [
            'd' => $auction_place,
            'page_title' => $page_title,
            'status_list' => $status_list,
        ]);
    }

    public function show(AuctionPlace $auction_place)
    {
        $this->authorize(Actions::View . '_' . Resources::AuctionPlace);
        $status_list =  $this->getStatusActive();

        $page_title = __('lang.view') . __('auction_places.page_title');
        return view('admin.auction-places.form', [
            'd' => $auction_place,
            'page_title' => $page_title,
            'status_list' => $status_list,
            'view' => true,
        ]);
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AuctionPlace);
        $auction_place = AuctionPlace::find($id);
        $auction_place->delete();

        return response()->json([
            'success' => true,
            'message' => 'Complete',
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:100',
            ],
        ], [], [
            'name' => __('auction_places.name'),
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $auction_place = AuctionPlace::firstOrNew(['id' => $request->id]);
        $auction_place->name = $request->name;
        $auction_place->address = $request->address;
        $auction_place->contact_name = $request->contact_name;
        $auction_place->contact_tel = $request->contact_tel;
        $auction_place->contact_email = $request->contact_email;
        $auction_place->remark = $request->remark;
        $auction_place->status = $request->status;
        $auction_place->save();

        $redirect_route = route('admin.auction-places.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    private function getStatusActive()
    {
        return collect([
            [
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('auction_places.status_' . STATUS_ACTIVE),
            ],
            [
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => __('auction_places.status_' . STATUS_INACTIVE),
            ],
        ]);
    }

    private function getStatusList()
    {
        return collect([
            (object)[
                'id' => STATUS_ACTIVE,
                'value' => STATUS_ACTIVE,
                'name' => __('auction_places.status_' . STATUS_ACTIVE),
            ],
            (object)[
                'id' => STATUS_INACTIVE,
                'value' => STATUS_INACTIVE,
                'name' => __('auction_places.status_' . STATUS_INACTIVE),
            ],
        ]);
    }
}
