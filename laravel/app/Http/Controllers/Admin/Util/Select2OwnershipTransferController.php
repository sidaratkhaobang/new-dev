<?php

namespace App\Http\Controllers\Admin\Util;

use App\Enums\OwnershipTransferFaceSheetTypeEnum;
use App\Enums\OwnershipTransferStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\CarClass;
use App\Models\Creditor;
use App\Models\InsuranceLot;
use App\Models\OwnershipTransfer;
use App\Models\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Select2OwnershipTransferController extends Controller
{

    function getContractNo(Request $request)
    {
        $list = OwnershipTransfer::leftjoin('hire_purchases','hire_purchases.id','=','ownership_transfers.hire_purchase_id')
        ->select('hire_purchases.id', 'hire_purchases.contract_no as name')
        ->where(function ($query) use ($request) {
            if (!empty($request->s)) {
                $query->where('hire_purchases.contract_no', 'like', '%' . $request->s . '%');
            }
        })
        ->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name,
            ];
        });
    return response()->json($list);
    }

    function getLeasing(Request $request)
    {
        $list = Creditor::select('creditors.id', 'creditors.name')
        ->where(function ($query) use ($request) {
            if (!empty($request->s)) {
                $query->where('creditors.name', 'like', '%' . $request->s . '%');
            }
        })
        ->limit(30)
        ->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'text' => $item->name,
            ];
        });
    return response()->json($list);
    }

    public function getStatusList(Request $request)
    {
        $s = $request->s;

        $status = collect([
            (object) [
                'id' => OwnershipTransferStatusEnum::WAITING_TRANSFER,
                'text' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::WAITING_TRANSFER . '_text'),
                'value' => OwnershipTransferStatusEnum::WAITING_TRANSFER,
            ],
            (object) [
                'id' => OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER,
                'text' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER . '_text'),
                'value' => OwnershipTransferStatusEnum::WAITING_DOCUMENT_TRANSFER,
            ],
            (object) [
                'id' => OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER,
                'text' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER . '_text'),
                'value' => OwnershipTransferStatusEnum::WAITING_SEND_TRANSFER,
            ],
            (object) [
                'id' => OwnershipTransferStatusEnum::TRANSFERING,
                'text' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::TRANSFERING . '_text'),
                'value' => OwnershipTransferStatusEnum::TRANSFERING,
            ],
            (object) [
                'id' => OwnershipTransferStatusEnum::TRANSFERED,
                'text' => __('ownership_transfers.status_' . OwnershipTransferStatusEnum::TRANSFERED . '_text'),
                'value' => OwnershipTransferStatusEnum::TRANSFERED,
            ],
        ]);

        if ($s) {
            $statuse = $status->filter(function ($item) use ($s) {
                return str_contains($item->text, $s);
            })->values();
        }

        return $status;
    }

    public function getStatusFaceSheetList(Request $request)
    {
        $s = $request->s;

        $facesheet_types = collect([
            (object)[
                'id' => OwnershipTransferFaceSheetTypeEnum::OWNERSHIP_TRANSFER,
                'value' => OwnershipTransferFaceSheetTypeEnum::OWNERSHIP_TRANSFER,
                'text' => __('ownership_transfers.type_face_sheet_' . OwnershipTransferFaceSheetTypeEnum::OWNERSHIP_TRANSFER),
            ],
            (object)[
                'id' => OwnershipTransferFaceSheetTypeEnum::RETURN_REGISTER_BOOK,
                'value' => OwnershipTransferFaceSheetTypeEnum::RETURN_REGISTER_BOOK,
                'text' => __('ownership_transfers.type_face_sheet_' . OwnershipTransferFaceSheetTypeEnum::RETURN_REGISTER_BOOK),
            ],
        ]);

        if ($s) {
            $facesheet_types = $facesheet_types->filter(function ($item) use ($s) {
                return str_contains($item->text, $s);
            })->values();
        }

        return $facesheet_types;
    }
  

  
}
