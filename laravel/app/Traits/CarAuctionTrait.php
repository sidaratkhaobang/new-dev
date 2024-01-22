<?php

namespace App\Traits;

use App\Enums\Actions;
use App\Enums\DrivingJobTypeStatusEnum;
use App\Enums\InsuranceCarStatusEnum;
use App\Enums\Resources;
use App\Enums\SellingPriceStatusEnum;
use App\Enums\SlideLineTypeEnum;
use App\Enums\SlideTypeEnum;
use App\Enums\CarAuctionStatusEnum;
use App\Models\AuctionPlace;
use App\Models\Car;
use App\Models\CarAccessory;
use App\Models\CarAuction;
use App\Models\CarParkTransfer;
use App\Models\CMI;
use App\Models\DrivingJob;
use App\Models\Leasing;
use App\Models\Slide;
use App\Models\SlideLine;
use App\Models\VMI;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\InsuranceTrait;
use Illuminate\Support\Facades\DB;
use App\Factories\DrivingJobFactory;
use App\Factories\CarparkTransferFactory;

trait CarAuctionTrait
{

    public static function getCarInfo($car_id)
    {
        $car = Car::find($car_id);
        if (!$car) {
            return null;
        }
        $car->name = $car->license_plate;
        $car->car_class_name = $car->carClass?->full_name;
        $car->chassis_no = $car->chassis_no;
        $car->engine_no = $car->engine_no;
        $car->current_mileage = $car->current_mileage;
        $car->registered_date = $car->registered_date;
        $car->car_price = $car->purchase_price;
        $car_age = Carbon::now()->diff($car->registered_date);
        $month = ($car_age->y * 12) + $car_age->m;
        $car->car_age = $car_age->y . " ปี " . $car_age->m . " เดือน " . $car_age->d . " วัน";
        $car->month_age = $month;
        $car_images_files = $car->getMedia('car_images');
        $car_images_files = get_medias_detail($car_images_files);
        $car->image = $car_images_files[0] ?? null;
        $leasing = Leasing::find($car->leasing_id);
        if ($leasing) {
            $car->ownership = ($leasing) ? $leasing->name : null;
        }
        $car->status = $car->status;
        return $car;
    }

    public static function getStatus()
    {
        return collect([
            (object) [
                'id' => SellingPriceStatusEnum::PRE_SALE_PRICE,
                'name' => __('selling_prices.status_' . SellingPriceStatusEnum::PRE_SALE_PRICE),
                'value' => SellingPriceStatusEnum::PRE_SALE_PRICE,
            ],
            (object) [
                'id' => SellingPriceStatusEnum::REQUEST_APPROVE,
                'name' => __('selling_prices.status_' . SellingPriceStatusEnum::REQUEST_APPROVE),
                'value' => SellingPriceStatusEnum::REQUEST_APPROVE,
            ],
            (object) [
                'id' => SellingPriceStatusEnum::PENDING_REVIEW,
                'name' => __('selling_prices.status_' . SellingPriceStatusEnum::PENDING_REVIEW),
                'value' => SellingPriceStatusEnum::PENDING_REVIEW,
            ],
            (object) [
                'id' => SellingPriceStatusEnum::CONFIRM,
                'name' => __('selling_prices.status_' . SellingPriceStatusEnum::CONFIRM),
                'value' => SellingPriceStatusEnum::CONFIRM,
            ],
            (object) [
                'id' => SellingPriceStatusEnum::REJECT,
                'name' => __('selling_prices.status_' . SellingPriceStatusEnum::REJECT),
                'value' => SellingPriceStatusEnum::REJECT,
            ],
        ]);
    }

    public static function getStatusAuction()
    {
        return collect([
            (object) [
                'id' => CarAuctionStatusEnum::PENDING_SALE,
                'name' => __('car_auctions.status_' . CarAuctionStatusEnum::PENDING_SALE),
                'value' => CarAuctionStatusEnum::PENDING_SALE,
            ],
            (object) [
                'id' => CarAuctionStatusEnum::READY_AUCTION,
                'name' => __('car_auctions.status_' . CarAuctionStatusEnum::READY_AUCTION),
                'value' => CarAuctionStatusEnum::READY_AUCTION,
            ],
            (object) [
                'id' => CarAuctionStatusEnum::PENDING_AUCTION,
                'name' => __('car_auctions.status_' . CarAuctionStatusEnum::PENDING_AUCTION),
                'value' => CarAuctionStatusEnum::PENDING_AUCTION,
            ],
            (object) [
                'id' => CarAuctionStatusEnum::SEND_AUCTION,
                'name' => __('car_auctions.status_' . CarAuctionStatusEnum::SEND_AUCTION),
                'value' => CarAuctionStatusEnum::SEND_AUCTION,
            ],
            (object) [
                'id' => CarAuctionStatusEnum::SOLD_OUT,
                'name' => __('car_auctions.status_' . CarAuctionStatusEnum::SOLD_OUT),
                'value' => CarAuctionStatusEnum::SOLD_OUT,
            ],
            (object) [
                'id' => CarAuctionStatusEnum::CHANGE_AUCTION,
                'name' => __('car_auctions.status_' . CarAuctionStatusEnum::CHANGE_AUCTION),
                'value' => CarAuctionStatusEnum::CHANGE_AUCTION,
            ],
        ]);
    }

    public static function getCarAccessorie($car_id)
    {
        $car_accessory = CarAccessory::where('car_id', $car_id)
            ->join('accessories', 'accessories.id', '=', 'car_accessories.accessory_id')
            ->select(
                'accessories.name',
                'car_accessories.remark',
                'car_accessories.amount',
            )->get();

        return $car_accessory;
    }

    static function sideBarSelling()
    {
        if (user_can(Actions::View . '_' . Resources::SellingPrice)) {
            return route('admin.selling-prices.index');
        }
        if (user_can(Actions::View . '_' . Resources::SellingCar)) {
            return route('admin.selling-cars.index');
        }
    }

    static function getIsNeed()
    {
        return collect([
            [
                'id' => 1,
                'value' => 1,
                'name' => __('lang.wanted'),
            ],
            [
                'id' => 2,
                'value' => 2,
                'name' => __('lang.unwanted'),
            ],
        ]);
    }

    public static function getAuctionPlace()
    {
        $data = AuctionPlace::select('id', 'name')
            ->where('status', STATUS_ACTIVE)
            ->get()->map(function ($item) {
                $item->id = $item->id;
                $item->text = $item->name;
                return $item;
            });

        return $data;
    }

    static function createCancelInsurance($car_auction)
    {
        $vmi = VMI::where('car_id', $car_auction->car_id)
            ->whereIn('status_vmi', [InsuranceCarStatusEnum::RENEW_POLICY, InsuranceCarStatusEnum::UNDER_POLICY])->first();
        if ($vmi) {
            $vmi->status_vmi = InsuranceCarStatusEnum::REQUEST_CANCEL;
            $vmi->save();
            $success = InsuranceTrait::createCancelInsurance($vmi->id, VMI::class, $car_auction->close_cmi_vmi_date, null);
        }

        $cmi = CMI::where('car_id', $car_auction->car_id)
            ->whereIn('status_cmi', [InsuranceCarStatusEnum::RENEW_POLICY, InsuranceCarStatusEnum::UNDER_POLICY])->first();
        if ($cmi) {
            $cmi->status_cmi = InsuranceCarStatusEnum::REQUEST_CANCEL;
            $cmi->save();
            $success = InsuranceTrait::createCancelInsurance($cmi->id, CMI::class, $car_auction->close_cmi_vmi_date, null);
        }
        return true;
    }

    static function createCarSlide($car_auction, $request_data = null)
    {
        $slide = new Slide();
        $slide_count = DB::table('slides')->count() + 1;
        $prefix = 'SL-';
        $slide->worksheet_no = generateRecordNumber($prefix, $slide_count);
        $slide->origin_place = $request_data->original_place;
        $slide->origin_date = $request_data->original_date;
        $slide->origin_contact = $request_data->original_contact;
        $slide->origin_tel = $request_data->original_tel;
        $slide->destination_place = $request_data->destination_place;
        $slide->destination_date = $request_data->destination_date;
        $slide->destination_contact = $request_data->destination_contact;
        $slide->destination_tel = $request_data->destination_tel;
        $slide->type = SlideTypeEnum::CAR;
        $slide->job_type = CarAuction::class;
        $slide->job_id = $car_auction->id;
        $slide->save();

        $slide_line = SlideLine::where('slide_id', $slide->id)->delete();
        $slide_line = new SlideLine();
        $slide_line->slide_id = $slide->id;
        $slide_line->car_id = $car_auction->car_id;
        $slide_line->type = SlideLineTypeEnum::PICKUP;
        $slide_line->save();

        return true;
    }

    public static function createDrivingJob($car_auction, $self_drive_type, $request_data = null, $type_status, $check_status = true)
    {
        $driving_job_old = DrivingJob::where('job_type', CarAuction::class)
            ->where('job_id', $car_auction->id)->where('self_drive_type', $self_drive_type)->count();
        if ($driving_job_old <= 0) {
            $driving_job_start_date = null;
            $driving_job_driver_name = null;
            $driving_job_destination = null;
            if (strcmp($type_status, STATUS_ACTIVE) == 0) {
                $driving_job_start_date = $request_data->driver_date;
                $driving_job_driver_name = $request_data->driver_contact;
                $driving_job_destination = $request_data->driver_place;
            } elseif (strcmp($type_status, STATUS_INACTIVE) == 0) {
                $driving_job_driver_name = $request_data->no_driver_contact ? $request_data->no_driver_contact : null;
            }
            $djf = new DrivingJobFactory(CarAuction::class, $car_auction->id, $car_auction->car_id, [
                'self_drive_type' => $self_drive_type,
                'start_date' => $driving_job_start_date,
                'destination' => $driving_job_destination,
                'driver_name' => $driving_job_driver_name,
            ]);
            $driving_job = $djf->create();

            if ($check_status) {
                $ctf = new CarparkTransferFactory($driving_job->id, $car_auction->car_id);
                $ctf->create();
            }
        }

        return true;
    }

    public static function getTotal($request, $status_enum)
    {
        $data = CarAuction::leftjoin('cars', 'cars.id', '=', 'car_auctions.car_id')
            ->leftjoin('leasings', 'leasings.id', '=', 'cars.leasing_id')
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('auction_places', 'auction_places.id', '=', 'car_auctions.auction_id')
            ->where('leasings.is_true_leasing', STATUS_ACTIVE)
            ->when($request->car_id, function ($query) use ($request) {
                $query->where('car_auctions.car_id', $request->car_id);
            })
            ->when($request->car_class_id, function ($query) use ($request) {
                $query->where('cars.car_class_id', $request->car_class_id);
            })
            ->when($request->status_id, function ($query) use ($request) {
                $query->where('car_auctions.status', $request->status_id);
            })
            ->when($request->auction_place_id, function ($query) use ($request) {
                $query->where('car_auctions.auction_id', $request->auction_place_id);
            })
            ->when($request->from_send_auction, function ($query) use ($request) {
                $query->whereDate('car_auctions.send_auction_date', '>=', $request->from_send_auction);
            })
            ->when($request->to_send_auction, function ($query) use ($request) {
                $query->whereDate('car_auctions.send_auction_date', '<=', $request->to_send_auction);
            })
            ->when($request->from_auction, function ($query) use ($request) {
                $query->whereDate('car_auctions.auction_date', '>=', $request->from_auction);
            })
            ->when($request->to_auction, function ($query) use ($request) {
                $query->whereDate('car_auctions.auction_date', '<=', $request->to_auction);
            })
            ->when($request->from_sale, function ($query) use ($request) {
                $query->whereDate('car_auctions.sale_date', '>=', $request->from_sale);
            })
            ->when($request->to_sale, function ($query) use ($request) {
                $query->whereDate('car_auctions.sale_date', '<=', $request->to_sale);
            })
            ->where('car_auctions.status', $status_enum)->count();

        return $data;
    }

    public static function getLinkList($car_auction)
    {
        $link_list = [];
        $default_arr = [
            'worksheet_no' => NULL,
            'link' => NULL,
        ];
        $link_list['car_slide'] = $default_arr;
        $link_list['car_park_transfer'] = $default_arr;
        // $link_list['driving_job'] = $default_arr;

        if (!$car_auction) {
            return $link_list;
        }

        $driving_job = DrivingJob::where('job_id', $car_auction->id)->where('job_type', CarAuction::class)->latest()->first();
        if ($driving_job) {
            // $link_list['driving_job']['worksheet_no'] = $driving_job->worksheet_no;
            // $link_list['driving_job']['link'] = route('admin.driving-jobs.show', ['driving_job' => $driving_job->id]);

            $car_park_transfer = CarParkTransfer::where('driving_job_id', $driving_job->id)->latest()->first();
            if ($car_park_transfer) {
                $link_list['car_park_transfer']['worksheet_no'] = $car_park_transfer->worksheet_no;
                $link_list['car_park_transfer']['link'] = route('admin.car-park-transfers.show', ['car_park_transfer' => $car_park_transfer->id]);
            }
        }
        $car_slide = Slide::where('job_id', $car_auction->id)->where('job_type', CarAuction::class)->latest()->first();
        if ($car_slide) {
            $link_list['car_slide']['worksheet_no'] = $car_slide->worksheet_no;
            $link_list['car_slide']['link'] = null;
        }
        return $link_list;
    }
}
