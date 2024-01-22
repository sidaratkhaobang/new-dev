<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Dealers;
use App\Models\CarType;
use App\Models\PurchaseOrderLine;
use App\Models\ImportCarLine;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionLineAccessory;
use App\Enums\ImportCarStatusEnum;
use App\Enums\ImportCarLineStatusEnum;
use App\Models\ImportCar;
use Illuminate\Support\Facades\Auth;
use DateTime;
use App\Enums\CreditorTypeEnum;
use App\Exports\ExportImportCar;
use Maatwebsite\Excel\Facades\Excel;
use App\Enums\CarEnum;
use Carbon\Carbon;

class ImportCarVendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index()
    // {
    //    //
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $purchase_requisition_cars = PurchaseOrderLine::select('purchase_order_lines.id', 'purchase_order_lines.name', 'purchase_order_lines.amount', 'purchase_order_lines.subtotal', 'purchase_order_lines.discount', 'purchase_order_lines.total')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'purchase_requisition_lines.car_color_id')
            ->addSelect('car_colors.name as color_name')
            ->where('import_cars.id', $request->import_id)
            ->get();
        $arr_ob = array();
        for ($i = 0; $i < count($purchase_requisition_cars); $i++) {
            $arr_ob[$purchase_requisition_cars[$i]->id] = [];
        }

        $import_car_lines = ImportCarLine::where('import_car_lines.import_car_id', $request->import_id)->get();

        $import_car_save = ImportCar::find($request->import_id);
        if (!($import_car_save->status == ImportCarStatusEnum::SENT_REVIEW && $request->status == ImportCarStatusEnum::PENDING_REVIEW)) {
            $import_car_save->status = $request->status;
            $import_car_save->save();
        }

        foreach ($purchase_requisition_cars as $index => $item) {
            $index0 = 0;
            foreach ($import_car_lines as $index2 => $import_car_line) {
                if (strcmp($item->id, $import_car_line->po_line_id) == 0) {
                    if ($request->installation_completed_date[$item->id][$index0] != null) {
                        $date = new DateTime($request->installation_completed_date[$item->id][$index0]);
                        $date_install_new = $date->format('Y-m-d');
                    } else {
                        $date_install_new = null;
                    }

                    if ($request->delivery_date_request[$item->id][$index0] != null) {
                        $date2 = new DateTime($request->delivery_date_request[$item->id][$index0]);
                        $date_delivery_new = $date2->format('Y-m-d');
                    } else {
                        $date_delivery_new = null;
                    }
                    $import_car_lines_save = ImportCarLine::find($import_car_line->id);

                    if (($import_car_lines_save->engine_no != "" || null) && ($import_car_lines_save->chassis_no != "" || null) && ($import_car_lines_save->install_date != "" || null)) {
                        if ($request->status_car_line[$item->id][$index0] == ImportCarLineStatusEnum::CONFIRM_DATA) {
                            $import_car_lines_save->status = ImportCarLineStatusEnum::CONFIRM_DATA;
                        } else if ($request->status_car_line[$item->id][$index0] == ImportCarLineStatusEnum::REJECT_DATA && $import_car_lines_save->status == ImportCarLineStatusEnum::REJECT_DATA) {
                            if (($import_car_lines_save->engine_no != $request->engine_no[$item->id][$index0]) || ($import_car_lines_save->chassis_no != $request->chassis_no[$item->id][$index0]) || ($import_car_lines_save->install_date != $date_install_new)) {
                                if (($request->engine_no[$item->id][$index0] == "" || null) || ($request->chassis_no[$item->id][$index0] == "" || null) || ($date_install_new == "" || null)) {
                                    $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING;
                                } else {
                                    $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING_REVIEW;
                                    $import_car_lines_save->reject_reason = NULL;
                                }
                            } else {
                                $import_car_lines_save->status = ImportCarLineStatusEnum::REJECT_DATA;
                            }
                        } else if ($request->status_car_line[$item->id][$index0] == ImportCarLineStatusEnum::REJECT_DATA) {
                            $import_car_lines_save->status = ImportCarLineStatusEnum::REJECT_DATA;
                            $import_car_lines_save->reject_reason = $request->reject_reason_text[$item->id][$index0] == null ? '' : $request->reject_reason_text[$item->id][$index0];
                        } else if (($request->engine_no[$item->id][$index0] == "" || null) || ($request->chassis_no[$item->id][$index0] == "" || null) || ($date_install_new == "" || null)) {
                            $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING;
                        } else {
                            if ($request->status_draft[$item->id][$index0] == ImportCarLineStatusEnum::CONFIRM_DATA) {
                                $import_car_lines_save->status = ImportCarLineStatusEnum::CONFIRM_DATA;
                            }else if ($request->status_draft[$item->id][$index0] == ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA) {
                                $import_car_lines_save->status = ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA;
                                $import_car_lines_save->verification_date = Carbon::now();
                            } else {
                                $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING_REVIEW;
                            }
                        }
                    } else {
                        if (($request->engine_no[$item->id][$index0] != "" || null) && ($request->chassis_no[$item->id][$index0] != "" || null) && ($date_install_new != "" || null)) {
                            $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING_REVIEW;
                        } else {
                            $import_car_lines_save->status = ImportCarLineStatusEnum::PENDING;
                        }
                    }

                    $import_car_lines_save->engine_no = $request->engine_no[$item->id][$index0] == null ? '' : $request->engine_no[$item->id][$index0];
                    $import_car_lines_save->chassis_no = $request->chassis_no[$item->id][$index0] == null ? '' : $request->chassis_no[$item->id][$index0];
                    $import_car_lines_save->install_date = $date_install_new;
                    $import_car_lines_save->delivery_date = $date_delivery_new;
                    $import_car_lines_save->delivery_location = $request->delivery_place[$item->id][$index0] == null ? '' : $request->delivery_place[$item->id][$index0];
                    $import_car_lines_save->save();
                   

                    // }
                    $index0++;
                }
            }
        }

        return $this->responseValidateSuccess('');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ImportCar $import_car_dealer, Request $request)
    {
        $id = substr($request->path(), 0, strrpos($request->path(), '/'));
        $id = strstr($id, '/');
        $id = str_replace('/', '', $id);
        $id = $import_car_dealer->id;
        $purchase_requisition_cars = PurchaseOrderLine::select('purchase_order_lines.id', 'purchase_order_lines.name', 'purchase_order_lines.amount', 'purchase_order_lines.subtotal', 'purchase_order_lines.discount', 'purchase_order_lines.total')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'purchase_requisition_lines.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'purchase_requisition_lines.car_class_id')
            ->addSelect('car_colors.name as color_name','car_classes.full_name as class_name')
            ->where('import_cars.id', $id)
            ->get();
        
        $pr_detail = PurchaseRequisition::leftjoin('purchase_orders', 'purchase_orders.pr_id', '=', 'purchase_requisitions.id')->where('purchase_orders.id', $import_car_dealer->po_id)->first();
        $po_detail = PurchaseOrder::select('purchase_orders.*', 'purchase_orders.remark as po_remark')->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')->where('import_cars.id', $import_car_dealer->id)->first();
        $import_car = ImportCar::find($id);
        $arr_ob = array();
        for ($i = 0; $i < count($purchase_requisition_cars); $i++) {
            $arr_ob[$purchase_requisition_cars[$i]->id] = [];
        }

        $arr_ob_2 = array();
        for ($i = 0; $i < count($purchase_requisition_cars); $i++) {
            $arr_ob_2[$purchase_requisition_cars[$i]->id] = [];
        }

        $import_car_lines = ImportCarLine::all();
        $index_loop = 0;
        foreach ($purchase_requisition_cars as $index => $item) {
            $index0 = 0;
            foreach ($import_car_lines as $index2 => $item2) {
                if (strcmp($item->id, $item2->po_line_id) == 0) {
                   
                        if ($item2->install_date != null) {
                            $date = new DateTime($item2->install_date);
                            $date_install_new = $date->format('d-m-Y');
                        } else {
                            $date_install_new = null;
                        }

                        if ($item2->delivery_date != null) {
                            $date_delivery = new DateTime($item2->delivery_date);
                            $date_delivery_new = $date_delivery->format('d-m-Y');
                        } else {
                            $date_delivery_new = null;
                        }

                        if ($item2->verification_date != null) {
                            $verification_date_format = new DateTime($item2->verification_date);
                            $verification_date_new = get_thai_date_format($verification_date_format,'d/m/Y H:i');
                        } else {
                            $verification_date_new = null;
                        }
                        if (!empty($item2->engine_no) || !empty($item2->chassis_no) || !empty($item2->install_date) || !empty($item2->delivery_date) || !empty($request->json_object[$index_loop]['หมายเลขเครื่องยนต์']) || !empty($request->json_object[$index_loop]['เลขตัวถัง']) || !empty($request->json_object[$index_loop]['วันที่พร้อมส่งมอบ'])) {
                            if ($request->json_object == null || '') {
                                $arr_ob[$item->id][$index0] = (object) array("engine_no" => $item2->engine_no, "chassis_no" => $item2->chassis_no, "installation_completed_date" => $date_install_new, "delivery_date" => $date_delivery_new,"delivery_place" =>$item2->delivery_location, 'id' => $item2->id, 'status' => $item2->status,'status_draft' => $item2->status,'status_delivery' => $item2->status_delivery, 'reject_reason' => $item2->reject_reason, 'verification_date' => $verification_date_new);
                            } else {
                                if ($item2->status != ImportCarLineStatusEnum::CONFIRM_DATA && $item2->status != ImportCarLineStatusEnum::VENDOR_CONFIRM_DATA) {
                                    if (!empty($request->json_object[$index_loop]['วันที่พร้อมส่งมอบ'])) {
                                        $date = new DateTime($request->json_object[$index_loop]['วันที่พร้อมส่งมอบ']);
                                        $date_install_new = $date->format('d-m-Y');
                                    } else {
                                        $date_install_new = null;
                                    }
                                    $arr_ob[$item->id][$index0] = (object) array("engine_no" => !empty($request->json_object[$index_loop]['หมายเลขเครื่องยนต์']) ? $request->json_object[$index_loop]['หมายเลขเครื่องยนต์'] : '', "chassis_no" => !empty($request->json_object[$index_loop]['เลขตัวถัง'])  ? $request->json_object[$index_loop]['เลขตัวถัง'] : '', "installation_completed_date" => $date_install_new, "delivery_date" => $date_delivery_new,"delivery_place" =>$item2->delivery_location , 'id' => $item2->id, 'status' => $item2->status,'status_draft' => $item2->status,'status_delivery' => $item2->status_delivery, 'reject_reason' => $item2->reject_reason, 'verification_date' => $verification_date_new);
                                    $index_loop++;
                                } else {
                                    $arr_ob[$item->id][$index0] = (object) array("engine_no" => $item2->engine_no, "chassis_no" => $item2->chassis_no, "installation_completed_date" => $date_install_new, "delivery_date" => $date_delivery_new,"delivery_place" =>$item2->delivery_location, 'id' => $item2->id, 'status' => $item2->status,'status_draft' => $item2->status,'status_delivery' => $item2->status_delivery, 'reject_reason' => $item2->reject_reason, 'verification_date' => $verification_date_new);
                                    $index_loop++;
                                }
                            }
                        } else {
                            $arr_ob[$item->id][$index0] = (object) array("engine_no" => '', "chassis_no" => '', "installation_completed_date" => '', "delivery_date" => '',"delivery_place" =>'', 'id' => $item2->id, 'status' => ImportCarLineStatusEnum::PENDING,'status_draft' => ImportCarLineStatusEnum::PENDING,'status_delivery' => $item2->status_delivery, 'reject_reason' => $item2->reject_reason, 'verification_date' => $verification_date_new);
                            $index_loop++;
                        }
                    // }
                    $index0++;
                }
            }
        }
        $accessory_pr = PurchaseRequisitionLineAccessory::leftjoin('accessories', 'accessories.id', '=', 'purchase_requisition_line_accessories.accessory_id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_requisition_line_accessories.purchase_requisition_line_id')
            ->leftjoin('purchase_order_lines', 'purchase_order_lines.pr_line_id', '=', 'purchase_requisition_lines.id')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            ->select('accessories.name as accessory_name', 'accessories.version', 'purchase_requisition_line_accessories.amount as pr_line_acc_amount', 'purchase_order_lines.id as po_line_id')
            ->where('import_cars.id', $id)
            ->get();

        $object =  $arr_ob;
        $purchase_order_dealer_list = [];
        $page_title = __('lang.edit') . __('import_cars.page_title');
        if ($request->json_object == null) {
            return view('admin.import-cars.form', [
                'object' => $object,
                'd' => $po_detail,
                'accessory_pr' => $accessory_pr,
                'page_title' => $page_title,
                'import_car' => $import_car,
                'purchase_requisition_cars' => $purchase_requisition_cars,
                'purchase_order_dealer_list' => $import_car,
                'test' => 4,
                'arr_ob_2' => $arr_ob_2,
                'pr_detail' => $pr_detail,
            ]);
        } else {
            return response()->json([
                'success' => $object,
                'message' => 'ok',
                'redirect' => view('admin.import-cars.form')
            ]);
        }
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

    public function export(ImportCar $import_car_dealer)
    {
        $po_line = PurchaseOrderLine::select('purchase_order_lines.id', 'purchase_order_lines.name', 'purchase_order_lines.amount', 'purchase_order_lines.subtotal', 'purchase_order_lines.discount', 'purchase_order_lines.total')
            ->leftjoin('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_lines.purchase_order_id')
            ->leftjoin('import_cars', 'import_cars.po_id', '=', 'purchase_orders.id')
            ->leftjoin('purchase_requisition_lines', 'purchase_requisition_lines.id', '=', 'purchase_order_lines.pr_line_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'purchase_requisition_lines.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'purchase_requisition_lines.car_class_id')
            ->addSelect(
                'car_colors.name as color_name',
                'car_classes.full_name as class_name',
                'purchase_orders.po_no as po_worksheet_no',
                )->where('import_cars.id', $import_car_dealer->id)
            ->get();
        return Excel::download(new ExportImportCar($po_line), 'template.xlsx');
    }
}
