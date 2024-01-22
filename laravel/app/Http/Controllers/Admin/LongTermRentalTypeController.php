<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\AuctionStatusEnum;
use App\Enums\Resources;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LongTermRentalType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class LongTermRentalTypeController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalType);
        $list = LongTermRentalType::sortable('name')
            ->branch()
            ->search($request->s)
            ->paginate(PER_PAGE);

        return view('admin.long-term-rental-types.index', [
            'list' => $list,
            's' => $request->s,
        ]);
    }

    public function create()
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalType);
        $d = new LongTermRentalType();
        $type_lists = $this->getTypeList();
        $page_title = __('lang.create') . __('long_term_rental_types.page_title');

        return view('admin.long-term-rental-types.form', [
            'd' => $d,
            'page_title' => $page_title,
            'type_lists' => $type_lists
        ]);
    }

    public static function getTypeList()
    {
        $type_lists = collect([
            (object) [
                'id' => AuctionStatusEnum::AUCTION,
                'name' => __('long_term_rental_types.type_' . AuctionStatusEnum::AUCTION),
                'value' => AuctionStatusEnum::AUCTION,
            ],
            (object) [
                'id' => AuctionStatusEnum::NO_AUCTION,
                'name' => __('long_term_rental_types.type_' . AuctionStatusEnum::NO_AUCTION),
                'value' => AuctionStatusEnum::NO_AUCTION,
            ],
        ]);
        return $type_lists;
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalType);
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('lt_rental_types', 'name')->whereNull('deleted_at')->ignore($request->id),
            ],
            'type' => [
                'required',
            ],
        ], [], [
            'name' => __('long_term_rental_types.name'),
            'type' => __('long_term_rental_types.type')
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }

        $long_term_rental_type = LongTermRentalType::firstOrNew(['id' => $request->id]);
        $long_term_rental_type->name = $request->name;
        $long_term_rental_type->type = $request->type;
        $long_term_rental_type->status = STATUS_ACTIVE;
        $long_term_rental_type->save();

        $redirect_route = route('admin.long-term-rental-types.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function show(LongTermRentalType $long_term_rental_type)
    {
        $this->authorize(Actions::View . '_' . Resources::LongTermRentalType);
        $type_lists = $this->getTypeList();
        $page_title = __('lang.view') . __('long_term_rental_types.page_title');
        return view('admin.long-term-rental-types.form', [
            'd' => $long_term_rental_type,
            'page_title' => $page_title,
            'type_lists' => $type_lists,
            'view' => true,
        ]);
    }

    public function edit(LongTermRentalType $long_term_rental_type)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalType);
        $type_lists = $this->getTypeList();
        $page_title = __('lang.edit') . __('long_term_rental_types.page_title');
        return view('admin.long-term-rental-types.form', [
            'd' => $long_term_rental_type,
            'page_title' => $page_title,
            'type_lists' => $type_lists
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $this->authorize(Actions::Manage . '_' . Resources::LongTermRentalType);
        $long_term_rental_type = LongTermRentalType::find($id);
        $long_term_rental_type->delete();

        return $this->responseComplete();
    }
}
