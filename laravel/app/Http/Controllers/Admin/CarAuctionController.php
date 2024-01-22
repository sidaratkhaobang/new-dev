<?php

namespace App\Http\Controllers\Admin;

use App\Classes\NotificationManagement;
use App\Enums\Actions;
use App\Enums\Resources;
use App\Enums\SellingPriceStatusEnum;
use App\Enums\CarAuctionStatusEnum;
use App\Enums\CarEnum;
use App\Enums\DepartmentEnum;
use App\Enums\SelfDriveTypeEnum;
use App\Enums\NotificationScopeEnum;
use App\Http\Controllers\Controller;
use App\Models\AuctionPlace;
use App\Models\Car;
use App\Models\CarAuction;
use App\Models\CarClass;
use App\Models\CarParkTransfer;
use App\Models\DrivingJob;
use App\Models\SellingPriceLine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\CarAuctionTrait;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class CarAuctionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize(Actions::View . '_' . Resources::CarAuction);
        $car_id = $request->car_id;
        $car_class_id = $request->car_class_id;
        $auction_place_id = $request->auction_place_id;
        $status_id = $request->status_id;
        $from_send_auction = $request->from_send_auction;
        $to_send_auction = $request->to_send_auction;
        $from_auction = $request->from_auction;
        $to_auction = $request->to_auction;
        $from_sale = $request->from_sale;
        $to_sale = $request->to_sale;
        $car_book = $request->car_book;

        $list = CarAuction::leftjoin('cars', 'cars.id', '=', 'car_auctions.car_id')
            ->leftjoin('leasings', 'leasings.id', '=', 'cars.leasing_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('auction_places', 'auction_places.id', '=', 'car_auctions.auction_id')
            ->where('leasings.is_true_leasing', STATUS_ACTIVE)
            ->when($car_id, function ($query) use ($car_id) {
                $query->where('car_auctions.car_id', $car_id);
            })
            ->when($car_class_id, function ($query) use ($car_class_id) {
                $query->where('cars.car_class_id', $car_class_id);
            })
            ->when($status_id, function ($query) use ($status_id) {
                $query->where('car_auctions.status', $status_id);
            })
            ->when($auction_place_id, function ($query) use ($auction_place_id) {
                $query->where('car_auctions.auction_id', $auction_place_id);
            })
            ->when($from_send_auction, function ($query) use ($from_send_auction) {
                $query->whereDate('car_auctions.send_auction_date', '>=', $from_send_auction);
            })
            ->when($to_send_auction, function ($query) use ($to_send_auction) {
                $query->whereDate('car_auctions.send_auction_date', '<=', $to_send_auction);
            })
            ->when($from_auction, function ($query) use ($from_auction) {
                $query->whereDate('car_auctions.auction_date', '>=', $from_auction);
            })
            ->when($to_auction, function ($query) use ($to_auction) {
                $query->whereDate('car_auctions.auction_date', '<=', $to_auction);
            })
            ->when($from_sale, function ($query) use ($from_sale) {
                $query->whereDate('car_auctions.sale_date', '>=', $from_sale);
            })
            ->when($to_sale, function ($query) use ($to_sale) {
                $query->whereDate('car_auctions.sale_date', '<=', $to_sale);
            })
            ->when($car_book, function ($query) use ($car_book) {
                $query->whereIn('car_auctions.id', $car_book);
            })
            ->select(
                'car_auctions.*',
                'car_classes.full_name as car_class_name',
                'cars.license_plate',
                'cars.chassis_no',
                'cars.engine_no',
                'cars.current_mileage as mileage',
                'car_classes.manufacturing_year as car_class_year',
                'auction_places.name as auction_name',
            )
            ->sortable('license_plate')
            ->paginate(PER_PAGE);
        $list->map(function ($item) {
            $driving_job = DrivingJob::where('job_id', $item->id)->where('job_type', CarAuction::class)->latest()->first();
            if ($driving_job) {
                $car_park_transfer = CarParkTransfer::where('driving_job_id', $driving_job->id)->latest()->first();
                if ($car_park_transfer) {
                    $item->car_park_transfer = $car_park_transfer->worksheet_no;
                    $item->car_park_link = route('admin.car-park-transfers.show', ['car_park_transfer' => $car_park_transfer->id]);
                }
            }
            return $item;
        });
        $total_car = CarAuction::whereNotIn('status', [CarAuctionStatusEnum::SOLD_OUT])->count();
        $total_ready = CarAuctionTrait::getTotal($request, CarAuctionStatusEnum::READY_AUCTION);
        $total_pending = CarAuctionTrait::getTotal($request, CarAuctionStatusEnum::PENDING_AUCTION);
        $total_sale = CarAuctionTrait::getTotal($request, CarAuctionStatusEnum::SOLD_OUT);
        $is_need = CarAuctionTrait::getIsNeed();
        $auction_place = CarAuctionTrait::getAuctionPlace();
        $status_list = CarAuctionTrait::getStatusAuction();

        $car_name = null;
        if ($car_id) {
            $car = Car::find($car_id);
            if ($car && $car->license_plate) {
                $car_name = $car?->license_plate ?? null;
            } else if ($car && $car->engine_no) {
                $car_name = __('inspection_cars.engine_no') . ' ' . $car?->engine_no ?? null;
            } else if ($car && $car->chassis_no) {
                $car_name = __('inspection_cars.chassis_no') . ' ' . $car?->chassis_no ?? null;
            }
        }

        $car_class_name = null;
        if ($car_class_id) {
            $car_class = CarClass::find($car_class_id);
            $car_class_name = $car_class?->full_name ?? null;
        }

        $auction_place_name = null;
        if ($auction_place_id) {
            $auction = AuctionPlace::find($auction_place_id);
            $auction_place_name = $auction?->name ?? null;
        }

        $page_title =  __('car_auctions.page_title');
        return view('admin.car-auctions.index', [
            'list' => $list,
            'page_title' => $page_title,
            'total_car' => $total_car,
            'total_ready' => $total_ready,
            'total_pending' => $total_pending,
            'total_sale' => $total_sale,
            'is_need' => $is_need,
            'auction_place' => $auction_place,
            'car_id' => $car_id,
            'car_class_id' => $car_class_id,
            'status_id' => $status_id,
            'auction_place_id' => $auction_place_id,
            'from_send_auction' => $from_send_auction,
            'to_send_auction' => $to_send_auction,
            'from_auction' => $from_auction,
            'to_auction' => $to_auction,
            'from_sale' => $from_sale,
            'to_sale' => $to_sale,
            'car_name' => $car_name,
            'car_class_name' => $car_class_name,
            'auction_place_name' => $auction_place_name,
            'status_list' => $status_list,
        ]);
    }

    public function edit(CarAuction $car_auction)
    {
        $this->authorize(Actions::Manage . '_' . Resources::CarAuction);
        // $car_auction->depreciation_age = $car_auction->depreciation_age ? $car_auction->depreciation_age : "60";
        $car = CarAuctionTrait::getCarInfo($car_auction->car_id);
        $car_accessory = CarAuctionTrait::getCarAccessorie($car_auction->car_id);
        $auction_place = AuctionPlace::find($car_auction->auction_id);
        $auction = null;
        if ($auction_place) {
            $auction = $auction_place->name;
        }

        $selling_price = SellingPriceLine::where('car_id', $car_auction->car_id)->where('status', SellingPriceStatusEnum::CONFIRM)->first();
        $car_auction->sale_price = ($selling_price && $selling_price->price) ? $selling_price->price : 0;
        $document_sale = $car_auction->getMedia('document_sale');
        $document_sale = get_medias_detail($document_sale);
        $link_list = CarAuctionTrait::getLinkList($car_auction);

        $page_title =  __('lang.edit') . __('car_auctions.page_title');
        return view('admin.car-auctions.form', [
            'd' => $car_auction,
            'page_title' => $page_title,
            'car' => $car,
            'car_accessory' => $car_accessory,
            'auction' => $auction,
            'document_sale' => $document_sale,
            'link_list' => $link_list,
        ]);
    }

    public function show(CarAuction $car_auction)
    {
        $this->authorize(Actions::View . '_' . Resources::CarAuction);
        $car = CarAuctionTrait::getCarInfo($car_auction->car_id);
        $car_accessory = CarAuctionTrait::getCarAccessorie($car_auction->car_id);
        $auction_place = AuctionPlace::find($car_auction->auction_id);
        $auction = null;
        if ($auction_place) {
            $auction = $auction_place->name;
        }

        $selling_price = SellingPriceLine::where('car_id', $car_auction->car_id)->where('status', SellingPriceStatusEnum::CONFIRM)->first();
        $car_auction->sale_price = ($selling_price && $selling_price->price) ? $selling_price->price : 0;
        $document_sale = $car_auction->getMedia('document_sale');
        $document_sale = get_medias_detail($document_sale);
        $link_list = CarAuctionTrait::getLinkList($car_auction);

        $page_title =  __('lang.view') . __('car_auctions.page_title');
        return view('admin.car-auctions.form', [
            'd' => $car_auction,
            'page_title' => $page_title,
            'car' => $car,
            'car_accessory' => $car_accessory,
            'auction' => $auction,
            'document_sale' => $document_sale,
            'link_list' => $link_list,
            'view' => true,
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if (strcmp($request->prev_status, CarAuctionStatusEnum::SEND_AUCTION) == 0) {
            $validator = Validator::make($request->all(), [
                'auto_grate' => [
                    'required',
                ],
                'nature' => [
                    'required',
                ],
                'redbook' => [
                    'required',
                ],
                'auction_price' => [
                    'required',
                ],
                'tls_price' => [
                    'required',
                ],
            ], [], [
                'auto_grate' => __('car_auctions.auto_grate'),
                'nature' => __('car_auctions.nature'),
                'redbook' => __('car_auctions.redbook'),
                'auction_price' => __('car_auctions.auction_price'),
                'tls_price' => __('car_auctions.tls_price'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        if (strcmp($request->prev_status, CarAuctionStatusEnum::PENDING_AUCTION) == 0) {
            $validator = Validator::make($request->all(), [
                'auction_date_show' => [
                    'required',
                ],
                'sale_date_show' => [
                    'required',
                ],
                'selling_price' => [
                    'required',
                ],
                'profit_loss' => [
                    'required',
                ],
                'tax_refund' => [
                    'required',
                ],
                'customer_name' => [
                    'required',
                ],
                'customer_address' => [
                    'required',
                ],
            ], [], [
                'auction_date_show' => __('car_auctions.auction_date'),
                'sale_date_show' => __('car_auctions.sale_date'),
                'selling_price' => __('car_auctions.selling_price'),
                'profit_loss' => __('car_auctions.profit_loss'),
                'tax_refund' => __('car_auctions.tax_refund'),
                'customer_name' => __('car_auctions.customer_name'),
                'customer_address' => __('car_auctions.customer_address'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        $car_auction = CarAuction::firstOrNew(['id' => $request->id]);
        $car_auction->depreciation_age = $request->depreciation_age;
        $car_auction->depreciation_month = transform_float($request->depreciation_month);
        $car_auction->depreciation_age_remain = $request->depreciation_age_remain;
        $car_auction->depreciation_current = transform_float($request->depreciation_current);
        $car_auction->target = transform_float($request->target);
        $car_auction->median_price = transform_float($request->median_price);

        if (strcmp($request->type_modal, 'cmi_vmi') == 0) {
            $car_auction->close_cmi_vmi_date = (!empty($request->close_cmi_vmi_date) ? date('Y-m-d H:i:s', strtotime($request->close_cmi_vmi_date)) : date('Y-m-d H:i:s'));
        }
        if (strcmp($request->type_modal, 'pick_up_key') == 0) {
            $car_auction->pick_up_date = (!empty($request->pick_up_date) ? date('Y-m-d H:i:s', strtotime($request->pick_up_date)) : date('Y-m-d H:i:s'));
        }
        if (strcmp($request->prev_status, CarAuctionStatusEnum::SEND_AUCTION) == 0) {
            $car_auction->auto_grate = $request->auto_grate;
            $car_auction->nature = $request->nature;
            $car_auction->remark = $request->remark;
            $car_auction->redbook = transform_float($request->redbook);
            $car_auction->auction_price = transform_float($request->auction_price);
            $car_auction->tls_price = transform_float($request->tls_price);
            $car_auction->reason = $request->reason;
        }
        if (strcmp($request->prev_status, CarAuctionStatusEnum::PENDING_AUCTION) == 0) {
            $car_auction->selling_price = transform_float($request->selling_price);
            $car_auction->vat_selling_price = transform_float($request->vat_selling_price);
            $car_auction->total_selling_price = transform_float($request->total_selling_price);
            $car_auction->profit_loss = transform_float($request->profit_loss);
            $car_auction->tax_refund = transform_float($request->tax_refund);
            $car_auction->other_price = transform_float($request->other_price);
            $car_auction->customer = $request->customer_name;
            $car_auction->address = $request->customer_address;
        }
        $car_auction->auction_date = $request->auction_date_show ? $request->auction_date_show : $request->auction_date;
        $car_auction->sale_date = $request->sale_date_show ? $request->sale_date_show : $request->sale_date;
        $car_auction->status = $request->status;
        $car_auction->save();

        if (strcmp($request->prev_status, CarAuctionStatusEnum::PENDING_SALE) == 0) {
            if (is_null($car_auction->close_cmi_vmi_date) || (is_null($car_auction->pick_up_date))) {
                $car_auction->status = CarAuctionStatusEnum::PENDING_SALE;
                $car_auction->save();
            }
        }

        if ((strcmp($request->prev_status, CarAuctionStatusEnum::PENDING_SALE) == 0) && (($car_auction->close_cmi_vmi_date) != null)) {
            $cancel_insurance = CarAuctionTrait::createCancelInsurance($car_auction);
        }

        if (strcmp($car_auction->status, CarAuctionStatusEnum::SOLD_OUT) == 0) {
            $car = Car::find($car_auction->car_id);
            if ($car) {
                $car->status = CarEnum::SOLD_OUT;
                $car->save();
            }
        }

        if ($request->document_sale__pending_delete_ids) {
            $pending_delete_ids = $request->document_sale__pending_delete_ids;
            if ((is_array($pending_delete_ids)) && (sizeof($pending_delete_ids) > 0)) {
                foreach ($pending_delete_ids as $media_id) {
                    $car_auction->deleteMedia($media_id);
                }
            }
        }

        if ($request->hasFile('document_sale')) {
            foreach ($request->file('document_sale') as $image) {
                if ($image->isValid()) {
                    $car_auction->addMedia($image)->toMediaCollection('document_sale');
                }
            }
        }

        $redirect_route = route('admin.car-auctions.index');
        return $this->responseValidateSuccess($redirect_route);
    }

    public function saveStatusCancel(Request $request)
    {
        if (empty($request->close_cmi_vmi_date)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกวันที่ขอยกเลิกประกัน / พรบ.',
            ], 422);
        }
        if (empty($request->arr_cancel_cmi_vmi)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรถที่ต้องการยกเลิกประกัน / พรบ.',
            ], 422);
        }

        if ($request->arr_cancel_cmi_vmi > 0) {
            foreach ($request->arr_cancel_cmi_vmi as $item) {
                $car_auction = CarAuction::find($item['id']);
                if ($car_auction) {
                    if (strcmp($request->prev_status, CarAuctionStatusEnum::PENDING_SALE) == 0) {
                        $car_auction->close_cmi_vmi_date = (!empty($request->close_cmi_vmi_date) ? date('Y-m-d H:i:s', strtotime($request->close_cmi_vmi_date)) : date('Y-m-d H:i:s'));
                    }
                    if ((is_null($car_auction->pick_up_date))) {
                        $car_auction->status = CarAuctionStatusEnum::PENDING_SALE;
                    } else {
                        $car_auction->status = CarAuctionStatusEnum::READY_AUCTION;
                    }
                    $car_auction->save();

                    if ((strcmp($request->prev_status, CarAuctionStatusEnum::PENDING_SALE) == 0) && (($car_auction->close_cmi_vmi_date) != null)) {
                        $cancel_insurance = CarAuctionTrait::createCancelInsurance($car_auction);
                    }
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.car-auctions.index'),
        ]);
    }

    public function saveStatusKey(Request $request)
    {
        if (empty($request->pick_up_date)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกวันที่ต้องการกุญแจ',
            ], 422);
        }
        if (empty($request->arr_pick_up_key)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรถที่ต้องการเบิกกุญแจ',
            ], 422);
        }

        if ($request->arr_pick_up_key > 0) {
            foreach ($request->arr_pick_up_key as $item) {
                $car_auction = CarAuction::find($item['id']);
                if ($car_auction) {
                    if (strcmp($request->prev_status, CarAuctionStatusEnum::PENDING_SALE) == 0) {
                        $car_auction->pick_up_date = (!empty($request->pick_up_date) ? date('Y-m-d H:i:s', strtotime($request->pick_up_date)) : date('Y-m-d H:i:s'));
                    }
                    if ((is_null($car_auction->close_cmi_vmi_date))) {
                        $car_auction->status = CarAuctionStatusEnum::PENDING_SALE;
                    } else {
                        $car_auction->status = CarAuctionStatusEnum::READY_AUCTION;
                    }
                    $car_auction->save();
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.car-auctions.index'),
        ]);
    }

    public function saveSendAuction(Request $request)
    {
        if (empty($request->auction_place)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกสถานที่ประมูล',
            ], 422);
        }
        if (empty($request->send_auction_date)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกวันที่ส่งไป Auction',
            ], 422);
        }
        if (empty($request->arr_send_auction)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรถที่ต้องการส่งไป Auction',
            ], 422);
        }

        if (strcmp($request->is_forklift, STATUS_ACTIVE) == 0) {
            $validator = Validator::make($request->all(), [
                'original_place' => ['required'],
                'original_date' => ['required'],
                'original_contact' => ['required'],
                'original_tel' => ['required'],
                'destination_place' => ['required'],
                'destination_date' => ['required'],
                'destination_contact' => ['required'],
                'destination_tel' => ['required'],
            ], [], [
                'original_place' => __('car_auctions.original_place'),
                'original_date' => __('car_auctions.original_date'),
                'original_contact' => __('car_auctions.original_contact'),
                'original_tel' => __('car_auctions.original_tel'),
                'destination_place' => __('car_auctions.destination_place'),
                'destination_date' => __('car_auctions.destination_date'),
                'destination_contact' => __('car_auctions.destination_contact'),
                'destination_tel' => __('car_auctions.destination_tel'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        if (strcmp($request->is_driver, STATUS_ACTIVE) == 0) {
            $validator = Validator::make($request->all(), [
                'driver_date' => ['required'],
                'driver_contact' => ['required'],
                'driver_place' => ['required'],
            ], [], [
                'driver_date' => __('car_auctions.driver_date'),
                'driver_contact' => __('car_auctions.driver_contact'),
                'driver_place' => __('car_auctions.driver_place'),
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                return $this->responseValidateFailed($validator);
            }
        }

        if ($request->arr_send_auction > 0) {
            foreach ($request->arr_send_auction as $item) {
                $car_auction = CarAuction::find($item['id']);
                if ($car_auction) {
                    $car_auction->auction_id = $request->auction_place;
                    $car_auction->send_auction_date = (!empty($request->send_auction_date) ? date('Y-m-d H:i:s', strtotime($request->send_auction_date)) : date('Y-m-d H:i:s'));

                    if (strcmp($request->change_status, CarAuctionStatusEnum::CHANGE_AUCTION) == 0) {
                        if ($car_auction->auto_grate) {
                            $car_auction->status = CarAuctionStatusEnum::PENDING_AUCTION;
                        } else {
                            $car_auction->status = CarAuctionStatusEnum::SEND_AUCTION;
                        }
                    } elseif (strcmp($request->prev_status, CarAuctionStatusEnum::READY_AUCTION) == 0) {
                        $car_auction->status = CarAuctionStatusEnum::SEND_AUCTION;
                    }
                    $car_auction->save();

                    if (strcmp($request->is_forklift, STATUS_ACTIVE) == 0) {
                        $car_slide = CarAuctionTrait::createCarSlide($car_auction, $request);
                    }
                    if (strcmp($request->is_driver, STATUS_ACTIVE) == 0) {
                        $type_status = STATUS_ACTIVE;
                        if (strcmp($request->change_status, CarAuctionStatusEnum::CHANGE_AUCTION) == 0) {
                            $driving_job = CarAuctionTrait::createDrivingJob($car_auction, SelfDriveTypeEnum::SEND, $request, $type_status, false);
                        } else {
                            $driving_job = CarAuctionTrait::createDrivingJob($car_auction, SelfDriveTypeEnum::SEND, $request, $type_status, true);
                        }
                    } elseif (strcmp($request->is_driver, STATUS_INACTIVE) == 0) {
                        $type_status = STATUS_INACTIVE;
                        if (strcmp($request->change_status, CarAuctionStatusEnum::CHANGE_AUCTION) == 0) {
                            $driving_job = CarAuctionTrait::createDrivingJob($car_auction, SelfDriveTypeEnum::SEND, $request, $type_status, true);
                        } else {
                            $driving_job = CarAuctionTrait::createDrivingJob($car_auction, SelfDriveTypeEnum::SEND, $request, $type_status, true);
                        }
                    }
                }
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.car-auctions.index'),
        ]);
    }

    public function saveStatusBook(Request $request)
    {
        if (empty($request->book_date)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกวันที่ต้องการเล่มทะเบียน',
            ], 422);
        }
        if (empty($request->arr_book)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกรถที่ต้องการเบิกเล่มทะเบียน',
            ], 422);
        }

        if ($request->arr_book > 0) {
            foreach ($request->arr_book as $item) {
                $car_auction = CarAuction::find($item['id']);
                if ($car_auction) {
                    $car_auction->book_date = (!empty($request->book_date) ? date('Y-m-d H:i:s', strtotime($request->book_date)) : date('Y-m-d H:i:s'));
                    $car_auction->save();
                }
            }

            $description = "เบิกเล่มทะเบียนรถจำนวน" . count($request->arr_book) . "คัน";
            $url = route('admin.car-auctions.index', ['car_book' => $request->arr_book]);
            $dataDepartment = [
                DepartmentEnum::CSD_CUSTOMER_SERVICE_CORPORATE,
            ];
            $notiDepartmentId = NotificationTrait::getDepartmentId($dataDepartment);
            $notiTypeChange = new NotificationManagement('เบิกเล่มทะเบียน', $description, $url, NotificationScopeEnum::DEPARTMENT, $notiDepartmentId, []);
            $notiTypeChange->send();
        }
        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.car-auctions.index'),
        ]);
    }
    public function saveChangeAuction(Request $request)
    {
        if (empty($request->auction_new_id)) {
            return response()->json([
                'success' => false,
                'message' => 'กรุณาเลือกสถานที่ประมูลใหม่',
            ], 422);
        }

        if ($request->id) {
            $car_auction = CarAuction::find($request->id);
            if ($car_auction) {
                $car_auction->auction_id = $request->auction_new_id;
                $car_auction->status = CarAuctionStatusEnum::CHANGE_AUCTION;
                $car_auction->save();

                $old_data = [
                    'status' => $request->status_old,
                    'auction_id' => $request->auction_old_id,
                ];
                $new_data = [
                    'status' => CarAuctionStatusEnum::CHANGE_AUCTION,
                    'auction_id' => $request->auction_new_id,
                ];

                $old_data_json = json_encode($old_data);
                $new_data_json = json_encode($new_data);
                $data_audit = [
                    'user_type' => User::class,
                    'user_id' => auth()?->user()?->id,
                    'event' => 'change_auction',
                    'auditable_type' => CarAuction::class,
                    'auditable_id' => $car_auction->id,
                    'old_values' => $old_data_json,
                    'new_values' => $new_data_json,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'user_agent' => $request?->userAgent(),
                    'ip_address' => $request?->ip(),
                    'url' => $request->url(),
                ];

                $save_audit = DB::table('audits')->insert($data_audit);
                if ($save_audit) {
                    $status = true;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'ok',
            'redirect' => route('admin.car-auctions.index'),
        ]);
    }

    public function printPowerAttorneyPdf(Request $request)
    {
        $car_auction = CarAuction::find($request->car_auction);
        $car = Car::find($car_auction->car_id);
        $license_plate = null;
        if ($car) {
            $license_plate = $car->license_plate ? $car->license_plate : null;
        }

        $pdf = PDF::loadView(
            'admin.car-auctions.pdf.attorney-pdf',
            [
                'license_plate' => $license_plate,
            ]
        );
        $pdf->add_info('Title', 'หนังสือมอบอำนาจ');
        return $pdf->stream();
    }

    public function printSaleConfirmPdf(Request $request)
    {
        $car_auction = CarAuction::find($request->car_auction);
        $car = Car::find($car_auction->car_id);
        $data['license_plate'] = null;
        $data['chassis_no'] = null; //ตัวถัง
        $data['engine_no'] = null; //เครื่องยนต์
        $data['selling_price'] = null; //ราคาที่ขาย
        if ($car) {
            $data['license_plate'] = $car->license_plate ? $car->license_plate : null;
            $data['chassis_no'] = $car->chassis_no ? $car->chassis_no : null;
            $data['engine_no'] = $car->engine_no ? $car->engine_no : null;
        }
        $data['selling_price'] = $car_auction->selling_price;

        $pdf = PDF::loadView(
            'admin.car-auctions.pdf.sale-confirm-pdf',
            [
                'data' => $data,
            ]
        );
        $pdf->add_info('Title', 'หนังสือยืนยันการขาย');
        return $pdf->stream();
    }

    public function printSaleSummaryPdf(Request $request)
    {
        $ids = $request->ids;
        if (!is_array($ids)) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }

        $car_auction = CarAuction::leftjoin('cars', 'cars.id', '=', 'car_auctions.car_id')
            ->leftjoin('car_colors', 'car_colors.id', '=', 'cars.car_color_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_types', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftJoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->whereIn('car_auctions.id', $ids)
            ->select(
                'car_auctions.*',
                'car_classes.name as car_class_name',
                'cars.license_plate',
                'car_classes.manufacturing_year as car_class_year',
                'car_brands.name as car_brand_name',
                'car_colors.name as car_color_name',
            )
            ->get();
        $car_auction->map(function ($item) {
            $item->lot_date = get_thai_date_format($item->sale_date, 'd/m/y');
            $item->sold_price_total = number_format($item->total_selling_price, 2, '.', ',');
            $item->sold_price_vat = number_format($item->selling_price, 2, '.', ',');
            $item->vat = number_format($item->vat_selling_price, 2, '.', ',');
            $item->tax = number_format($item->tax_refund, 2, '.', ',').
            $item->other = number_format($item->other_price, 2, '.', ',');
            return $item;
        });

        $pdf = PDF::loadView(
            'admin.car-auctions.pdf.sale-summary-pdf',
            [
                'car_auction' => $car_auction
            ]
        );
        $pdf->add_info('Title', 'เอกสารสรุปการขาย');
        return $pdf->download('เอกสารสรุปการขาย.pdf');
    }

    public function exportDownloadExcel(Request $request)
    {
        $ids = $request->ids;
        if (!is_array($ids)) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }

        $car_auction_list = CarAuction::whereIn('id', $ids)->get();
        if (sizeof($car_auction_list) <= 0) {
            return $this->responseWithCode(false, __('lang.store_error_title'), null, 422);
        }

        foreach ($car_auction_list as $key => $car_auction) {
            $car_auction->index = $key + 1;
            $car = Car::find($car_auction->car_id);
            $car_auction->license_plate = $car->license_plate ? $car->license_plate : '';
            $car_auction->engine_no = $car->engine_no ? $car->engine_no : '';
            $car_auction->chassis_no = $car->chassis_no ? $car->chassis_no : '';
            $car_auction->car_class_name = $car->carClass?->full_name;
            $car_auction->engine_size = $car->engine_size ? $car->engine_size : '';
            $car_auction->customer = $car_auction->customer ? $car_auction->customer : '';
            $car_auction->address = $car_auction->address ? $car_auction->address : '';
            $car_auction->selling_price_car = number_format($car_auction->selling_price);
        }
        if (count($car_auction_list) > 0) {
            return (new FastExcel($car_auction_list))->download('file.xlsx', function ($line) {
                return [
                    'ลำดับ' => $line->index,
                    'ทะเบียน' => $line->license_plate,
                    'เลขเครื่อง' => $line->engine_no,
                    'เลขตัวถัง' => $line->chassis_no,
                    'รุ่น' => $line->car_class_name,
                    'CC' => $line->engine_size,
                    'ลูกค้า' => $line->customer,
                    'ที่อยู่' => $line->address,
                    'ราคาขาย' => $line->selling_price_car,
                ];
            });
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }
}
