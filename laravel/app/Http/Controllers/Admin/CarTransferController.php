<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CarTransfer;

class CarTransferController extends Controller
{
    public function index(Request $request)
    {
        $status = $this->getStatus();
        $license_category = $this->getLicenseCategory();

        return view('admin.car-transfers.index', [
            's' => $request->s,
            'status' => $status,
            'license_category' => $license_category,
        ]);
    }

    public function create()
    {
        $d = new CarTransfer();
        $d->open_date = date('Y-m-d');
        $license_category = $this->getLicenseCategory();
        $rental_type_list = PurchaseRequisitionController::getRentalType();

        $page_title = __('car_transfers.license_table');
        return view('admin.car-transfers.form',  [
            'd' => $d,
            'page_title' => $page_title,
            'license_category' => $license_category,
            'rental_type_list' => $rental_type_list,
        ]);
    }

    // public function edit()
    // {
    //
    // }

    public function store(Request $request)
    {
        $redirect_route = route('admin.car-transfers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function destroy()
    {
        $redirect_route = route('admin.car-transfers.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function getLicenseCategory()
    {
        $license_category = collect([
            (object) [
                'id' => '1',
                'name' => 'โอนย้ายในคลัง',
                'value' => '1'
            ],
            (object) [
                'id' => '2',
                'name' => 'โอนย้ายนอกคลัง',
                'value' => '2'
            ]
        ]);
        return $license_category;
    }

    public function getStatus()
    {
        $status = collect([
            (object) [
                'id' => 0,
                'name' => __('car_transfers.status_' . 0 . '_text'),
                'value' => 0,
            ],
            (object) [
                'id' => 1,
                'name' => __('car_transfers.status_' . 1 . '_text'),
                'value' => 1,
            ],
            (object) [
                'id' => 2,
                'name' => __('car_transfers.status_' . 2 . '_text'),
                'value' => 2,
            ],
            (object) [
                'id' => 3,
                'name' => __('car_transfers.status_' . 3 . '_text'),
                'value' => 3,
            ],
        ]);
        return $status;
    }
}
