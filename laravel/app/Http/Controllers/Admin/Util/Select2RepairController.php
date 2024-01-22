<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\CreditorTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\BillSlip;
use App\Models\Car;
use App\Models\CreditorTypeRelation;
use App\Models\Repair;
use App\Models\RepairList;
use App\Models\RepairOrder;
use App\Models\Slide;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Select2RepairController extends Controller
{
    function getRepairCodeList(Request $request)
    {
        $list = RepairList::select('id', 'code')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('code', 'like', '%' . $request->s . '%');
                }
            })
            ->orderBy('code')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->code
                ];
            });
        return response()->json($list);
    }

    function getRepairNameList(Request $request)
    {
        $list = RepairList::select('id', 'name')
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('name', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->id)) {
                    $query->where('id', $request->id);
                }
            })
            ->orderBy('name')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getSlideList(Request $request)
    {
        $list = Slide::select('id', 'worksheet_no')
            // ->whereNotIn('status', ['COMPLETE'])
            ->where(function ($query) use ($request) {
                if (!empty($request->s)) {
                    $query->where('worksheet_no', 'like', '%' . $request->s . '%');
                }
                if (!empty($request->id)) {
                    $query->where('id', $request->id);
                }
            })
            ->orderBy('worksheet_no')
            ->limit(30)
            ->get()->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        __log($list);
        return response()->json($list);
    }

    function getBillRecipient(Request $request)
    {
        $list = [];
        $search = $request->s;
        $list = User::limit(30)
            ->when(!empty($search), function ($query_search) use ($search) {
                $query_search->where('name', 'Like', '%' . $search . '%');
            })
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    function getGeographies(Request $request)
    {
        $list = [];
        $search = $request->s;
        $list = DB::table('Geographies')
            ->when(!empty($search), function ($query_search) use ($search) {
                $query_search->where('name', 'Like', '%' . $search . '%');
            })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    public function getCreditorServices(Request $request)
    {
        $list = [];
        $search = $request->s;
        $list = CreditorTypeRelation::leftJoin('creditors', 'creditors.id', 'creditors_types_relation.creditor_id')
            ->leftJoin('creditor_types', 'creditor_types.id', 'creditors_types_relation.creditor_type_id')
            ->select('creditors.name', 'creditors.id')
            ->where('creditor_types.type', CreditorTypeEnum::LEASING)
            ->when(!empty($search), function ($query_search) use ($search) {
                $query_search->where('creditors.name', 'Like', '%' . $search . '%');
            })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            });
        return response()->json($list);
    }

    public function getRepairBillNoList(Request $request)
    {
        $list = [];
        $search = $request->s;
        $list = BillSlip::when(!empty($search), function ($query_search) use ($search) {
            $query_search->where('worksheet_no', 'Like', '%' . $search . '%');
        })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->worksheet_no
                ];
            });
        return response()->json($list);
    }

    public function getCarList(Request $request)
    {
        $search = $request?->s;
        $listCar = Car::select('id', 'license_plate', 'engine_no', 'chassis_no')
            ->when(!empty($search), function ($query_search) use ($search) {
                $query_search->where('license_plate', 'like', '%' . $search . '%');
                $query_search->orWhere('engine_no', 'like', '%' . $search . '%');
                $query_search->orWhere('chassis_no', 'like', '%' . $search . '%');
            })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                $text = null;
                if ($item?->license_plate) {
                    $text = __('inspection_cars.license_plate') . ' ' . $item?->license_plate;
                } else if ($item?->engine_no) {
                    $text = __('inspection_cars.engine_no') . ' ' . $item?->engine_no;
                } else if ($item?->chassis_no) {
                    $text = __('inspection_cars.chassis_no') . ' ' . $item?->chassis_no;
                }
                $item->id = $item->id;
                $item->text = $text;
                return $item;
            });
        return response()->json($listCar);
    }

    public function getRepairWorksheetNo(Request $request)
    {
        $search = $request?->s;
        $list_work_sheet = Repair::select('id', 'worksheet_no')
            ->when(!empty($search), function ($query_search) use ($search) {
                $query_search->where('worksheet_no', 'like', '%' . $search . '%');
            })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                $item->id = $item?->id;
                $item->text = $item?->worksheet_no;
                return $item;
            });
        return response()->json($list_work_sheet);
    }

    public function getRepairListItem(Request $request)
    {
        $search = $request?->s;
        $repair_list = RepairList::when(!empty($search), function ($query_search) use ($search) {
            $query_search->where('name', 'Like', '%' . $search . '%');
        })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                $item->id = $item?->id;
                $item->text = $item?->name;
                return $item;
            });
        return response()->json($repair_list);
    }

    public function getInvoiceNo(Request $request)
    {
        $search = $request?->s;
        $repair_oreder = RepairOrder::when(!empty($search), function ($query_search) use ($search) {
            $query_search->where('invoice_no', 'Like', '%' . $search . '%');
        })
            ->limit(30)
            ->get()
            ->map(function ($item) {
                $item->id = $item?->id;
                $item->text = $item?->invoice_no;
                return $item;
            });
        return response()->json($repair_oreder);
    }
}
