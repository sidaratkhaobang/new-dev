<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuctionRejectReason;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AuctionRejectReasonController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::AuctionRejectReason);  
        $list = AuctionRejectReason::sortable('name')
            ->search($request->s)
            ->paginate(PER_PAGE);

        return view('admin.auction-reject-reasons.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::AuctionRejectReason);
        $d = new AuctionRejectReason();
        $page_title = __('lang.create') . __('auction_reject_reasons.page_title');

        return view('admin.auction-reject-reasons.form', [
            'd' => $d,
            'page_title' => $page_title
        ]);
    }

    public function edit(AuctionRejectReason $auction_reject_reason)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AuctionRejectReason);
        $page_title = __('lang.edit') . __('auction_reject_reasons.page_title');
        return view('admin.auction-reject-reasons.form', [
            'd' => $auction_reject_reason,
            'page_title' => $page_title,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AuctionRejectReason);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 'string', 'max:100',
                Rule::unique('auction_reject_reasons', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
        ], [], [
            'name' => __('auction_reject_reasons.name')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $auction_reject_reason = AuctionRejectReason::firstOrNew(['id' => $request->id]);
        $auction_reject_reason->name = $request->name;
        $auction_reject_reason->status = STATUS_ACTIVE;
        $auction_reject_reason->save();

        $redirect_route = route('admin.auction-reject-reasons.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(AuctionRejectReason $auction_reject_reason)
    {
        $this->authorize(Actions::View . '_' . Resources::AuctionRejectReason);
        $page_title = __('lang.view') . __('auction_reject_reasons.page_title');
        $view = true;
        return view('admin.auction-reject-reasons.form', [
            'd' => $auction_reject_reason,
            'view' => $view,
            'page_title' => $page_title,
        ]);
    }


    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::AuctionRejectReason);
        $auction_reject_reason = AuctionRejectReason::find($id);
        $auction_reject_reason->delete();

        return $this->responseComplete();
    }
}
