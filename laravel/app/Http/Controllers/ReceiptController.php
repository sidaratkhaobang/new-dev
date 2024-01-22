<?php

namespace App\Http\Controllers;

use App\Models\OrderPromotionCodeLine;
use App\Models\Receipt;
use App\Models\Rental;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    function print(Request $request)
    {
        $receipt = Receipt::find($request->id);
        if ($receipt) {
            $page_title = $receipt->worksheet_no;
            $reference_id = $receipt->reference_id;
            $reference_type = $receipt->reference_type;
            if (strcmp($reference_type, Rental::class) == 0) {
                $rental = Rental::find($reference_id);
                $branch_tax_no = ($rental->branch) ? $rental->branch->tax_no : null;
                $branch_name = ($rental->branch) ? $rental->branch->name : null;
                $branch_address = ($rental->branch) ? $rental->branch->address : null;

                $pdf = PDF::loadView(
                    'admin.receipts.component-pdf.pdf',
                    [
                        'd' => $receipt,
                        'branch_tax_no' => $branch_tax_no,
                        'branch_name' => $branch_name,
                        'branch_address' => $branch_address,
                        'page_title' => $page_title,
                    ]
                );
                return $pdf->stream();
            } else if (strcmp($reference_type, OrderPromotionCode::class) == 0) {
                $branch_promotion = OrderPromotionCodeLine::leftJoin('promotion_codes', 'promotion_codes.id', '=', 'order_promotion_code_lines.promotion_code_id')
                    ->leftJoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
                    ->leftJoin('branches', 'branches.id', '=', 'promotions.branch_id')
                    ->where('order_promotion_code_lines.order_promotion_code_id', $reference_id)
                    ->select('branches.name as branch_name', 'branches.tax_no as branch_tax_no', 'branches.address as branch_address')->first();
                $branch_name = ($branch_promotion->branch_name) ? $branch_promotion->branch_name : null;
                $branch_tax_no = ($branch_promotion->branch_tax_no) ? $branch_promotion->branch_tax_no : null;
                $branch_address = ($branch_promotion->branch_address) ? $branch_promotion->branch_address : null;

                $pdf = PDF::loadView(
                    'admin.receipts.component-pdf.pdf',
                    [
                        'd' => $receipt,
                        'branch_tax_no' => $branch_tax_no,
                        'branch_name' => $branch_name,
                        'branch_address' => $branch_address,
                        'page_title' => $page_title,
                    ]
                );
                return $pdf->stream();
            }
        } else {
            abort(404);
        }
    }
}