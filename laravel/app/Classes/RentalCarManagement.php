<?php

namespace App\Classes;

use Exception;
use DateTime;
use DateInterval;
use DatePeriod;
use App\Models\Car;
use App\Models\CarBrand;
use App\Models\Rental;
use App\Models\RentalLine;
use App\Models\ProductCarType;
use App\Enums\RentalTypeEnum;
use App\Enums\CarEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class RentalCarManagement
{
    public $dates;
    public $dates_return;
    public $dates_rentals;
    public $dates_return_cars;
    public $service_type_id;
    public $car_ids;
    public $car_count;
    public $rentals;
    public $pickup_date;
    public $return_date;
    public $rental_type;

    public $times;

    public function __construct($service_type_id, $rental_type = null, $pickup_date = null, $return_date = null)
    {
        $this->dates = [];
        $this->dates_return = [];
        $this->dates_rentals = [];
        $this->dates_return_cars = [];
        $this->service_type_id = $service_type_id;
        $this->car_ids = [];
        $this->car_count = 0;
        $this->rentals = [];
        $this->pickup_date = $pickup_date;
        $this->return_date = $return_date;
        $this->rental_type = $rental_type;

        $this->times = [];
    }

    function getAvailablePickupDates()
    {
        // * prepare - get cars count
        $this->car_ids = $this->getAllCarIds($this->service_type_id);
        $this->car_count = sizeof($this->car_ids);

        // * prepare - generate dates range
        $this->dates = $this->generateDates($this->car_count);

        // * prepare - get all rentals
        $this->rentals = $this->getAllRentals($this->service_type_id, $this->car_ids);

        // * operate
        $this->dates = $this->reduceAvailableDates($this->rentals, $this->dates);

        $available_dates = $this->getAvailableSlots($this->dates);
        return $available_dates;
    }

    function getAvailablePickupTimes($pickup_date)
    {
        // * prepare - get cars count
        $this->car_ids = $this->getAllCarIds($this->service_type_id);
        $this->car_count = sizeof($this->car_ids);

        // * prepare - generate times range
        $this->times = $this->generateTimes($pickup_date, $this->car_count);

        // * prepare - get all rentals
        $this->rentals = $this->getAllRentals($this->service_type_id, $this->car_ids);

        // * operate
        $this->times = $this->reduceAvailableTimes($this->rentals, $this->times, $pickup_date);

        $available_times = $this->getAvailableSlots($this->times);
        return $available_times;
    }

    function getAvailableReturnDates($pickup_date)
    {
        // * prepare - get cars count
        $this->car_ids = $this->getAllCarIds($this->service_type_id);
        $this->car_count = sizeof($this->car_ids);

        // * prepare - generate dates range
        $this->dates = $this->generateDates($this->car_count);

        // * prepare - get all rentals
        $this->rentals = $this->getAllRentals($this->service_type_id, $this->car_ids);

        // * operate
        $this->dates = $this->clearAvailableDates($this->rentals, $this->dates, $pickup_date);

        $available_dates = $this->getAvailableSlots($this->dates);
        return $available_dates;
    }

    function getAvailableReturnTimes($return_date)
    {
        // * prepare - get cars count
        $this->car_ids = $this->getAllCarIds($this->service_type_id);
        $this->car_count = sizeof($this->car_ids);

        // * prepare - generate times range
        $this->times = $this->generateTimes($return_date, $this->car_count);

        // * prepare - get all rentals
        $this->rentals = $this->getAllRentals($this->service_type_id, $this->car_ids);

        // * operate
        $this->times = $this->clearAvailableTimes($this->rentals, $this->times, $return_date);

        $available_times = $this->getAvailableSlots($this->times);
        return $available_times;
    }

    function getAvailableCars($pickup_date, $pickup_time, $return_date, $return_time)
    {
        // * prepare - get cars count
        $this->car_ids = $this->getAllCarIds($this->service_type_id);
        $this->car_count = sizeof($this->car_ids);

        /* // * prepare - generate times range
        $this->times = $this->generateDatesTimes($pickup_date, $pickup_time, $return_date, $return_time, $this->car_ids); */

        // * prepare - get all rentals
        $this->rentals = $this->getRentalsByDates($pickup_date, $pickup_time, $return_date, $return_time, $this->service_type_id, $this->car_ids);
        // * operate
        $this->car_ids = $this->clearAvailableCarIds($this->rentals, $this->car_ids);
        return $this->car_ids;
    }

    function getRentalCars($service_type_id, $product_id, $optionals = [])
    {
        $car_brand_id = isset($optionals['car_brand_id']) ? $optionals['car_brand_id'] : null;
        $license_plate = isset($optionals['license_plate']) ? $optionals['license_plate'] : null;

        if (strcmp($car_brand_id, 'all') == 0) {
            $car_brand_id = null;
        }

        $product_car_types = !empty($product_id) ? $this->getProductCarTypes($product_id) : [];

        $cars = Car::select('cars.id', 'cars.license_plate')
            ->addSelect('car_classes.name as car_class_name', 'car_classes.full_name as car_class_full_name', DB::raw('false as checked'))

            // join
            ->leftjoin('car_classes', 'car_classes.id', '=', 'cars.car_class_id')
            ->leftjoin('car_types', 'car_types.id', '=', 'car_classes.car_type_id')
            ->leftjoin('car_brands', 'car_brands.id', '=', 'car_types.car_brand_id')

            // check service_type
            ->when(!empty($service_type_id), function ($query) use ($service_type_id) {
                $query->whereIn('cars.id', function ($q) use ($service_type_id) {
                    $q->select('cars_service_types.car_id')
                        ->from('cars_service_types')
                        ->where('cars_service_types.service_type_id', $service_type_id);
                });
            })

            // check car_class + car_type (from product)
            ->when((sizeof($product_car_types) > 0), function ($query) use ($product_car_types) {
                $query->whereIn('car_classes.car_type_id', $product_car_types);
            })

            // check car_brand (from filter search)
            ->when(!empty($car_brand_id), function ($query) use ($car_brand_id) {
                $query->where('car_brands.id', $car_brand_id);
            })

            // check license_plate (from filter search)
            ->when(!empty($license_plate), function ($query) use ($license_plate) {
                $query->where('cars.license_plate', 'like', '%' . $license_plate . '%');
            })

            // default where
            ->where('cars.rental_type', RentalTypeEnum::SHORT)
            ->whereIn('cars.status', [CarEnum::READY_TO_USE, CarEnum::NEWCAR])
            ->get();

        return $cars;
    }

    function getRentalCarBrands($service_type_id, $product_id, $optionals = [])
    {
        $product_car_types = $this->getProductCarTypes($product_id);

        $car_brands = CarBrand::selectRaw('car_brands.id,car_brands.name,COUNT(DISTINCT(cars.id)) as car_sum')

            // join
            ->leftjoin('car_types', 'car_types.car_brand_id', '=', 'car_brands.id')
            ->leftjoin('car_classes', 'car_classes.car_type_id', '=', 'car_types.id')
            ->leftjoin('cars', 'cars.car_class_id', '=', 'car_classes.id')

            // check service_type
            ->whereIn('cars.id', function ($query) use ($service_type_id) {
                $query->select('cars_service_types.car_id')
                    ->from('cars_service_types')
                    ->where('cars_service_types.service_type_id', $service_type_id);
            })

            // check car_class + car_type (from product)
            ->when((sizeof($product_car_types) > 0), function ($query) use ($product_car_types) {
                $query->whereIn('car_classes.car_type_id', $product_car_types);
            })

            // default where
            ->where('cars.rental_type', RentalTypeEnum::SHORT)
            ->whereIn('cars.status', [CarEnum::READY_TO_USE, CarEnum::NEWCAR])

            ->groupBy('car_brands.id', 'car_brands.name')
            ->get();
        return $car_brands;
    }

    function getProductCarTypes($product_id)
    {
        $product_car_types = ProductCarType::where('product_id', $product_id)->pluck('car_type_id')->toArray();
        return $product_car_types;
    }

    function getAllCarIds($service_type_id)
    {
        $rental_type = RentalTypeEnum::SHORT;
        if (!is_null($this->rental_type)) {
            $rental_type = $this->rental_type;
            $car_ids = Car::select('id')
                ->where('rental_type', RentalTypeEnum::SPARE)
                ->pluck('id')->toArray();
            return $car_ids;
        }
        $car_ids = Car::select('id')->whereIn('id', function ($query) use ($service_type_id) {
            $query->select('car_id')
                ->from('cars_service_types')
                ->where('service_type_id', $service_type_id);
        })
            ->where('rental_type', $rental_type)
            ->pluck('id')->toArray();
        return $car_ids;
    }

    /* function getAllSpareCarIds($service_type_id)
    {
        $car_ids = Car::select('id')
            ->where('rental_type', RentalTypeEnum::SPARE)
            ->pluck('id')->toArray();
        return $car_ids;
    } */

    function getAllRentals($service_type_id, $car_ids)
    {
        $rentals = Rental::select('id', 'pickup_date', 'return_date')->whereIn('id', function ($query) use ($car_ids) {
            $query->select('rental_id')
                ->from('rental_lines')
                ->whereIn('car_id', $car_ids);
        })
            ->where('service_type_id', $service_type_id)
            ->whereDate('return_date', '>=', date('Y-m-d'))
            ->get();
        return $rentals;
    }

    function getRentalsByDates($pickup_date, $pickup_time, $return_date, $return_time, $service_type_id, $car_ids)
    {
        /* $pickup_date_time = new DateTime($pickup_date . ' ' . $pickup_time);
        $pickup_date_time = $pickup_date_time->format('Y-m-d H:i:s');
        $return_date_time = new DateTime($return_date . ' ' . $return_time);
        $return_date_time = $return_date_time->format('Y-m-d H:i:s'); */
        $rentals = Rental::select('id', 'pickup_date', 'return_date')->whereIn('id', function ($query) use ($car_ids) {
            $query->select('rental_id')
                ->from('rental_lines')
                ->whereIn('car_id', $car_ids);
        })
            ->when((!empty($service_type_id)), function ($query) use ($service_type_id) {
                $query->where('service_type_id', $service_type_id);
            })
            ->where(function ($query) use ($pickup_date, $pickup_time, $return_date, $return_time) {
                $query->where(function ($query2) use ($pickup_date, $pickup_time, $return_date, $return_time) {
                    // $query2->whereDate('pickup_date', '<=', $pickup_date);
                    // $query2->whereDate('return_date', '>=', $pickup_date);
                    $query2->where('pickup_date', '<=', $pickup_date);
                    $query2->where('return_date', '>=', $pickup_date);
                });
                $query->orWhere(function ($query2) use ($pickup_date, $pickup_time, $return_date, $return_time) {
                    // $query2->whereDate('pickup_date', '<=', $return_date);
                    // $query2->whereDate('return_date', '>=', $return_date);
                    $query2->where('pickup_date', '<=', $return_date);
                    $query2->where('return_date', '>=', $return_date);
                });
                $query->orWhere(function ($query2) use ($pickup_date, $pickup_time, $return_date, $return_time) {
                    // $query2->whereDate('pickup_date', '>=', $pickup_date);
                    // $query2->whereDate('return_date', '<=', $return_date);
                    $query2->where('pickup_date', '>=', $pickup_date);
                    $query2->where('return_date', '<=', $return_date);
                });
                $query->orWhere(function ($query2) use ($pickup_date, $pickup_time, $return_date, $return_time) {
                    // $query2->whereDate('pickup_date', '<=', $pickup_date);
                    // $query2->whereDate('return_date', '>=', $return_date);
                    $query2->where('pickup_date', '<=', $pickup_date);
                    $query2->where('return_date', '>=', $return_date);
                });
            })
            ->get();
        return $rentals;
    }

    function reduceAvailableDates($rentals, $dates)
    {
        foreach ($rentals as $rental) {
            $start = new DateTime($rental->pickup_date);
            $end   = new DateTime($rental->return_date);
            //$end->modify('+1 day');
            $interval = new DateInterval('P1D');
            $period = new DatePeriod(
                $start,
                $interval,
                $end
            );
            foreach ($period as $key => $value) {
                if (isset($dates[$value->format('Y-m-d')])) {
                    $dates[$value->format('Y-m-d')]--;
                }
            }
        }
        return $dates;
    }

    function clearAvailableDates($rentals, $dates, $pickup_date)
    {
        $interval = new DateInterval('P1D');
        // clear date before current date
        $start_clear = new DateTime($pickup_date);
        $start_clear->modify('-30 day');
        $end_clear   = new DateTime($pickup_date);
        $end_clear->modify('+1 day');
        $period_clear = new DatePeriod(
            $start_clear,
            $interval,
            $end_clear
        );
        foreach ($period_clear as $key => $value) {
            if (isset($dates[$value->format('Y-m-d')])) {
                $dates[$value->format('Y-m-d')] = 0;
            }
        }

        foreach ($rentals as $rental) {
            $start = new DateTime($rental->pickup_date);
            //$start->modify('+1 day');
            $end   = new DateTime($rental->return_date);
            $end->modify('+35 day');
            $period = new DatePeriod(
                $start,
                $interval,
                $end
            );
            foreach ($period as $key => $value) {
                if (isset($dates[$value->format('Y-m-d')])) {
                    $dates[$value->format('Y-m-d')]--;
                }
            }
        }
        return $dates;
    }

    function reduceAvailableTimes($rentals, $times, $date)
    {
        foreach ($rentals as $rental) {
            $start = new DateTime($rental->pickup_date);
            $end   = new DateTime($rental->return_date);
            $interval = new DateInterval('PT30M');
            $period = new DatePeriod(
                $start,
                $interval,
                $end
            );
            foreach ($period as $key => $value) {
                if (strcmp($value->format('Y-m-d'), $date) != 0) {
                    continue;
                }
                if (isset($times[$value->format('H:i')])) {
                    $times[$value->format('H:i')]--;
                }
            }
        }
        return $times;
    }

    function clearAvailableTimes($rentals, $times, $date)
    {
        foreach ($rentals as $rental) {
            $start = new DateTime($rental->pickup_date);
            $end   = new DateTime($rental->return_date);
            $end->modify('+1 day');
            $interval = new DateInterval('PT30M');
            $period = new DatePeriod(
                $start,
                $interval,
                $end
            );
            foreach ($period as $key => $value) {
                if (strcmp($value->format('Y-m-d'), $date) != 0) {
                    continue;
                }
                if (isset($times[$value->format('H:i')])) {
                    $times[$value->format('H:i')]--;
                }
            }
        }
        return $times;
    }

    function clearAvailableCarIds($rentals, $car_ids)
    {
        foreach ($rentals as $rental) {
            $line_car_ids = RentalLine::where('rental_id', $rental->id)->where('status', STATUS_ACTIVE)->pluck('car_id')->toArray();
            // dd($line_car_ids);
            foreach ($line_car_ids as $id) {
                $key = array_search($id, $car_ids);
                if (strcmp($key, false) !== 0) {
                    unset($car_ids[$key]);
                }
            }
        }
        return $car_ids;
    }

    function clearAvailableSpareCarIds($rentals, $car_ids)
    {
        foreach ($rentals as $rental) {
            $line_car_ids = RentalLine::where('rental_id', $rental->id)->pluck('car_id')->toArray();
            // dd($line_car_ids);
            foreach ($line_car_ids as $id) {
                $key = array_search($id, $car_ids);
                if (strcmp($key, false) !== 0) {
                    unset($car_ids[$key]);
                }
            }
        }
        return $car_ids;
    }

    function getAvailableSlots($slots)
    {
        return collect($slots)->filter(function ($count, $date) {
            return ($count > 0 ? true : false);
        })->keys()->toArray();
    }

    function generateDates($car_count)
    {
        $start = new DateTime();
        $end   = new DateTime();
        $end->modify('+1 month');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod(
            $start,
            $interval,
            $end
        );
        $dates = [];
        foreach ($period as $key => $value) {
            $dates[$value->format('Y-m-d')] = $car_count;
            /* $this->dates_return[$value->format('Y-m-d')] = $car_count;
            $this->dates_rentals[$value->format('Y-m-d')] = [];
            $this->dates_return_cars[$value->format('Y-m-d')] = 1; */
        }
        return $dates;
    }

    function generateTimes($date, $car_count)
    {
        $start = new DateTime($date . ' 00:00');
        $end   = new DateTime($date . ' 00:00');
        $end->modify('+1 day');
        $interval = new DateInterval('PT30M');
        $period = new DatePeriod(
            $start,
            $interval,
            $end
        );
        $times = [];
        foreach ($period as $key => $value) {
            $times[$value->format('H:i')] = $car_count;
        }
        return $times;
    }

    /* function generateDatesTimes($pickup_date, $pickup_time, $return_date, $return_time, $car_ids)
    {
        $start = new DateTime($pickup_date . ' ' . $pickup_time);
        $end   = new DateTime($return_date . ' ' . $return_time);
        $interval = new DateInterval('PT30M');
        $period = new DatePeriod(
            $start,
            $interval,
            $end
        );
        $times = [];
        foreach ($period as $key => $value) {
            $times[$value->format('Y-m-d H:i')] = $car_ids;
        }
        return $times;
    } */
}
