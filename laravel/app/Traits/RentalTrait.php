<?php

namespace App\Traits;

use App\Classes\OrderManagement;
use App\Classes\ProductManagement;
use App\Classes\QuickPay;
use App\Classes\RentalCarManagement;
use App\Enums\CarEnum;
use App\Enums\ComparisonPriceStatusEnum;
use App\Enums\DiscountTypeEnum;
use App\Enums\InvoiceTypeEnum;
use App\Enums\LongTermRentalPriceStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\OfficeTypeEnum;
use App\Enums\OrderLineTypeEnum;
use App\Enums\PaymentGatewayEnum;
use App\Enums\PromotionTypeEnum;
use App\Enums\QuotationStatusEnum;
use App\Enums\RentalBillTypeEnum;
use App\Enums\RentalStatusEnum;
use App\Enums\RentalTypeEnum;
use App\Enums\ServiceTypeEnum;
use App\Enums\SpecStatusEnum;
use App\Enums\WithHoldingTaxEnum;
use App\Models\Branch;
use App\Models\Car;
use App\Models\Location;
use App\Models\OrderPromotionCode;
use App\Models\Product;
use App\Models\ProductAdditional;
use App\Models\ProductCarClass;
use App\Models\Promotion;
use App\Models\PromotionCode;
use App\Models\PromotionCodeUsage;
use App\Models\PromotionFreeCarClass;
use App\Models\PromotionFreeProductAdditional;
use App\Models\Quotation;
use App\Models\QuotationLine;
use App\Models\Rental;
use App\Models\RentalBill;
use App\Models\RentalBillPromotionCode;
use App\Models\RentalCheckIn;
use App\Models\RentalDriver;
use App\Models\RentalLine;
use App\Models\RentalProductAdditional;
use App\Models\RentalProductTransport;
use App\Models\ServiceType;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait RentalTrait
{
    static function getCarRentalTimeLine($request)
    {
        // dd($request->all());
        // TO DO query
        $service_type_id = $request->service_type_id;
        $brand = $request->car_brand_id;
        // $search = $request->search;

        $now = Carbon::now();
        $year = ($request->year) ? $request->year : $now->year;
        $month = ($request->month) ? $request->month : $now->month;
        $pickup_year = $return_year = $year;
        $pickup_month = $month - 1;
        $return_month = $month + 1;

        if ($month == 1) {
            $pickup_month = 12;
            $pickup_year = $year - 1;
        }
        if ($month == 12) {
            $return_month = 1;
            $return_year = $year + 1;
        }
        $last_day_in_month = Carbon::createFromDate($year, $month)->daysInMonth;
        $last_day_return_month = Carbon::createFromDate($return_year, $return_month)->daysInMonth;

        // start previous month
        $start_pickup_date_query = $pickup_year . '-' . $pickup_month . '-01 00:00:00';
        $end_pickup_date_query = $year . '-' . $month . '-' . $last_day_in_month . ' 23:59:59';

        $start_return_date_query = $year . '-' . $month . '-01 00:00:00';
        // end next month
        $end_return_date_query = $return_year . '-' . $return_month . '-' . $last_day_return_month . ' 23:59:59';

        $q = Rental::leftjoin('rental_lines', 'rental_lines.rental_id', '=', 'rentals.id')
            ->whereNotNull('rental_lines.car_id')
            ->whereNotIN('rentals.status', [RentalStatusEnum::CANCEL])
            ->where('rental_lines.item_type', Product::class)
            ->whereBetween('rentals.pickup_date', [$start_pickup_date_query, $end_pickup_date_query])
            ->whereBetween('rentals.return_date', [$start_return_date_query, $end_return_date_query])
            ->select(
                'rentals.pickup_date',
                'rentals.return_date',
                'rentals.status',
                'rental_lines.car_id',
                'rentals.id',
            );
        $rental_list = $q->get();

        // Log::info('pickup_date', [$start_pickup_date_query, $end_pickup_date_query]);
        // Log::info('return_date', [$start_return_date_query, $end_return_date_query]);

        $rental_list->map(function ($rental) use ($pickup_month, $return_month, $last_day_in_month, $last_day_return_month) {
            $pickup_datetime = Carbon::createFromFormat('Y-m-d H:i:s', $rental->pickup_date);
            $rental_pickup_month = $pickup_datetime->format('m');
            $rental->pickup_index = strval($pickup_datetime->format('d'));
            if ($rental_pickup_month <= $pickup_month) {
                $rental->pickup_index = 1;
            }

            $return_datetime = Carbon::createFromFormat('Y-m-d H:i:s', $rental->return_date);
            $rental_return_month = $return_datetime->format('m');
            $rental->return_index = strval($return_datetime->format('d'));
            if ($rental_return_month >= $return_month) {
                $rental->return_index = $last_day_return_month;
            }
            $rental->count_index = strval($rental->return_index - $rental->pickup_index + 1);
            return $rental;
        });

        $rental = Rental::find($request->rental_id);
        $product_car_class = null;
        if ($rental) {
            $product_car_class = ProductCarClass::where('product_id', $rental->product_id)->pluck('car_class_id')->toArray();
        }

        $rental = new RentalCarManagement($service_type_id);
        if ($request->rental_type) {
            $rental->rental_type = $request->rental_type;
        }
        $pickup_date = new DateTime($request->pickup_date);
        $return_date = new DateTime($request->return_date);
        $pickup_date->modify('-1 day');
        $return_date->modify('+1 day');
        $car_ids_all = $rental->getAllCarIds($service_type_id);
        $pickup_time_not_available = null;
        $return_time_not_available = null;
        $rentals_rent_date = $rental->getRentalsByDates($pickup_date, $pickup_time_not_available, $return_date, $return_time_not_available, $service_type_id, $car_ids_all);
        if (is_null($request->rental_type)) {
            // $rental = new RentalCarManagement($service_type_id);
            // $pickup_date = new DateTime($request->pickup_date);
            // $return_date = new DateTime($request->return_date);
            // $pickup_date->modify('-1 day');
            // $return_date->modify('+1 day');
            // $car_ids_all = $rental->getAllCarIds($service_type_id);
            // $pickup_time_not_available = null;
            // $return_time_not_available = null;
            // $rentals_rent_date = $rental->getRentalsByDates($pickup_date, $pickup_time_not_available, $return_date, $return_time_not_available, $service_type_id, $car_ids_all);
            $car_ids = $rental->getAvailableCars($pickup_date, $request->pickup_time, $return_date, $request->return_time);
            $car_not_availables = [];
            foreach ($rentals_rent_date as $rent) {
                $line_car_ids = RentalLine::where('rental_id', $rent->id)->pluck('car_id')->toArray();
                foreach ($line_car_ids as $id) {
                    $car_not_availables[] = $id;
                }
            }

            $car_list = Car::leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
                ->leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
                ->leftjoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
                ->leftjoin('cars_service_types', 'cars_service_types.car_id', '=', 'cars.id')
                ->leftjoin('service_types', 'service_types.id', '=', 'cars_service_types.service_type_id')
                // To DO
                ->where('service_types.id', $service_type_id)
                /* ->when(!empty($brand), function ($query) use ($brand) {
                $query->where('car_brands.id', $brand);
                })
                ->when(!empty($product_car_class), function ($query) use ($product_car_class) {
                $query->whereIn('cars.car_class_id', $product_car_class);
                }) */
                // ->when(!empty($search), function ($query) use ($search) {
                //     $query->where('cars.license_plate', 'like', '%' . $search . '%');
                // })
                ->where('cars.rental_type', RentalTypeEnum::SHORT)
                ->whereIn('cars.status', [CarEnum::READY_TO_USE, CarEnum::NEWCAR])
                // ->whereIn('cars.id', $car_ids)
                ->select(
                    'cars.id',
                    'cars.license_plate',
                    'car_classes.name as car_class_name',
                    'car_classes.full_name as car_class_full_name',
                    DB::raw('false as checked'),
                )
                ->distinct()
                ->get();
            $car_list->map(function ($car) use ($rental_list, $car_ids, $car_not_availables, $rentals_rent_date, $request) {
                $car->can_rent = false;
                $search = array_search($car->id, $car_ids);
                if (strcmp($search, false) !== 0) {
                    $car->can_rent = true;
                } else {
                    $line_car = RentalLine::where('rental_id', $request->rental_id)->pluck('car_id')->toArray();
                    // dd($line_car, $car_not_availables);
                    foreach ($line_car as $id) {
                        if ($car->id == $id) {
                            $search_car = array_search($id, $car_not_availables);
                            if (strcmp($search_car, false) !== 0) {
                                $car->can_rent = true;
                            } else {

                                $car->can_rent = false;
                            }
                        }
                    }
                }
                $car_images = $car->getMedia('car_images');
                $car->image = get_medias_detail($car_images);
                $car_rentals = $rental_list->where('car_id', $car->id);
                $car->timelines = $car_rentals;
                $car->class_name = $car->car_class_full_name;
            });
        } else {
            // $rental = new RentalCarManagement($service_type_id);
            // if ($request->rental_type) {
            //     $rental->rental_type = $request->rental_type;
            // }
            // $pickup_date = new DateTime($request->pickup_date);
            // $return_date = new DateTime($request->return_date);
            // $pickup_date->modify('-1 day');
            // $return_date->modify('+1 day');
            // $car_ids_all = $rental->getAllSpareCarIds($service_type_id);
            // $pickup_time_not_available = null;
            // $return_time_not_available = null;
            // $rentals_rent_date = $rental->getRentalsByDates($pickup_date, $pickup_time_not_available, $return_date, $return_time_not_available, $service_type_id, $car_ids_all);
            $car_ids = $rental->clearAvailableSpareCarIds($rentals_rent_date, $car_ids_all);

            $car_list = Car::leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
                ->leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
                ->leftjoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
                ->leftjoin('cars_service_types', 'cars_service_types.car_id', '=', 'cars.id')
                ->leftjoin('service_types', 'service_types.id', '=', 'cars_service_types.service_type_id')
                // ->where('service_types.id', $service_type_id)
                ->when(!empty($brand), function ($query) use ($brand) {
                    $query->where('car_brands.id', $brand);
                })
                ->when(!empty($product_car_class), function ($query) use ($product_car_class) {
                    $query->whereIn('cars.car_class_id', $product_car_class);
                })
                ->where('cars.rental_type', RentalTypeEnum::SPARE)
                ->whereIn('cars.status', [CarEnum::READY_TO_USE, CarEnum::NEWCAR])
                ->select(
                    'cars.id',
                    'cars.license_plate',
                    'car_classes.name as car_class_name',
                    'car_classes.full_name as car_class_full_name',
                    DB::raw('false as checked'),
                )
                ->distinct()
                ->get();

            $car_list->map(function ($car) use ($rental_list, $car_ids, $rentals_rent_date, $request) {
                $car->can_rent = false;
                $search = array_search($car->id, $car_ids);
                if (strcmp($search, false) !== 0) {
                    $car->can_rent = true;
                } else {
                    $car->can_rent = false;
                }
                $car_images = $car->getMedia('car_images');
                $car->image = get_medias_detail($car_images);
                $car_rentals = $rental_list->where('car_id', $car->id);
                $car->timelines = $car_rentals;
                $car->class_name = $car->car_class_full_name;
            });
        }
        return $car_list;
    }

    public static function getCarRentalByMonthYear($car_id, $month, $year)
    {
        $now = Carbon::now();
        $year = $year ?? $now->year;
        $month = $month ?? $now->month;
        $pickup_year = $return_year = $year;
        $pickup_month = $month - 1;
        $return_month = $month + 1;

        if ($month == 1) {
            $pickup_month = 12;
            $pickup_year = $year - 1;
        }
        if ($month == 12) {
            $return_month = 1;
            $return_year = $year + 1;
        }
        $last_day_in_month = Carbon::createFromDate($year, $month)->daysInMonth;
        $last_day_return_month = Carbon::createFromDate($return_year, $return_month)->daysInMonth;

        $start_pickup_date_query = $pickup_year . '-' . $pickup_month . '-01 00:00:00';
        $end_pickup_date_query = $year . '-' . $month . '-' . $last_day_in_month . ' 23:59:59';

        $start_return_date_query = $year . '-' . $month . '-01 00:00:00';
        $end_return_date_query = $return_year . '-' . $return_month . '-' . $last_day_return_month . ' 23:59:59';
        $rental_list = Rental::leftjoin('rental_lines', 'rental_lines.rental_id', '=', 'rentals.id')
            ->whereNotNull('rental_lines.car_id')
            ->whereNotIn('rentals.status', [RentalStatusEnum::CANCEL])
            ->where('rental_lines.item_type', Product::class)
            ->where('rental_lines.car_id', $car_id)
            ->whereBetween('rentals.pickup_date', [$start_pickup_date_query, $end_pickup_date_query])
            ->whereBetween('rentals.return_date', [$start_return_date_query, $end_return_date_query])
            ->select(
                'rentals.pickup_date',
                'rentals.return_date',
                'rentals.status',
                'rental_lines.car_id',
                'rentals.id',
            )->get();

        $status_arr = [
            RentalStatusEnum::DRAFT => 'dark',
            RentalStatusEnum::PENDING => 'warning',
            RentalStatusEnum::PAID => 'success',
            RentalStatusEnum::SUCCESS => 'primary',
            RentalStatusEnum::AWAIT_RECEIVE => 'warning',
            RentalStatusEnum::ACTIVE => 'success',
            RentalStatusEnum::CANCEL => 'danger',
        ];

        $rental_list->map(function ($rental) use ($pickup_month, $return_month, $last_day_in_month, $last_day_return_month, $status_arr) {
            $pickup_datetime = Carbon::createFromFormat('Y-m-d H:i:s', $rental->pickup_date);
            $rental_pickup_month = $pickup_datetime->format('m');
            $rental->start_point = strval($pickup_datetime->format('d'));
            if ($rental_pickup_month <= $pickup_month) {
                $rental->start_point = 1;
            }

            $return_datetime = Carbon::createFromFormat('Y-m-d H:i:s', $rental->return_date);
            $rental_return_month = $return_datetime->format('m');
            $rental->end_point = strval($return_datetime->format('d'));
            // if ($rental_return_month >= $return_month) {
            //     $rental->end_point = $last_day_return_month;
            // }
            $rental->total_point = strval($rental->end_point - $rental->start_point + 1);
            $rental->bg_color = $status_arr[$rental->status] ?? '';
            $pickup_hours = $pickup_datetime->format('H:i');
            $return_hours = $return_datetime->format('H:i');
            $rental->text = $pickup_hours . ' - ' . $return_hours;
            return $rental;
        });
        return $rental_list;
    }

    static function getCarRentalSpareTimeLine($request)
    {
        // TO DO query
        $service_type_id = $request->service_type_id;
        $brand = $request->car_brand_id;

        $now = Carbon::now();
        $year = ($request->year) ? $request->year : $now->year;
        $month = ($request->month) ? $request->month : $now->month;
        $pickup_year = $return_year = $year;
        $pickup_month = $month - 1;
        $return_month = $month + 1;

        if ($month == 1) {
            $pickup_month = 12;
            $pickup_year = $year - 1;
        }
        if ($month == 12) {
            $return_month = 1;
            $return_year = $year + 1;
        }
        $last_day_in_month = Carbon::createFromDate($year, $month)->daysInMonth;
        $last_day_return_month = Carbon::createFromDate($return_year, $return_month)->daysInMonth;

        // start previous month
        $start_pickup_date_query = $pickup_year . '-' . $pickup_month . '-01 00:00:00';
        $end_pickup_date_query = $year . '-' . $month . '-' . $last_day_in_month . ' 23:59:59';

        $start_return_date_query = $year . '-' . $month . '-01 00:00:00';
        // end next month
        $end_return_date_query = $return_year . '-' . $return_month . '-' . $last_day_return_month . ' 23:59:59';

        $q = Rental::leftjoin('rental_lines', 'rental_lines.rental_id', '=', 'rentals.id')
            ->whereNotNull('rental_lines.car_id')
            ->whereNotIN('rentals.status', [RentalStatusEnum::CANCEL])
            ->whereBetween('rentals.pickup_date', [$start_pickup_date_query, $end_pickup_date_query])
            ->whereBetween('rentals.return_date', [$start_return_date_query, $end_return_date_query])
            ->select(
                'rentals.pickup_date',
                'rentals.return_date',
                'rentals.status',
                'rental_lines.car_id',
                'rentals.id',
            );

        $rental_list = $q->get();

        $rental_list->map(function ($rental) use ($pickup_month, $return_month, $last_day_in_month, $last_day_return_month) {
            $pickup_datetime = Carbon::createFromFormat('Y-m-d H:i:s', $rental->pickup_date);
            $rental_pickup_month = $pickup_datetime->format('m');
            $rental->pickup_index = strval($pickup_datetime->format('d'));
            if ($rental_pickup_month <= $pickup_month) {
                $rental->pickup_index = 1;
            }

            $return_datetime = Carbon::createFromFormat('Y-m-d H:i:s', $rental->return_date);
            $rental_return_month = $return_datetime->format('m');
            $rental->return_index = strval($return_datetime->format('d'));
            if ($rental_return_month >= $return_month) {
                $rental->return_index = $last_day_return_month;
            }
            $rental->count_index = strval($rental->return_index - $rental->pickup_index + 1);
            return $rental;
        });

        $rental = Rental::find($request->rental_id);
        $product_car_class = null;
        if ($rental) {
            $product_car_class = ProductCarClass::where('product_id', $rental->product_id)->pluck('car_class_id')->toArray();
        }

        $rental = new RentalCarManagement($service_type_id);
        $pickup_date = new DateTime($request->pickup_date);
        $return_date = new DateTime($request->return_date);
        $pickup_date->modify('-1 day');
        $return_date->modify('+1 day');
        $car_ids_all = $rental->getAllSpareCarIds($service_type_id);
        $pickup_time_not_available = null;
        $return_time_not_available = null;
        $rentals_rent_date = $rental->getRentalsByDates($pickup_date, $pickup_time_not_available, $return_date, $return_time_not_available, $service_type_id, $car_ids_all);
        $car_ids = $rental->clearAvailableCarIds($rentals_rent_date, $car_ids_all);

        $car_list = Car::leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->leftjoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->leftjoin('cars_service_types', 'cars_service_types.car_id', '=', 'cars.id')
            ->leftjoin('service_types', 'service_types.id', '=', 'cars_service_types.service_type_id')
            // ->where('service_types.id', $service_type_id)
            ->when(!empty($brand), function ($query) use ($brand) {
                $query->where('car_brands.id', $brand);
            })
            ->when(!empty($product_car_class), function ($query) use ($product_car_class) {
                $query->whereIn('cars.car_class_id', $product_car_class);
            })
            ->where('cars.rental_type', RentalTypeEnum::SPARE)
            ->whereIn('cars.status', [CarEnum::READY_TO_USE, CarEnum::NEWCAR])
            ->select(
                'cars.id',
                'cars.license_plate',
                'car_classes.name as car_class_name',
                'car_classes.full_name as car_class_full_name',
                DB::raw('false as checked'),
            )
            ->distinct()
            ->get();

        $car_list->map(function ($car) use ($rental_list, $car_ids, $rentals_rent_date, $request) {
            $car->can_rent = false;
            $search = array_search($car->id, $car_ids);
            if (strcmp($search, false) !== 0) {
                $car->can_rent = true;
            } else {
                $car->can_rent = false;
            }
            $car_images = $car->getMedia('car_images');
            $car->image = get_medias_detail($car_images);
            $car_rentals = $rental_list->where('car_id', $car->id);
            $car->timelines = $car_rentals;
            $car->class_name = $car->car_class_full_name;
        });
        // dd($car_list);
        return $car_list;
    }

    static function findProduct($rental)
    {
        $pm = new ProductManagement($rental->service_type_id, $rental->branch_id);
        $product = $pm->find($rental->product_id, [
            'pickup_date_time' => $rental->pickup_date // date_time format
        ]);
        return $product;
    }

    static function findProductPrice($rental)
    {
        $pm = new ProductManagement($rental->service_type_id, $rental->branch_id);
        $price = $pm->findPrice($rental->product_id, $rental->pickup_date, $rental->return_date);
        return $price;
    }

    static function getComparePriceStatusList()
    {
        return collect([
            (object)[
                'id' => ComparisonPriceStatusEnum::DRAFT,
                'name' => __('long_term_rentals.compare_price_status_' . ComparisonPriceStatusEnum::DRAFT),
                'value' => ComparisonPriceStatusEnum::DRAFT,
            ],
            (object)[
                'id' => ComparisonPriceStatusEnum::CONFIRM,
                'name' => __('long_term_rentals.compare_price_status_' . ComparisonPriceStatusEnum::CONFIRM),
                'value' => ComparisonPriceStatusEnum::CONFIRM,
            ]
        ]);
    }

    static function getRentalPriceStatusList()
    {
        return collect([
            (object)[
                'id' => LongTermRentalPriceStatusEnum::DRAFT,
                'name' => __('long_term_rentals.rental_price_status_' . LongTermRentalPriceStatusEnum::DRAFT),
                'value' => LongTermRentalPriceStatusEnum::DRAFT,
            ],
            (object)[
                'id' => LongTermRentalPriceStatusEnum::CONFIRM,
                'name' => __('long_term_rentals.rental_price_status_' . LongTermRentalPriceStatusEnum::CONFIRM),
                'value' => LongTermRentalPriceStatusEnum::CONFIRM,
            ],
            (object)[
                'id' => LongTermRentalPriceStatusEnum::REJECT,
                'name' => __('long_term_rentals.rental_price_status_' . LongTermRentalPriceStatusEnum::REJECT),
                'value' => LongTermRentalPriceStatusEnum::REJECT,
            ],
        ]);
    }

    static function getSpecStatusList()
    {
        return collect([
            (object)[
                'id' => SpecStatusEnum::DRAFT,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::DRAFT),
                'value' => SpecStatusEnum::DRAFT,
            ],
            (object)[
                'id' => SpecStatusEnum::ACCESSORY_CHECK,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::ACCESSORY_CHECK),
                'value' => SpecStatusEnum::ACCESSORY_CHECK,
            ],
            (object)[
                'id' => SpecStatusEnum::PENDING_REVIEW,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::PENDING_REVIEW),
                'value' => SpecStatusEnum::PENDING_REVIEW,
            ],
            (object)[
                'id' => SpecStatusEnum::CONFIRM,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::CONFIRM),
                'value' => SpecStatusEnum::CONFIRM,
            ],
            (object)[
                'id' => SpecStatusEnum::REJECT,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::REJECT),
                'value' => SpecStatusEnum::REJECT,
            ],
        ]);
    }

    static function getApproveSpecStatusList()
    {
        return collect([
            (object)[
                'id' => SpecStatusEnum::CONFIRM,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::CONFIRM),
                'value' => SpecStatusEnum::CONFIRM,
            ],
            (object)[
                'id' => SpecStatusEnum::REJECT,
                'name' => __('long_term_rentals.spec_status_' . SpecStatusEnum::REJECT),
                'value' => SpecStatusEnum::REJECT,
            ],
        ]);
    }

    static function getStatusRentalList()
    {
        return collect([
            (object)[
                'id' => LongTermRentalStatusEnum::NEW,
                'value' => LongTermRentalStatusEnum::NEW,
                'name' => __('long_term_rentals.lt_rental_status_' . LongTermRentalStatusEnum::NEW),
            ],
            (object)[
                'id' => LongTermRentalStatusEnum::SPECIFICATION,
                'value' => LongTermRentalStatusEnum::SPECIFICATION,
                'name' => __('long_term_rentals.lt_rental_status_' . LongTermRentalStatusEnum::SPECIFICATION),
            ],
            (object)[
                'id' => LongTermRentalStatusEnum::COMPARISON_PRICE,
                'value' => LongTermRentalStatusEnum::COMPARISON_PRICE,
                'name' => __('long_term_rentals.lt_rental_status_' . LongTermRentalStatusEnum::COMPARISON_PRICE),
            ],
            (object)[
                'id' => LongTermRentalStatusEnum::RENTAL_PRICE,
                'value' => LongTermRentalStatusEnum::RENTAL_PRICE,
                'name' => __('long_term_rentals.lt_rental_status_' . LongTermRentalStatusEnum::RENTAL_PRICE),
            ],
            (object)[
                'id' => LongTermRentalStatusEnum::QUOTATION,
                'value' => LongTermRentalStatusEnum::QUOTATION,
                'name' => __('long_term_rentals.lt_rental_status_' . LongTermRentalStatusEnum::QUOTATION),
            ],
            (object)[
                'id' => LongTermRentalStatusEnum::QUOTATION_CONFIRM,
                'value' => LongTermRentalStatusEnum::QUOTATION_CONFIRM,
                'name' => __('long_term_rentals.lt_rental_status_' . LongTermRentalStatusEnum::QUOTATION_CONFIRM),
            ],
            (object)[
                'id' => LongTermRentalStatusEnum::COMPLETE,
                'value' => LongTermRentalStatusEnum::COMPLETE,
                'name' => __('long_term_rentals.lt_rental_status_' . LongTermRentalStatusEnum::COMPLETE),
            ],
        ]);
    }

    static function getStatusShortTermRentalList()
    {
        return collect([
            (object)[
                'id' => RentalStatusEnum::DRAFT,
                'value' => RentalStatusEnum::DRAFT,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::DRAFT),
            ],
            (object)[
                'id' => RentalStatusEnum::PENDING,
                'value' => RentalStatusEnum::PENDING,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::PENDING),
            ],
            (object)[
                'id' => RentalStatusEnum::PAID,
                'value' => RentalStatusEnum::PAID,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::PAID),
            ],
            (object)[
                'id' => RentalStatusEnum::PREPARE,
                'value' => RentalStatusEnum::PREPARE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::PREPARE),
            ],
            (object)[
                'id' => RentalStatusEnum::AWAIT_RECEIVE,
                'value' => RentalStatusEnum::AWAIT_RECEIVE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::AWAIT_RECEIVE),
            ],
            (object)[
                'id' => RentalStatusEnum::ACTIVE,
                'value' => RentalStatusEnum::ACTIVE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::ACTIVE),
            ],
            (object)[
                'id' => RentalStatusEnum::AWAIT_RETURN,
                'value' => RentalStatusEnum::AWAIT_RETURN,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::AWAIT_RETURN),
            ],
            (object)[
                'id' => RentalStatusEnum::SUCCESS,
                'value' => RentalStatusEnum::SUCCESS,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::SUCCESS),
            ],
            (object)[
                'id' => RentalStatusEnum::CANCEL,
                'value' => RentalStatusEnum::CANCEL,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::CANCEL),
            ],

            // (object) [
            //     'id' => RentalStatusEnum::TEMPORARY,
            //     'value' => RentalStatusEnum::TEMPORARY,
            //     'name' => __('short_term_rentals.status_' . RentalStatusEnum::TEMPORARY),
            // ],
            // (object) [
            //     'id' => RentalStatusEnum::REMARK,
            //     'value' => RentalStatusEnum::REMARK,
            //     'name' => __('short_term_rentals.status_' . RentalStatusEnum::REMARK),
            // ],
            (object)[
                'id' => RentalStatusEnum::CHANGE,
                'value' => RentalStatusEnum::CHANGE,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::CHANGE),
            ],

        ]);
    }

    static function getRentalCarsByIds($car_ids)
    {
        $car_list = Car::leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->leftjoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')
            ->whereIn('cars.id', $car_ids)
            ->select(
                'cars.id',
                'cars.license_plate',
                'car_classes.name as car_class_name',
                'car_classes.full_name as car_class_full_name'
            )
            ->get();
        return $car_list;
    }

    static function canAddProductAdditional($service_type)
    {
        // $allowed_list = [ServiceTypeEnum::LIMOUSINE, ServiceTypeEnum::BOAT, ServiceTypeEnum::MINI_COACH];
        // return in_array($service_type, $allowed_list);
        $allowed_list = [ServiceTypeEnum::SLIDE_FORKLIFT];
        return !in_array($service_type, $allowed_list);
        // return true; // every service type can
    }

    static function canAddMutipleCar($service_type)
    {
        $allowed_list = [ServiceTypeEnum::BUS, ServiceTypeEnum::BOAT, ServiceTypeEnum::MINI_COACH];
        return in_array($service_type, $allowed_list);
    }

    static function canAddDriver($service_type)
    {
        $allowed_list = [ServiceTypeEnum::SELF_DRIVE];
        return in_array($service_type, $allowed_list);
    }

    static function canAddProductTransport($service_type)
    {
        $allowed_list = [ServiceTypeEnum::SLIDE_FORKLIFT];
        return in_array($service_type, $allowed_list);
    }

    static function getBranchOfficeList()
    {
        return collect([
            (object)[
                'id' => OfficeTypeEnum::HEAD_OFFICE,
                'value' => OfficeTypeEnum::HEAD_OFFICE,
                'name' => __('short_term_rentals.office_' . OfficeTypeEnum::HEAD_OFFICE),
            ],
            (object)[
                'id' => OfficeTypeEnum::BRANCH,
                'value' => OfficeTypeEnum::BRANCH,
                'name' => __('short_term_rentals.office_' . OfficeTypeEnum::BRANCH),
            ],
        ]);
    }

    static function getBranchOfServiceType($service_type_id)
    {
        $list = Branch::leftjoin('products', 'products.branch_id', '=', 'branches.id')
            ->where('products.service_type_id', $service_type_id)
            ->orwhereNull('products.service_type_id')
            ->select('branches.id', 'branches.name')
            ->where('branches.status', STATUS_ACTIVE)
            ->where('products.status', STATUS_ACTIVE)
            ->distinct()
            ->get()->map(function ($item) {
                return (object)[
                    'id' => $item->id,
                    'name' => $item->name,
                    'value' => $item->id,
                ];
            });
        return $list;
    }

    static function getRentalDriverList($rental_id)
    {
        $rental_driver_list = RentalDriver::where('rental_id', $rental_id)->get();
        $rental_driver_list->map(function ($item) {
            $item->id_card_number = $item->citizen_id;
            $item->license_number = $item->license_id;
            $driver_license_medias = $item->getMedia('rental_driver_license');
            $license_files = get_medias_detail($driver_license_medias);
            $license_files = collect($license_files)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->license_files = $license_files;
            $item->pending_delete_license_files = [];
            // get driver citizen files
            $driver_citizen_medias = $item->getMedia('rental_driver_citizen');
            $citizen_files = get_medias_detail($driver_citizen_medias);
            $citizen_files = collect($citizen_files)->map(function ($item) {
                $item['formated'] = true;
                $item['saved'] = true;
                $item['raw_file'] = null;
                return $item;
            })->toArray();
            $item->citizen_files = $citizen_files;
            $item->pending_delete_citizen_files = [];
            return $item;
        });
        return $rental_driver_list;
    }

    static function getRentalProductAdditionalList($rental_id)
    {
        return RentalProductAdditional::leftjoin('rentals', 'rentals.id', '=', 'rental_product_additionals.rental_id')
            ->leftjoin('rental_bills', 'rental_bills.id', '=', 'rental_product_additionals.rental_bill_id')
            ->leftjoin('product_additionals', 'product_additionals.id', '=', 'rental_product_additionals.product_additional_id')
            ->leftjoin('cars', 'cars.id', '=', 'rental_product_additionals.car_id')
            ->where('rental_product_additionals.rental_id', $rental_id)
            ->select(
                'rental_product_additionals.id',
                'rental_product_additionals.product_additional_id',
                'rental_product_additionals.car_id',
                'cars.license_plate as car_name',
                'rental_product_additionals.name',
                'rental_product_additionals.price',
                'rental_product_additionals.amount',
                'rental_product_additionals.is_free',
                'rental_product_additionals.is_from_promotion',
                'rental_product_additionals.is_from_product',
                'rental_bills.status as rental_bill_status'
            )
            ->get()
            ->map(function ($item) {
                if ($item->is_free) {
                    $item->price_format = number_format(0, 2, '.', ',');
                } else {
                    $total = $item->price * $item->amount;
                    $item->price_format = number_format($total, 2, '.', ',');
                }
                return $item;
            });
    }

    static function getRentalLineCarList($rental_id, $with_product_additionals = true, $with_extras = false)
    {
        return RentalLine::leftJoin('cars', 'cars.id', '=', 'rental_lines.car_id')
            ->leftJoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->where('rental_lines.rental_id', $rental_id)
            ->where('rental_lines.item_type', Product::class)
            ->whereNotNull('rental_lines.car_id')
            ->select(
                'rental_lines.id as rental_line_id',
                'rental_lines.item_id',
                'rental_lines.item_type',
                'rental_lines.car_id',
                'cars.license_plate',
                'cars.license_plate as name',
                'car_classes.name as class_name',
                'car_classes.full_name as class_full_name',
                'rental_lines.unit_price',
                'rental_lines.amount',
                'rental_lines.subtotal',
                'rental_lines.discount',
                'rental_lines.vat',
                'rental_lines.total',
            )->get()->map(function ($rental_line) use ($rental_id, $with_product_additionals, $with_extras) {
                // get car data
                $car = $rental_line->car;
                $rental_line->image_url = $car->image_url;
                unset($rental_line->car);

                // get product data
                $rental_line->product_name = $rental_line->summary_display_name;
                unset($rental_line->item);

                $rental_line->product_additionals = [];
                if ($with_product_additionals) {
                    $rental_line->product_additionals = RentalTrait::getRentalLineProductAdditionalList($rental_id, $rental_line->car_id)->toArray();
                }
                $rental_line->extras = [];
                if ($with_extras) {
                    $rental_line->extras = RentalTrait::getRentalLineExtraList($rental_id, $rental_line->car_id)->toArray();
                }
                $rental_line->rental_checkins = RentalTrait::getRentalCheckInList($rental_id, $car?->id);
                return $rental_line;
            });
    }

    static function getRentalLinePromotionList($rental_id)
    {
        return RentalLine::leftJoin('promotions', 'promotions.id', '=', 'rental_lines.item_id')
            ->where('rental_lines.rental_id', $rental_id)
            ->where('rental_lines.item_type', Promotion::class)
            ->select(
                'rental_lines.id as rental_line_id',
                'rental_lines.item_id',
                'rental_lines.item_type',
                'rental_lines.car_id',
                'promotions.name as promotion_name',
                'rental_lines.unit_price',
                'rental_lines.amount',
                'rental_lines.subtotal',
                'rental_lines.discount',
                'rental_lines.vat',
                'rental_lines.total',
            )->get();
    }

    static function getRentalLineCouponList($rental_id)
    {
        return RentalLine::leftJoin('promotion_codes', 'promotion_codes.id', '=', 'rental_lines.item_id')
            ->leftJoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
            ->where('rental_lines.rental_id', $rental_id)
            ->where('rental_lines.item_type', PromotionCode::class)
            ->select(
                'rental_lines.id as rental_line_id',
                'rental_lines.item_id',
                'rental_lines.item_type',
                'rental_lines.car_id',
                'promotion_codes.code as voucher_code',
                'promotions.name as promotion_name',
                'rental_lines.unit_price',
                'rental_lines.amount',
                'rental_lines.subtotal',
                'rental_lines.discount',
                'rental_lines.vat',
                'rental_lines.total',
            )->get();
    }

    static function getRentalLineProductAdditionalList($rental_id, $car_id)
    {
        return RentalLine::join('product_additionals', 'product_additionals.id', '=', 'rental_lines.item_id')
            ->where('rental_lines.rental_id', $rental_id)
            ->where('rental_lines.item_type', ProductAdditional::class)
            ->where('rental_lines.car_id', $car_id)
            ->select(
                'rental_lines.id as rental_line_id',
                'rental_lines.item_id as product_additional_id',
                'rental_lines.car_id',
                'rental_lines.subtotal',
                'rental_lines.amount',
                'rental_lines.unit_price',
                'rental_lines.discount',
                'rental_lines.total',
                'rental_lines.is_free',
                'rental_lines.is_from_promotion',
                'rental_lines.is_from_product',
                'rental_lines.is_from_coupon',

                'product_additionals.price',
                'product_additionals.name as product_additional_name',
            )
            ->get();
    }

    static function getRentalLineExtraList($rental_id, $car_id)
    {
        return RentalLine::where('rental_lines.rental_id', $rental_id)
            ->where('rental_lines.item_type', OrderLineTypeEnum::EXTRA)
            ->where('rental_lines.car_id', $car_id)
            ->select(
                'rental_lines.id as rental_line_id',
                'rental_lines.item_id as extra_id',
                'rental_lines.car_id',
                'rental_lines.name as extra_name',
                'rental_lines.subtotal',
                'rental_lines.amount',
                'rental_lines.unit_price',
                'rental_lines.discount',
                'rental_lines.total',
                'rental_lines.is_free',
                'rental_lines.is_from_promotion',
                'rental_lines.is_from_product',
                'rental_lines.is_from_coupon',
            )
            ->get();
    }

    static function getRentalProductTransportList($rental_id, $type)
    {
        return RentalProductTransport::where('rental_id', $rental_id)->where('transfer_type', $type)->select('id', 'product_type', 'brand_name', 'class_name', 'license_plate', 'color_name', 'engine_no as engine', 'chassis_no as chassis', 'remark', 'column_1 as width_m', 'column_2 as long_m', 'column_3 as height_m', 'column_4 as weight_m')->get();
    }


    static function getRentalProductTransportReturnList($rental_id, $type)
    {
        return RentalProductTransport::where('rental_id', $rental_id)->where('transfer_type', $type)->select('id', 'product_type', 'brand_name as brand_name', 'class_name as class_name', 'license_plate as license_plate', 'color_name as color_name', 'engine_no as engine', 'chassis_no as chassis', 'remark as remark', 'column_1 as width_m', 'column_2 as long_m', 'column_3 as height_m', 'column_4 as weight_m')->get();
    }


    static function getPromotionDiscount($request)
    {
        $promotion_id = $request->promotion_id;
        $promotion_codes = $request->promotion_codes;
        $rental_bill_id = $request->rental_bill_id;
        $withholding_tax = $request->withholding_tax;

        $rental_bill = RentalBill::find($rental_bill_id);
        $om = new OrderManagement($rental_bill);
        $om->setPromotion($promotion_id, $promotion_codes);
        $om->setWithHoldingTaxVal($withholding_tax);
        $om->calculate();
        $summary = $om->getSummary();
        __log($summary);
        return $summary;
    }

    static function getSelectedVoucher($rental_bill_id)
    {
        return PromotionCode::leftjoin('promotion_code_usages', 'promotion_code_usages.promotion_code_id', '=', 'promotion_codes.id')
            ->leftjoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
            ->where('promotions.promotion_type', PromotionTypeEnum::VOUCHER)
            ->where('promotion_code_usages.item_id', $rental_bill_id)
            ->pluck('promotion_codes.id');
    }

    static function getSelectedPromotion($rental_bill_id)
    {
        return PromotionCode::leftjoin('promotion_code_usages', 'promotion_code_usages.promotion_code_id', '=', 'promotion_codes.id')
            ->leftjoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
            ->whereNotIn('promotions.promotion_type', [PromotionTypeEnum::VOUCHER])
            ->where('promotion_code_usages.item_id', $rental_bill_id)
            ->pluck('promotion_codes.id');
    }

    static function checkCarsAllowForBooking($available_car_list, $booking_car_id_list)
    {
        $result = true;
        foreach ($booking_car_id_list as $booking_car_id) {
            $is_available = $available_car_list->contains('id', $booking_car_id);
            if (!$is_available) {
                $result = false;
            }
        }
        return $result;
    }


    static function createRentalBill($rental_id)
    {
        $rental_bill = new RentalBill();
        $rental_bill->bill_type = RentalBillTypeEnum::PRIMARY;
        $rental_bill->rental_id = $rental_id;
        $rental_bill->save();
        return $rental_bill;
    }

    static function saveRentalFreeProducts($rental_id, $promotion_id, $car_id)
    {
        $free_products = PromotionFreeProductAdditional::where('promotion_id', $promotion_id)->get();
        foreach ($free_products as $free_product) {
            // get product-additional information
            $product_additional_name = '-';
            $product_additional_price = 0;
            $product_additional = $free_product->product_additional;
            if ($product_additional) {
                $product_additional_name = $product_additional->name;
                $product_additional_price = abs(floatval($product_additional->price));
            }

            // create rental product additional
            $rental_product_additional = new RentalProductAdditional();
            $rental_product_additional->rental_id = $rental_id;
            $rental_product_additional->product_additional_id = $product_additional->id;
            $rental_product_additional->car_id = $car_id;
            $rental_product_additional->is_free = 1;
            $rental_product_additional->is_from_promotion = 1;
            $rental_product_additional->name = $product_additional_name;
            $rental_product_additional->amount = 1;
            $rental_product_additional->price = 0;
            $rental_product_additional->save();
        }
    }

    static function getLicensePlateRentalCars($rental_id)
    {
        $rental_lines = static::getRentalLineCars($rental_id);
        return $rental_lines->implode('description', ', ');
    }

    static function getRentalLineCars($rental_id)
    {
        return RentalLine::where('rental_id', $rental_id)
            ->where('item_type', Product::class)
            ->whereNotNull('car_id')
            ->get();
    }

    static function getCarRentalLineArray($rental_id)
    {
        return RentalLine::where('rental_id', $rental_id)
            ->where('item_type', Product::class)
            ->whereNotNull('car_id')
            ->pluck('car_id')->toArray();
    }

    static function clearPromotionProductAdditionals($rental_bill_id)
    {
        RentalProductAdditional::where('rental_bill_id', $rental_bill_id)
            ->where('is_from_promotion', STATUS_ACTIVE)
            ->delete();
    }

    static function saveNewPromotionCode($promotion_code_id, $customer_id)
    {
        $promotion_code = PromotionCode::find($promotion_code_id);
        if ($promotion_code) {
            $promotion_code->customer_id = $customer_id;
            $promotion_code->is_used = STATUS_ACTIVE;
            $promotion_code->quota = intval($promotion_code->quota) - 1;
            $promotion_code->use_date = date('Y-m-d H:i:s');
            $promotion_code->save();
        }
        return true;
    }

    static function savePromotionCodeUsage($promotion_code_id, $customer_id, $ref_type, $ref_id)
    {
        $promotion_code_usage = new PromotionCodeUsage();
        $promotion_code_usage->promotion_code_id = $promotion_code_id;
        $promotion_code_usage->customer_id = $customer_id;
        $promotion_code_usage->item_type = $ref_type;
        $promotion_code_usage->item_id = $ref_id;
        $promotion_code_usage->use_date = date('Y-m-d H:i:s');
        $promotion_code_usage->save();
        return true;
    }

    static function saveRentalBillPromotionCode($rental_bill_id, $promotion_code_id)
    {
        $rental_bill_promo_code = new RentalBillPromotionCode();
        $rental_bill_promo_code->rental_bill_id = $rental_bill_id;
        $rental_bill_promo_code->promotion_code_id = $promotion_code_id;
        $rental_bill_promo_code->save();
        return true;
    }

    static function createRentalQuotation($rental, $rental_bill)
    {
        $user = Auth::user();
        $quotation_count = Quotation::all()->count() + 1;
        $prefix = 'QT';
        $quotation = new Quotation;
        $quotation->qt_no = generateRecordNumber($prefix, $quotation_count, false);
        $quotation->qt_type = QuotationStatusEnum::DRAFT;
        $quotation->reference_type = Rental::class;
        $quotation->reference_id = $rental->id;
        $quotation->customer_id = $rental->customer_id;
        $quotation->customer_name = $rental->customer_name;
        $quotation->customer_address = $rental->customer_address;
        $quotation->customer_tel = $rental->customer_tel;
        $quotation->customer_email = $rental->customer_email;
        $quotation->customer_zipcode = $rental->customer_zipcode;
        $quotation->customer_province_id = $rental->customer_province_id;
        $quotation->subtotal = $rental_bill->subtotal;
        $quotation->vat = $rental_bill->vat;
        $quotation->total = $rental_bill->total;
        $quotation->rental_bill_id = $rental_bill->id;
        $quotation->save();

        $quotation->ref_1 = ($user && isset($user->branch)) ? $user->branch->code : null;
        $quotation->ref_2 = $quotation->qt_no;
        $quotation->save();

        $rental->quotation_id = $quotation->id;
        $rental->save();
        return $quotation;
    }

    static function saveRentalQuotationLines($rental_bill_id, $quotation_id)
    {
        $rental_lines = RentalLine::where('rental_bill_id', $rental_bill_id)->get();
        if ($rental_lines) {
            foreach ($rental_lines as $item) {
                $quotation_line = new QuotationLine();
                $quotation_line->quotation_id = $quotation_id;
                $quotation_line->reference_id = $item->id;
                $quotation_line->reference_type = RentalLine::class;
                $quotation_line->amount = $item->amount;
                $quotation_line->subtotal = $item->subtotal;
                $quotation_line->save();
            }
        }
        return true;
    }

    static function clearRentalBillPromotionCode($rental_bill_id, $rental_id)
    {
        $old_promo_codes = RentalBillPromotionCode::where('rental_bill_id', $rental_bill_id)->get();
        foreach ($old_promo_codes as $key => $old_promo_code) {
            $clear_free_car_classes = RentalTrait::clearFreeCarClass($old_promo_code->promotion_code_id, $rental_id);
            $old_promotion_code_cleared = RentalTrait::clearOldPromotionCode($old_promo_code->promotion_code_id);
        }
        RentalBillPromotionCode::where('rental_bill_id', $rental_bill_id)->delete();
        return true;
    }

    static function clearFreeCarClass($promotion_code_id, $rental_id)
    {
        $promotion_code = PromotionCode::find($promotion_code_id);
        $promotion_free_car_class_array = PromotionFreeCarClass::where('promotion_id', $promotion_code->promotion_id)->pluck('car_class_id')->toArray();
        $car_rental_lines = RentalTrait::getRentalLineCars($rental_id);
        foreach ($car_rental_lines as $key => $car_rental_line) {
            $car = Car::find($car_rental_line->car_id);
            if ($car && $car->car_class_id) {
                if (in_array($car->car_class_id, $promotion_free_car_class_array)) {
                    if (strcmp($car_rental_line->is_free, STATUS_ACTIVE) === 0) {
                        $car_rental_line->is_free = 0;
                        $car_rental_line->save();
                    }
                }
            }
        }
        return true;
    }

    static function clearOldPromotionCode($promotion_code_id)
    {
        $old_promotion = PromotionCode::find($promotion_code_id);
        if ($old_promotion) {
            $old_promotion->customer_id = NULL;
            $old_promotion->is_used = STATUS_DEFAULT;
            $old_promotion->quota = intval($old_promotion->quota) + 1;
            $old_promotion->use_date = NULL;
            $old_promotion->save();
        }
        return true;
    }

    static function clearRentalBillPromotionCodeUsed($rental_bill_id)
    {
        PromotionCodeUsage::where('item_type', RentalBill::class)
            ->where('item_id', $rental_bill_id)->delete();
        return true;
    }

    static function getRentalBillPrimaryByRentalId($rental_id)
    {
        return RentalBill::where('rental_id', $rental_id)
            ->where('bill_type', RentalBillTypeEnum::PRIMARY)
            ->first();
    }

    static function getPaymentGateWayDetailForSAP($rental)
    {
        $result = '';
        if (strcmp($rental->payment_gateway, PaymentGatewayEnum::OMISE) === 0) {
            return $result = 'omise: ' . $rental->payment_remark;
        }

        if (strcmp($rental->payment_gateway, PaymentGatewayEnum::MC_PAYMENT) === 0) {
            return $result = 'MC Payment: ' . $rental->payment_remark;
        }

        if (strcmp($rental->payment_gateway, PaymentGatewayEnum::SCB_BILL_PAY) === 0) {
            $timestamp = strtotime($rental->payment_date);
            $formatted = date('d:m:y H:i', $timestamp);
            return $result = 'qrcode: ' . $formatted;
        }

        if (strcmp($rental->payment_gateway, PaymentGatewayEnum::APP_2C2P) === 0) {
            return $result = '2c2p';
        }
        return $result;
    }

    static function calculatePricePerEachRentalCar(Rental $rental, RentalLine $rental_car)
    {
        $sum_total = RentalLine::where('rental_id', $rental->id)
            ->where('car_id', $rental_car->car_id)
            ->whereIn('item_type', [Product::class, ProductAdditional::class, OrderLineTypeEnum::EXTRA])
            ->sum('total');

        $price_bf_tax = getPriceExcludeVat($sum_total);
        return floatval(number_format($price_bf_tax, 2, '.', ''));
    }

    static function getOrderPromotionCode($customer_id, $promotion_code_id)
    {
        $order_promotion_code = OrderPromotionCode::leftjoin('order_promotion_code_lines', 'order_promotion_codes.id', '=', 'order_promotion_code_lines.order_promotion_code_id')
            ->where('order_promotion_codes.customer_id', $customer_id)
            ->where('order_promotion_code_lines.promotion_code_id', $promotion_code_id)
            ->select(
                'order_promotion_codes.id as order_promotion_code_id',
                'order_promotion_codes.customer_id as customer_id',
                'order_promotion_code_lines.promotion_code_id as promotion_code_id',
                'order_promotion_code_lines.vat as vat',
                'order_promotion_code_lines.total as total',
            )
            ->first();
        return $order_promotion_code;
    }

    static function generateQuickpayUrl($rental, $quotation, $primary = true)
    {
        $qp = new QuickPay();
        $qp->amount = $rental->total;
        $qp->description = 'description';
        $qp->rental_id = strval($rental->id);
        $qp->quotation_id = strval($quotation->id);
        $qp->class_name = Quotation::class;

        $expire_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        if ($primary) {
            $expire_date = date('Y-m-d H:i:s', strtotime($rental->pickup_date));
        }
        $qp->expire_date = $expire_date;
        $response = $qp->generateLink();
        if (strcmp($response['resCode'], '000') != 0) {
            __log('generateQuickpayUrl', [
                'response' => $response,
            ], 'error');
        }
        $payment_url = $response['url'];

        $quotation->payment_url = $payment_url;
        $quotation->payment_expire_date = $expire_date;
        $quotation->payment_ref_id = $response['qpID'];
        $quotation->payment_ref_id2 = $response['orderIdPrefix'];
        $quotation->save();
        return $payment_url;
    }

    static function updateQuickpayUrl($rental, $quotation, $primary = true)
    {
        $qp = new QuickPay();
        $qp->qp_id = $quotation->payment_ref_id;
        $qp->amount = $rental->total;
        $qp->description = 'description';
        $qp->rental_id = strval($rental->id);
        $qp->quotation_id = strval($quotation->id);
        $qp->class_name = Quotation::class;

        $expire_date = date('Y-m-d H:i:s', strtotime('+30 days'));
        if ($primary) {
            $expire_date = date('Y-m-d H:i:s', strtotime($rental->pickup_date));
        }
        $qp->expire_date = $expire_date;
        $response = $qp->update();
        if (strcmp($response['resCode'], '000') != 0) {
            __log('updateQuickpayUrl', [
                'response' => $response,
            ], 'error');
        }
    }

    static function getCountPromotionCodeUsed($promotion_code_id)
    {
        return PromotionCodeUsage::where('promotion_code_id', $promotion_code_id)->count();
    }

    static function getWithHodingTaxList()
    {
        return collect([
            [
                'id' => WithHoldingTaxEnum::TAX_1,
                'name' => __('short_term_rentals.withholding_tax_' . WithHoldingTaxEnum::TAX_1),
                'value' => WithHoldingTaxEnum::TAX_1,
            ],
            [
                'id' => WithHoldingTaxEnum::TAX_3,
                'name' => __('short_term_rentals.withholding_tax_' . WithHoldingTaxEnum::TAX_3),
                'value' => WithHoldingTaxEnum::TAX_3,
            ],
            [
                'id' => WithHoldingTaxEnum::TAX_5,
                'name' => __('short_term_rentals.withholding_tax_' . WithHoldingTaxEnum::TAX_5),
                'value' => WithHoldingTaxEnum::TAX_5,
            ]
        ]);
    }

    static function getDataServiceType($rental_id)
    {
        $url = asset('images/place_holder_150.png');
        if (!empty($idRental)) {
            $d = Rental::find($rental_id);
            if ($d) {
                $dataServiceType = ServiceType::find($d->service_type_id);
                if ($dataServiceType) {
                    $medias = $dataServiceType->getMedia('service_images');
                    $files = get_medias_detail($medias);
                    if (sizeof($files) > 0) {
                        $url = $files[0]['url'];
                    }
                }
            }
        }
        return $url;
    }

    /* static function getOrderChannelList()
    {
    return collect([
    [
    'id' => OrderChannelEnum::SMARTCAR,
    'name' => __('short_term_rentals.order_channel_' . OrderChannelEnum::SMARTCAR),
    'value' => OrderChannelEnum::SMARTCAR,
    ],
    [
    'id' => OrderChannelEnum::OTHER,
    'name' => __('short_term_rentals.order_channel_' . OrderChannelEnum::OTHER),
    'value' => OrderChannelEnum::OTHER,
    ]
    ]);
    } */

    static function getDataInfo($idRental)
    {
        $dataInfo = [];
        if ($idRental) {
            $dataInfo = Rental::whereNotNull('product_id')->find($idRental);
        }
        return $dataInfo;
    }

    static function getDataCarSelect($rental_bill_id, $rental_id)
    {
        $dataCarList = [];
        if (!empty($rental_bill_id) && !empty($rental_id)) {
            $dataCarList = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
                ->leftjoin('rentals', 'rentals.id', '=', 'rental_lines.rental_id')
                ->leftjoin('rental_bills', 'rental_bills.rental_id', '=', 'rentals.id')
                ->where('rental_lines.rental_bill_id', $rental_bill_id)
                ->where('rentals.id', $rental_id)
                ->pluck('cars.id');
        }
        return $dataCarList;
    }

    static function getDataProductAdditional($rental_line_ids, $rental_bill_id, $rental_id)
    {
        $dataCarProductAdditional = collect([]);
        if (!empty($rental_line_ids) && !empty($rental_bill_id) && !empty($rental_id)) {
            $dataCarProductAdditional = Car::leftjoin('rental_lines', 'rental_lines.car_id', '=', 'cars.id')
                ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
                ->whereIn('rental_lines.id', $rental_line_ids)
                ->where('rental_lines.item_type', Product::class)
                ->where('rental_lines.rental_bill_id', $rental_bill_id)
                ->whereNotNull('rental_lines.car_id')
                ->select(
                    'cars.id as id',
                    'cars.license_plate',
                    'car_classes.name as class_name',
                    'car_classes.full_name as class_full_name',
                )->get()->map(function ($car) use ($rental_id) {
                    $car_images = $car->getMedia('car_images');
                    $car->image = get_medias_detail($car_images);
                    $car->name = $car->license_plate;
                    $car->product_additionals = $car->rental_product_additionals->map(function ($product) use ($rental_id) {
                        $product->price_item = $product->product_additional->price;
                        $product->product_additional_id = $product->product_additional->id;
                        $product->id_data = $product->id;
                        if ($product->rental_id === $rental_id) {
                            return $product;
                        }
                    });
                    $car->product_additionals = array_values(array_filter($car->product_additionals->toArray()));
                    return $car;
                });
        }
        return $dataCarProductAdditional;
    }

    static function getDataPromotion($rental_bill_id)
    {
        if (!empty($rental_bill_id)) {
            $checked_vouchers = RentalTrait::getSelectedVoucher($rental_bill_id);
            $voucher_data = PromotionCode::wherein('id', $checked_vouchers)->pluck('promotion_id');
            $voucher_data = Promotion::wherein('id', $voucher_data)->get();
            $checked_promotion = RentalTrait::getSelectedPromotion($rental_bill_id)->first();
            $promotion_data = PromotionCode::find($checked_promotion)?->promotion;
            return [
                'promotion' => $promotion_data,
                'voucher' => $voucher_data,
            ];
        } else {
            return [
                'promotion' => [],
                'voucher' => [],
            ];
        }
    }

    static function getDataPromotionList($rental_bill_id, $rental_id)
    {
        $check_promotion_bill_usage = PromotionCodeUsage::where('item_id', $rental_bill_id)
            ->where('item_type', RentalBill::class)
            ->count();
        if (empty($check_promotion_bill_usage)) {
            return [];
        }
        $voucher = [];
        if (!empty($rental_bill_id) && !empty($rental_id)) {
            $rental_bill = RentalBill::find($rental_bill_id);
            $rental = Rental::find($rental_id);
            $promotion_type = \App\Enums\PromotionTypeEnum::PROMOTION;
            $customer_id = $rental->customer_id;
            $voucher = PromotionCode::leftjoin('promotions', 'promotions.id', '=', 'promotion_codes.promotion_id')
                ->when($promotion_type, function ($query) use ($promotion_type, $customer_id) {
                    if (strcmp($promotion_type, PromotionTypeEnum::VOUCHER) === 0) {
                        $query->whereNull('promotion_codes.customer_id');
                        $query->where('promotion_codes.is_sold', STATUS_ACTIVE);
                        $query->where('promotions.promotion_type', PromotionTypeEnum::VOUCHER);
                    }
                    if (strcmp($promotion_type, PromotionTypeEnum::PROMOTION) === 0) {
                        $query->whereNotIn('promotions.promotion_type', [PromotionTypeEnum::VOUCHER]);
                    }
                })
                ->where('promotion_codes.is_booking', STATUS_DEFAULT)

                // TODO
                // ->where('promotion_codes.is_used', STATUS_DEFAULT)
                ->where('promotion_codes.quota', '>', 0)
                // ->whereNull('.promotion_codesuse_date')
                ->select(
                    '*',
                    'promotions.name as promotion_name',
                    'promotion_codes.id',
                    'promotion_codes.code as name',
                    'promotion_codes.promotion_id',
                )
                ->get();
            $voucher = collect($voucher)->chunk(5);
        }
        return $voucher;
    }

    static function findDiscountForEachLine($rental, $promotion_id, $line)
    {
        $discount = 0;
        $line_total = floatval($line->total);
        $om = new OrderManagement($rental);
        $om->calculate();
        $discount = $om->findPromotionDiscount($promotion_id, $line_total, $line);
        return $discount;
    }

    static function saveRentalLineDiscounts($line, $discount = 0)
    {
        if (floatval($discount) > 0) {
            $rental_line = RentalLine::find($line->id);
            if ($rental_line) {
                $rental_line->discount = floatval($discount);
                $rental_line->save();
            }
        }
        return true;
    }

    static function saveRentalLine($rental, $item_type, $item_id, $amount = 1, $unit_price, $optionals = [])
    {
        $rentalLine = new RentalLine();
        $rentalLine->rental_id = $rental->id;
        $rentalLine->item_type = $item_type;
        $rentalLine->item_id = $item_id;
        $rentalLine->amount = $amount;
        $rentalLine->unit_price = abs(floatval($unit_price));
        $rentalLine->pickup_date = $rental->pickup_date;
        $rentalLine->return_date = $rental->return_date;
        $rentalLine->is_free = $optionals['is_free'] ?? false;
        $rentalLine->is_from_product = $optionals['is_from_product'] ?? false;
        $rentalLine->is_from_promotion = $optionals['is_from_promotion'] ?? false;
        $rentalLine->is_from_coupon = $optionals['is_from_coupon'] ?? false;

        $rentalLine->car_id = $optionals['car_id'] ?? null;
        $rentalLine->name = $optionals['name'] ?? null;
        $rentalLine->description = $optionals['description'] ?? null;
        $rentalLine->save();
    }

    static function clearRentalLines($rental_id, $item_type, $optionals = [])
    {
        RentalLine::where('rental_id', $rental_id)
            ->where('item_type', $item_type)
            ->where(function ($query) use ($optionals) {
                if (isset($optionals['is_free'])) {
                    $query->where('is_free', $optionals['is_free']);
                }
                if (isset($optionals['is_from_product'])) {
                    $query->where('is_from_product', $optionals['is_from_product']);
                }
                if (isset($optionals['is_from_promotion'])) {
                    $query->where('is_from_promotion', $optionals['is_from_promotion']);
                }
                if (isset($optionals['is_from_coupon'])) {
                    $query->where('is_from_coupon', $optionals['is_from_coupon']);
                }
            })
            ->forceDelete();
    }

    static function getCarDataFromRentalLine($rental_id)
    {
        if (empty($rental_id)) {
            return [];
        }
        $car_data = RentalLine::where('rental_id', $rental_id)->where('item_type', Product::class)->pluck('car_id');
        return $car_data;
    }

    static function getProductAdditionalsFromPromotion($promotion)
    {
        if (empty($promotion)) {
            return [];
        }
        $free_products = [];
        if (strcmp($promotion->discount_type, DiscountTypeEnum::FREE_ADDITIONAL_PRODUCT) == 0) {
            $free_product_relations = PromotionFreeProductAdditional::where('promotion_id', $promotion->id)->get();
            foreach ($free_product_relations as $key => $free_product_relation) {
                $product_additional = $free_product_relation->productAdditional;
                if ($product_additional) {
                    $free_products[] = $product_additional;
                }
            }
        }
        return $free_products;
    }

    static function getRentalStatusList()
    {
        $status_list = collect([
            (object)[
                'id' => RentalStatusEnum::DRAFT,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::DRAFT),
                'value' => RentalStatusEnum::DRAFT,
                'class' => 'dark-blue',
            ],
            (object)[
                'id' => RentalStatusEnum::PENDING,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::PENDING),
                'value' => RentalStatusEnum::PENDING,
                'class' => 'warning',
            ],
            (object)[
                'id' => RentalStatusEnum::PAID,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::PAID),
                'value' => RentalStatusEnum::PAID,
                'class' => 'success',
            ],
            (object)[
                'id' => RentalStatusEnum::SUCCESS,
                'name' => __('short_term_rentals.status_' . RentalStatusEnum::SUCCESS),
                'value' => RentalStatusEnum::SUCCESS,
                'class' => 'primary',
            ],
        ]);
        return $status_list;
    }

    static function formatCarList($car_list)
    {
        $car_list->map(function ($car) {
            $car->can_select = true;
            $car->checked = false;
            $car->sub_name = $car->license_plate;
            $car->name = $car->car_class_full_name;
            $car_images = $car->getMedia('car_images');
            $car_images = get_medias_detail($car_images);
            $car_image = sizeof($car_images) > 0 ? $car_images[0] : null;
            $car->image = $car_image;
        });
        return $car_list;
    }

    public static function getManagementRentalCars($rental_id)
    {
        $rental = Rental::find($rental_id);
        if (!$rental) {
            return [];
        }
        $service_type_id = $rental->service_type_id;
        $product_id = $rental->product_id;
        $rm = new RentalCarManagement($service_type_id);
        $selected_car_array = RentalTrait::getCarRentalLineArray($rental_id);
        $car_list = $rm->getRentalCars($service_type_id, $product_id);
        $car_list = RentalTrait::formatCarList($car_list);
        $car_list = $car_list->filter(function ($item) use ($selected_car_array) {
            if (in_array($item->id, $selected_car_array)) {
                return $item;
            }
        })->values();
        return $car_list;
    }

    static function getRentalCheckInList($rental_id, $car_id)
    {
        $rental_checkins = RentalCheckIn::where('rental_id', $rental_id)
            ->where('car_id', $car_id)
            ->get()
            ->map(function ($item) {
                if ($item->location_id) {
                    $location_name = Location::find($item?->location_id);
                    $item->default_name = $location_name?->name;
                }
                if ($item->location_name) {
                    $item->location_id = $item->location_name;
                }
                return $item;
            });
        return $rental_checkins;
    }

    static function createRentalLocation($id = null, $rental_id = null, $car_id = null, $location_id = null, $optionals = [])
    {

        $rental_checkin = RentalCheckIn::findOrNew($id);
        $rental_checkin->rental_id = $rental_id;
        $rental_checkin->location_id = !empty($optionals['location_name']) ? null : $location_id;
        $rental_checkin->car_id = $car_id;
        $rental_checkin->location_name = $optionals['location_name'] ?? null;
        $rental_checkin->lat = $optionals['lat'] ?? null;
        $rental_checkin->lng = $optionals['lng'] ?? null;
        $rental_checkin->arrived_at = $optionals['arrived_at'] ?? null;
        $rental_checkin->departured_at = $optionals['departured_at'] ?? null;
        $rental_checkin->save();
    }

    static function getTypeInvoice()
    {
        return collect([
            [
                'id' => InvoiceTypeEnum::FIX,
                'name' => 'fix วัน',
                'value' => InvoiceTypeEnum::FIX,
            ],
            [
                'id' => InvoiceTypeEnum::MONTH,
                'name' => 'ชนเดือน',
                'value' => InvoiceTypeEnum::MONTH,
            ]
        ]);
    }

    static function getInvoiceDateLength()
    {
        $days_list = [];
        for ($i = 1; $i <= 31; $i++) {
            $days_list[] = (object)[
                'id' => $i,
                'name' => $i,
                'value' => $i,
            ];
        }
        return collect($days_list);
    }
}
