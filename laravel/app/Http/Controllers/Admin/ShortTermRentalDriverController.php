<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Actions;
use App\Enums\RentalStateEnum;
use App\Enums\Resources;
use App\Enums\TransferTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\ProductAdditional;
use App\Models\Rental;
use App\Models\RentalCheckIn;
use App\Models\RentalDriver;
use App\Rules\CitizenRule;
use App\Rules\TelRule;
use App\Traits\RentalDriverTrait;
use App\Traits\RentalTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ShortTermRentalDriverController extends Controller
{
    use RentalTrait, RentalDriverTrait;

    public function edit(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental = Rental::find($rental_id);
        if (empty($rental)) {
            return redirect()->route('admin.short-term-rentals.index');
        }

        $customer_id = $rental?->customer_id;
        $rental_driver_list = [];
        $product_additional_list = [];
        $product_transport_list = [];
        $service_type = ($rental?->serviceType) ? $rental?->serviceType?->service_type : null;
        $allow_driver = RentalTrait::canAddDriver($service_type);
        $allow_product_additional = RentalTrait::canAddProductAdditional($service_type);
        $allow_product_transport = RentalTrait::canAddProductTransport($service_type);
        if ($allow_driver) {
            $rental_driver_list = RentalTrait::getRentalDriverList($rental?->id);
            if (sizeof($rental_driver_list) === 0) {
                $this->copyCustomerDriverToRentalDriver($customer_id, $rental?->id);
            }
        }
        if ($allow_product_additional) {
            //$product_additional_list = RentalTrait::getRentalLineProductAdditionalList($rental?->id);
        }
        $product_transport_list = null;
        $product_transport_return_list = null;
        if ($allow_product_transport) {
            $product_transport_list = RentalTrait::getRentalProductTransportList($rental?->id, TransferTypeEnum::OUT);
            $product_transport_return_list = RentalTrait::getRentalProductTransportReturnList($rental?->id, TransferTypeEnum::IN);
            $product_transport_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file');
                $product_files = get_medias_detail($product_file_medias);
                $product_files = collect($product_files)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files = $product_files;
                return $item;
            });

            $product_transport_return_list->map(function ($item) {
                // $item->product_files = null;
                $product_file_medias = $item->getMedia('product_file_return');
                $product_files_return = get_medias_detail($product_file_medias);
                $product_files_return = collect($product_files_return)->map(function ($item) {
                    $item['formated'] = true;
                    $item['saved'] = true;
                    $item['raw_file'] = null;
                    return $item;
                })->toArray();
                $item->product_files_return = $product_files_return;
                return $item;
            });
        }
        $cars = RentalTrait::getRentalLineCarList($rental_id);
        $product_additional_price_list = ProductAdditional::select('id', 'price')->pluck('price', 'id')->toArray();
        $driver_list = RentalTrait::getRentalDriverList($rental->id);
        $page_title = __('lang.edit') . __('short_term_rentals.sheet');
        $product_additional_carousel = $this->getProductTransportListData()->chunk(4);
        return view('admin.short-term-rental-driver.form', [
            'rental_id' => $rental_id,
            'd' => $rental,
            'driver_list' => $driver_list,
            'cars' => $cars,
            'rental' => $rental,
            'allow_product_additional' => $allow_product_additional,
            'allow_driver' => $allow_driver,
            'product_additional_list' => $product_additional_list,
            'allow_product_transport' => $allow_product_transport,
            'product_transport_list' => $product_transport_list,
            'product_transport_return_list' => $product_transport_return_list,
            'product_additional_price_list' => $product_additional_price_list,
            'product_additional_carousel' => $product_additional_carousel,
            'page_title' => $page_title,
        ]);
    }

    public function getProductTransportListData()
    {
        return collect(
            [
                (object)[
                    'type' => 'car',
                    'title' => 'รถยนต์',
                    'sub_title' => 'รองรับสูงสุด 1 คัน',
                ],
                (object)[
                    'type' => 'broken-car',
                    'title' => 'รถเสีย',
                    'sub_title' => 'รองรับสูงสุด 1 คัน',
                ],
                (object)[
                    'type' => 'big-bike',
                    'title' => 'บิ๊กไบค์',
                    'sub_title' => 'รองรับสูงสุด 5 คัน',
                ],
                (object)[
                    'type' => 'industrial-products',
                    'title' => 'สินค้าอุตสาหกรรม',
                    'sub_title' => 'รองรับตามขนาดบรรทุก',
                ],
                (object)[
                    'type' => 'product-more',
                    'title' => 'อื่นๆ',
                    'sub_title' => 'รองรับตามขนาดบรรทุก',
                ],
            ]
        );
    }

    public function store(Request $request)
    {
        $this->authorize(Actions::Manage . '_' . Resources::ShortTermRental);
        $rental_id = $request->rental_id;
        $rental = Rental::find($rental_id);

        $validator = Validator::make($request->all(), [
            'product_additionals.*.product_additional_id' => 'required',
            'product_additionals.*.amount' => 'required|not_in:0',
        ], [], [
            'product_additionals.*.product_additional_id' => 'ชื่อออฟชั่นเสริม',
            'product_additionals.*.amount' => 'จำนวนออฟชั่นเสริม'
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            return $this->responseValidateFailed($validator);
        }
        if (!empty($request->drivers)) {
            $request->drivers = tel_format_in_array($request->drivers);
            $validator = Validator::make($request->drivers, [
                '*.name' => 'required',
                '*.tel' => [new TelRule],
                '*.citizen_id' => [new CitizenRule],
            ], [], [
                '*.name' => 'ชื่อผู้ขับขี่',
                '*.tel' => 'เบอร์โทร',
                '*.citizen_id' => 'บัตรประชาชน',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        if ($request->location) {
            $validator = Validator::make($request->location, [
                '*.*.location_name' => 'required_if:*.*.location_id,null',
                '*.*.location_id' => 'required_if:*.*.location_name,null',
                '*.*.arrived_at' => 'required',
                '*.*.departured_at' => 'required',
            ], [], [
                '*.*.location_name' => 'สถานที่',
                '*.*.location_id' => 'สถานที่',
                '*.*.arrived_at' => 'วัน เวลาที่เริ่มต้นแวะพัก',
                '*.*.departured_at' => 'วัน เวลาที่สิ้นสุดแวะพัก',
            ]);
            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }
        if (!$rental->serviceType) {
            return $this->responseWithCode(false, __('lang.not_found'), null, 422);
        }
        if (RentalTrait::canAddDriver($rental->serviceType->service_type)) {
            $car_id = $request->car_id;
            $pending_delete_license_files = $request->pending_delete_license_files;
            $pending_delete_citizen_files = $request->pending_delete_citizen_files;
            $this->deleteRentalDriverFiles($pending_delete_license_files);
            $this->deleteRentalDriverFiles($pending_delete_citizen_files);

            $this->saveRentalDriver($request, $rental_id);
        }
        //$rental_bill = RentalBill::where('rental_id', $rental_id)->first();

        if (RentalTrait::canAddProductAdditional($rental->serviceType->service_type)) {
            $this->saveRentalProductAdditional($request, $rental_id, $rental);
        }

        if (RentalTrait::canAddProductTransport($rental->serviceType->service_type)) {
            if (!isset($request->product_transport)) {
                return $this->responseWithCode(false, 'กรุณากรอกข้อมูลสินค้านำส่งอย่างน้อยหนึ่งสินค้า', null, 422);
            } else if (!isset($request->product_transport_return)) {
                return $this->responseWithCode(false, 'กรุณากรอกข้อมูลสินค้านำกลับอย่างน้อยหนึ่งสินค้า', null, 422);
            } else {
                $this->saveRentalProductTransport($request, $rental_id);
            }
        }

        $rental->remark = $request->rental_remark;
        $rental->objective = $request->objective;
        $rental->rental_state = RentalStateEnum::PROMOTION;
        $rental->save();
        if ($request->location_del) {
            RentalCheckIn::whereIn('id', $request->location_del)->delete();
        }
        if ($request->location) {
            foreach ($request->location as $car_id => $value_location) {
                if (!empty($value_location)) {
                    foreach ($value_location as $key => $value) {
                        $optionals = [];
                        $optionals['arrived_at'] = $value['arrived_at'] ?? null;
                        $optionals['departured_at'] = $value['departured_at'] ?? null;
                        $optionals['location_name'] = $value['location_name'] ?? null;
                        $optionals['lat'] = $value['lat'] ?? null;
                        $optionals['lng'] = $value['lng'] ?? null;
                        $id = $value['id'] ?? null;
                        $location_id = $value['location_id'] ?? null;
                        RentalTrait::createRentalLocation($id, $rental_id, $car_id, $location_id, $optionals);
                    }
                }
            }
        }
        $redirect_route = route('admin.short-term-rental.promotion.edit', [
            'rental_id' => $rental_id,
        ]);
        return $this->responseValidateSuccess($redirect_route);
    }

    function deleteRentalDriverFiles($pending_files)
    {
        if ((!empty($pending_files)) && (sizeof($pending_files) > 0)) {
            foreach ($pending_files as $media_id) {
                $media = Media::find($media_id);
                if ($media && $media->model_id) {
                    $model = RentalDriver::find($media->model_id);
                    $model->deleteMedia($media->id);
                }
            }
        }
        return true;
    }

    public function getDataDriver(Request $request)
    {
        $rental_id = $request->rental_id;
        $rental = Rental::find($rental_id);
        if ($rental) {
            $data['driver_name'] = ($rental) ? $rental->customer_name : null;
            $data['driver_tel'] = ($rental) ? $rental->customer_tel : null;;
            $data['driver_email'] = ($rental) ? $rental->customer_email : null;
            return [
                'success' => true,
                'data' => $data
            ];
        }
    }
}
