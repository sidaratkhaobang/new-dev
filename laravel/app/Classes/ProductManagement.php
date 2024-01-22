<?php

namespace App\Classes;

use App\Enums\CalculateTypeEnum;
use App\Models\Car;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\ProductPriceCarClass;
use App\Models\ProductPriceDestination;
use App\Models\ProductPriceOrigin;
use App\Traits\DayTrait;
use Carbon\Carbon;
use DateTime;
use Exception;

class ProductManagement
{
    use DayTrait;

    public $service_type_id;
    public $type_package;
    public $is_application;

    public $pickup_date;
    public $return_date;
    public $order_hours;
    public $order_days;

    public $branch_id;
    public $origin_id;
    public $destination_id;
    public $error_message;

    public function __construct($service_type_id, $branch_id = null)
    {
        $this->service_type_id = $service_type_id;
        $this->type_package = null;
        $this->branch_id = $branch_id;
        $this->is_application = false;

        $this->pickup_date = null;
        $this->return_date = null;
        $this->order_hours = 0;
        $this->order_days = 0;

        $this->origin_id = null;
        $this->destination_id = null;
        $this->error_message = null;
    }

    private function _getMainQuery($product_id = null, $service_type_id = null, $branch_id = null, $type_package = null, $optionals = [])
    {
        // extract params
        $pickup_date_time = isset($optionals['pickup_date_time']) ? $optionals['pickup_date_time'] : null;

        // prepare data
        // TODO
        // start_booking_time + end_booking_time + reserve_booking_duration
        $current_date = date('Y-m-d');
        $current_date_time = date('Y-m-d H:i:s');
        $pickup_date = (!empty($pickup_date_time) ? date('Y-m-d', strtotime($pickup_date_time)) : null);
        $dayofweek = (!empty($pickup_date) ? date('w', strtotime($pickup_date)) : null);
        $pickup_time = (!empty($pickup_date_time) ? date('H:i:s', strtotime($pickup_date_time)) : null);

        $check_reserve = false;
        $hours_diff = 0;
        if (!empty($pickup_date_time)) {
            $check_reserve = true;
            $hours_diff = $this->getHoursDiff($current_date_time, $pickup_date_time);
        }

        // start main query
        $queryBuilder = Product::select('id', 'name', 'sku', 'calculate_type', 'standard_price')
            ->where(function ($query) use ($product_id) {
                if ((!empty($product_id))) {
                    $query->where('id', $product_id);
                }
            })
            ->where('service_type_id', $service_type_id)
            ->where('branch_id', $branch_id)
            /* ->where(function ($query) use ($branch_id) {
                if (!empty($branch_id)) {
                    $query->where('branch_id', $branch_id);
                    $query->orWhereNull('branch_id');
                }
            }) */
            ->where(function ($query) use ($dayofweek) {
                if ((!is_null($dayofweek))) {
                    if (strcmp($dayofweek, '0') == 0) {
                        $query->where('booking_day_sun', '1');
                    }
                    if (strcmp($dayofweek, '1') == 0) {
                        $query->where('booking_day_mon', '1');
                    }
                    if (strcmp($dayofweek, '2') == 0) {
                        $query->where('booking_day_tue', '1');
                    }
                    if (strcmp($dayofweek, '3') == 0) {
                        $query->where('booking_day_wed', '1');
                    }
                    if (strcmp($dayofweek, '4') == 0) {
                        $query->where('booking_day_thu', '1');
                    }
                    if (strcmp($dayofweek, '5') == 0) {
                        $query->where('booking_day_fri', '1');
                    }
                    if (strcmp($dayofweek, '6') == 0) {
                        $query->where('booking_day_sat', '1');
                    }
                }
            })
            // booking date
            ->where(function ($query) use ($current_date) {
                $query->whereNull('start_date');
                $query->orWhereDate('start_date', '<=', $current_date);
            })
            ->where(function ($query) use ($current_date) {
                $query->whereNull('end_date');
                $query->orWhereDate('end_date', '>=', $current_date);
            })
            // booking time
            ->where(function ($query) use ($pickup_time) {
                if (!empty($pickup_time)) {
                    $query->whereNull('start_booking_time');
                    $query->orWhereTime('start_booking_time', '<=', $pickup_time);
                }
            })
            ->where(function ($query) use ($pickup_time) {
                if (!empty($pickup_time)) {
                    $query->whereNull('end_booking_time');
                    $query->orWhereTime('end_booking_time', '>=', $pickup_time);
                }
            })
            ->where(function ($query) use ($check_reserve, $hours_diff) {
                if ($check_reserve) {
                    $query->whereNull('reserve_booking_duration');
                    $query->orWhere('reserve_booking_duration', '<=', $hours_diff);
                }
            })
            ->where(function ($query) {
                if ($this->is_application) {
                    $query->where('is_used_application', '1');
                }
            })
            ->where(function ($query) use ($type_package) {
                if (!empty($type_package)) {
                    if (strcmp($type_package, CalculateTypeEnum::DAILY) == 0) {
                        $query->wherein('calculate_type', [
                            CalculateTypeEnum::DAILY,
                            CalculateTypeEnum::FIXED,
                            CalculateTypeEnum::HOURLY
                        ]);
                    }
                    if (strcmp($type_package, CalculateTypeEnum::MONTHLY) == 0) {
                        $query->where('calculate_type', CalculateTypeEnum::MONTHLY);
                    }
                }

            })
            ->where('status', STATUS_ACTIVE);
        return $queryBuilder;
    }

    function getAvailableProducts($s = null, $optionals = [])
    {
        $query = $this->_getMainQuery(null, $this->service_type_id, $this->branch_id, $this->type_package, $optionals);
        if (!empty($s)) {
            $query->where('products.name', 'like', '%' . $s . '%');
        }
        $products = $query->get();
        return $products;
    }

    function find($product_id, $optionals = [])
    {
        if (empty($product_id)) {
            return null;
        }
        $query = $this->_getMainQuery($product_id, $this->service_type_id, $this->branch_id, $optionals);
        $product = $query->first();
        return $product;
    }

    function findPrice($product_id, $pickup_date_time, $return_date_time, $optionals = [])
    {
        // extract params
        $car_id = isset($optionals['car_id']) ? $optionals['car_id'] : null;

        // TODO
        // customer_group
        if (empty($product_id)) {
            throw new Exception('empty product_id');
        }
        $product = $this->find($product_id);
        if (empty($product)) {
            throw new Exception('empty product');
        }

        if (empty($pickup_date_time)) {
            throw new Exception('empty pickup_date_time');
        }
        if (empty($return_date_time)) {
            throw new Exception('empty return_date_time');
        }

        $order_hours = $this->getHoursDiff($pickup_date_time, $return_date_time);
        $order_days = $this->getDaysDiff($pickup_date_time, $return_date_time);
        if ($order_hours <= 0) {
            throw new Exception('empty order_hours');
        }

        $price = floatval($product->standard_price);

        // prepare data
        $current_date = date('Y-m-d');
        // $dayofweek = date('w');
        $dayofweek = ($this->pickup_date) ? date('w', strtotime($this->pickup_date)) : null;

        $origin_id = (string)$this->origin_id;
        $destination_id = (string)$this->destination_id;

        $car = null;
        $car_class_id = null;
        //$car_id = $rental_line ? $rental_line->car_id : null;
        if (!empty($car_id)) {
            $car = Car::find($car_id);
            if ($car) {
                $car_class_id = $car->car_class_id;
            }
        }

        $product_prices = ProductPrice::select('id', 'price')
            ->where('product_id', $product_id)
            ->where('status', STATUS_ACTIVE)
            ->when($dayofweek, function ($query) use ($dayofweek) {
                if (strcmp($dayofweek, '0') == 0) {
                    $query->where('booking_day_sun', '1');
                }
                if (strcmp($dayofweek, '1') == 0) {
                    $query->where('booking_day_mon', '1');
                }
                if (strcmp($dayofweek, '2') == 0) {
                    $query->where('booking_day_tue', '1');
                }
                if (strcmp($dayofweek, '3') == 0) {
                    $query->where('booking_day_wed', '1');
                }
                if (strcmp($dayofweek, '4') == 0) {
                    $query->where('booking_day_thu', '1');
                }
                if (strcmp($dayofweek, '5') == 0) {
                    $query->where('booking_day_fri', '1');
                }
                if (strcmp($dayofweek, '6') == 0) {
                    $query->where('booking_day_sat', '1');
                }
            })
            ->where(function ($query) use ($current_date) {
                $query->whereNull('start_date');
                $query->orWhereDate('start_date', '<=', $current_date);
            })
            ->where(function ($query) use ($current_date) {
                $query->whereNull('end_date');
                $query->orWhereDate('end_date', '>=', $current_date);
            })
            ->orderBy('priority')
            ->get();

        $product_prices = $product_prices->filter(function ($price) use ($origin_id, $destination_id, $car_class_id) {
            $success = true;
            // origin
            $origins = ProductPriceOrigin::where('product_price_id', $price->id)->get();
            if (sizeof($origins) > 0) {
                $origins = $origins->filter(function ($item) use ($origin_id) {
                    return (strcmp($item->origin_id, $origin_id) == 0);
                });
                if (sizeof($origins) <= 0) {
                    $success = false;
                }
            }
            // destination
            $destinations = ProductPriceDestination::where('product_price_id', $price->id)->get();
            if (sizeof($destinations) > 0) {
                $destinations = $destinations->filter(function ($item) use ($destination_id) {
                    return (strcmp($item->destination_id, $destination_id) == 0);
                });
                if (sizeof($destinations) <= 0) {
                    $success = false;
                }
            }
            // destination
            $car_classes = ProductPriceCarClass::where('product_price_id', $price->id)->get();
            if (sizeof($car_classes) > 0) {
                $car_classes = $car_classes->filter(function ($item) use ($car_class_id) {
                    return (strcmp($item->car_class_id, $car_class_id) == 0);
                });
                if (sizeof($car_classes) <= 0) {
                    $success = false;
                }
            }
            return $success;
        });

        // final
        if (sizeof($product_prices) > 0) {
            $price = floatval($product_prices[0]->price);
            if ($price < 0) {
                $price = 0;
            }
        }

        if (isset($optionals['unit_price'])) {
            return $price;
        }

        // cal type
        $calculate_type = $product->calculate_type;
        if (strcmp($calculate_type, CalculateTypeEnum::DAILY) == 0) {
            $price = $price * $order_days;
        } else if (strcmp($calculate_type, CalculateTypeEnum::HOURLY) == 0) {
            $price = $price * $order_hours;
        }

        return $price;
    }

    function getOrderAmount($product_id, $pickup_date_time, $return_date_time, $optionals = [])
    {
        $product = $this->find($product_id);
        if (empty($product)) {
            throw new Exception('empty product');
        }
        if (empty($pickup_date_time)) {
            throw new Exception('empty pickup_date_time');
        }
        if (empty($return_date_time)) {
            throw new Exception('empty return_date_time');
        }
        $order_amount = 0;
        $calculate_type = $product->calculate_type;
        if (strcmp($calculate_type, CalculateTypeEnum::DAILY) == 0) {
            $order_amount = $this->getDaysDiff($pickup_date_time, $return_date_time);
        } else if (strcmp($calculate_type, CalculateTypeEnum::HOURLY) == 0) {
            $order_amount = $this->getHoursDiff($pickup_date_time, $return_date_time);
        }
        return $order_amount;
    }

    function setBranchId($branch_id)
    {
        $this->branch_id = $branch_id;
    }

    function setIsApplication($is_application)
    {
        $this->is_application = $is_application;
    }

    function setDates($pickup_date, $return_date)
    {
        $this->pickup_date = $pickup_date;
        $this->return_date = $return_date;

        $date1 = new DateTime($pickup_date);
        $date2 = new DateTime($return_date);

        $diff = $date2->diff($date1);
        if (strcmp($diff->invert, '1') == 0) {
            $this->order_days = abs(intval($diff->d));
            $this->order_hours = abs(intval($diff->h)) + ($this->order_days * 24);
            if ((abs(intval($diff->h)) > 0) || (abs(intval($diff->i)) > 0)) {
                $this->order_days += 1;
            }
        }
    }

    function setLocations($origin_id, $destination_id)
    {
        $this->origin_id = $origin_id;
        $this->destination_id = $destination_id;
    }

    function setTypePackage($type_package)
    {
        $this->type_package = $type_package;
    }

    function validate($product_id)
    {
        $product = Product::find($product_id);

        if (empty($product)) {
            $this->error_message = $this->errorMsg('PRODUCT_NOT_FOUND');
            return false;
        }
        $pickup_date = $this->pickup_date;
        $return_date = $this->return_date;
        if (empty($pickup_date)) {
            $this->error_message = $this->errorMsg('PRODUCT_NOT_FOUND');
            return false;
        }

        if ($this->order_hours <= 0) {
            $this->error_message = $this->errorMsg('HOUR_ORDER_EMPTY');
            return false;
        }

        $dayofweek = date('N', strtotime($pickup_date));
        if (!($this->checkProductDateAvailable($dayofweek, $product))) {
            $this->error_message = $this->errorMsg('DAY_NOT_AVAILABLE');
            return false;
        }

        $pickup_time = Carbon::parse($pickup_date)->toTimeString();
        if ($product->start_booking_time > $pickup_time) {
            $this->error_message = $this->errorMsg('START_BOOKING_TIME') . ' ';
            $this->error_message .= $product->start_booking_time . ' ';
            $this->error_message .= __('short_term_rentals.up');
            return false;
        }

        if ($product->end_booking_time < $pickup_time) {
            $this->error_message = $this->errorMsg('END_BOOKING_TIME') . ' ';
            $this->error_message .= $product->end_booking_time . ' ';
            $this->error_message .= __('short_term_rentals.minute_abbrv');
            return false;
        }

        if (intval($product->reserve_booking_duration) > 0 && !empty($pickup_date)) {
            $hours_before_pickup_day = getHoursBetweenDate('now', $pickup_date);
            if ($hours_before_pickup_day < $product->reserve_booking_duration) {
                $this->error_message = $this->errorMsg('RESERVATION_HOUR') . ' ' . $product->reserve_booking_duration . ' ' . __('products.hour');
                return false;
            }
        }

        $fix_days = intval($product->fix_days);
        if ($fix_days > 0) {
            $fixed_return_date = Carbon::parse($pickup_date)->addDays($fix_days)->startOfDay();
            $return_date_start_of_day = Carbon::parse($return_date)->startOfDay();
            $is_same_day = $fixed_return_date->isSameDay($return_date_start_of_day);
            if (!$is_same_day) {
                $this->error_message = $this->errorMsg('FIX_DAYS');
                return false;
            }
        }
        return true;
    }

    function errorMsg($key)
    {
        $package = __('short_term_rentals.package');
        $message_bag = [
            'PRODUCT_NOT_FOUND' => __('short_term_rentals.error_product_not_found'),
            'PICKUP_DATE_FOUND' => __('short_term_rentals.error_pickup_date_not_found'),
            'DAY_NOT_AVAILABLE' => __('short_term_rentals.error_day_not_available'),
            'START_BOOKING_TIME' => __('short_term_rentals.error_start_booking_time'),
            'END_BOOKING_TIME' => __('short_term_rentals.error_end_booking_time'),
            'RESERVATION_HOUR' => __('short_term_rentals.error_reservation'),
            'FIX_DAYS' => __('short_term_rentals.error_fix_days'),
            'HOUR_ORDER_EMPTY' => __('short_term_rentals.hour_order_empty')
        ];
        return $package . ': ' . $message_bag[$key];
    }

    function checkProductDateAvailable($date, $product)
    {
        $avaliable = false;
        switch ($date) {
            case 1:
                $avaliable = ($product->booking_day_mon === STATUS_ACTIVE) ? true : false;
                break;
            case 2:
                $avaliable = ($product->booking_day_tue === STATUS_ACTIVE) ? true : false;
                break;
            case 3:
                $avaliable = ($product->booking_day_wed === STATUS_ACTIVE) ? true : false;
                break;
            case 4:
                $avaliable = ($product->booking_day_thu === STATUS_ACTIVE) ? true : false;
                break;
            case 5:
                $avaliable = ($product->booking_day_fri === STATUS_ACTIVE) ? true : false;
                break;
            case 6:
                $avaliable = ($product->booking_day_sat === STATUS_ACTIVE) ? true : false;
                break;
            case 7:
                $avaliable = ($product->booking_day_sun === STATUS_ACTIVE) ? true : false;
                break;
            default:
                $avaliable = false;
        }
        return $avaliable;
    }

    function getReturnDate($product_id, $pickup_date, $return_date)
    {
        $product = $this->find($product_id);
        $fix_days = intval($product->fix_days);
        $fix_return_time = intval($product->fix_return_time);
        $fix_return_date = Carbon::parse($return_date);
        if ($fix_days > 0) {
            $fix_return_date = Carbon::parse($pickup_date)->addDays($fix_days);
        }
        if ($fix_return_time) {
            $fix_return_time_arr = explode(":", $fix_return_time);
            $fix_return_date->hour($fix_return_time_arr[0]);
            $fix_return_date->minute($fix_return_time_arr[1]);
            $fix_return_date->second($fix_return_time_arr[2]);
        }
        return $fix_return_date->toDateTimeString();
    }
}
