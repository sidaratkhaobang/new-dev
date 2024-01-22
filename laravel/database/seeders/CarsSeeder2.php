<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Car;
use App\Enums\RentalTypeEnum;
use App\Enums\CarEnum;
use App\Enums\CreditorTypeEnum;
use App\Models\CarClass;
use App\Models\CarColor;
use App\Models\CarCategory;
use App\Models\CarCharacteristic;
use App\Models\Branch;
use App\Models\Creditor;
use App\Models\CreditorType;
use App\Models\CreditorTypeRelation;
use Illuminate\Support\Facades\DB;

class CarsSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $creditor_types = CreditorType::select('type', 'id')->pluck('id', 'type')->toArray();

        $rental_types = [
            __('cars.rental_type_' . RentalTypeEnum::SHORT) => RentalTypeEnum::SHORT,
            __('cars.rental_type_' . RentalTypeEnum::LONG) => RentalTypeEnum::LONG,
            __('cars.rental_type_' . RentalTypeEnum::REPLACEMENT) => RentalTypeEnum::REPLACEMENT,
            __('cars.rental_type_' . RentalTypeEnum::TRANSPORT) => RentalTypeEnum::TRANSPORT,
            __('cars.rental_type_' . RentalTypeEnum::OTHER) => RentalTypeEnum::OTHER,
            __('cars.rental_type_' . RentalTypeEnum::SPARE) => RentalTypeEnum::SPARE,
            __('cars.rental_type_' . RentalTypeEnum::BORROW) => RentalTypeEnum::BORROW,
        ];

        $car_status = [
            __('cars.status_' . CarEnum::DRAFT) => CarEnum::DRAFT,
            __('cars.status_' . CarEnum::NEWCAR) => CarEnum::NEWCAR,
            __('cars.status_' . CarEnum::NEWCAR_PENDING) => CarEnum::NEWCAR_PENDING,
            __('cars.status_' . CarEnum::EQUIPMENT) => CarEnum::EQUIPMENT,
            __('cars.status_' . CarEnum::LEASE) => CarEnum::LEASE,
            __('cars.status_' . CarEnum::PENDING_RETURN) => CarEnum::PENDING_RETURN,
            __('cars.status_' . CarEnum::ACCIDENT) => CarEnum::ACCIDENT,
            __('cars.status_' . CarEnum::REPAIR) => CarEnum::REPAIR,
            __('cars.status_' . CarEnum::PENDING_SALE) => CarEnum::PENDING_SALE,
            __('cars.status_' . CarEnum::READY_TO_USE) => CarEnum::READY_TO_USE,
            __('cars.status_' . CarEnum::CONTRACT_EXPIRED) => CarEnum::CONTRACT_EXPIRED,
            __('cars.status_' . CarEnum::SOLD_OUT) => CarEnum::SOLD_OUT,
            __('cars.status_' . CarEnum::PENDING_REVIEW) => CarEnum::PENDING_REVIEW,
            __('cars.status_' . CarEnum::PENDING_DELIVER) => CarEnum::PENDING_DELIVER,
        ];

        $index = 0;

        $handle = fopen(storage_path('init/database/cars2.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 9) {
                continue;
            }
            $code = trim($col[0]);
            $license_plate = trim($col[1]);
            $engine_no = trim($col[2]);
            $chassis_no = trim($col[3]);

            $car_class_name = trim($col[4]);
            $car_color_name = trim($col[5]);

            /* $rental_type = trim($col[6]); */

            $branch_name = trim($col[7]);
            $leasing_name = trim($col[8]);
            $registered_date = trim($col[9]);

            $car_category_name = trim($col[12]);
            $car_characteristic_name = trim($col[13]);
            $car_tax_exp_date = trim($col[14]);
            $purchase_price = trim($col[15]);

            $rental_type_name = trim($col[17]);
            $status_name = trim($col[19]);

            $current_mileage = trim($col[20]);
            $have_gps = trim($col[21]);
            $vid = trim($col[22]);
            $have_dvr = trim($col[23]);



            $check_name = $license_plate;
            if (empty($check_name)) {
                $check_name = $engine_no;
            }
            if (empty($check_name)) {
                $check_name = $chassis_no;
            }

            if (empty($check_name)) {
                continue;
            }



            // prepare master
            $car_class = CarClass::where('full_name', $car_class_name)->first();
            if (empty($car_class)) {
                if (!empty($car_class_name)) {
                    $this->command->warn('car_class not found : ' . $car_class_name);
                    $d1 = new CarClass();
                    $d1->name = $car_class_name;
                    $d1->full_name = $car_class_name;
                    $d1->save();
                }
            }

            $car_color = CarColor::where('name', $car_color_name)->first();
            if (empty($car_color)) {
                if (!empty($car_color_name)) {
                    $this->command->warn('car_color not found : ' . $car_color_name);
                    $d1 = new CarColor();
                    $d1->name = $car_color_name;
                    $d1->save();
                }
            }

            if (in_array($branch_name, ['กรุงเทพมหานคร', 'N/A'])) {
                $branch_name = 'กรุงเทพ';
            }
            if (in_array($branch_name, ['เชียงราย'])) {
                $branch_name = 'ท่าอากาศยานแม่ฟ้าหลวงเชียงราย';
            }
            $branch_name = 'สาขา' . $branch_name;
            $branch = Branch::where('name', $branch_name)->first();
            if (empty($branch)) {
                $this->command->warn('branch not found : "' . $branch_name . '"');
            }

            $leasing_name = trim($leasing_name, "'");
            if (strcmp($leasing_name, 'บริษัท โตโยต้า ลีสซิ่ง ( ประเทศไทย ) จำกัด') == 0) {
                $leasing_name = 'บริษัท โตโยต้าลีสซิ่ง (ประเทศไทย) จำกัด';
            }
            if (strcmp($leasing_name, 'ธนาคารธนชาต จำกัด ( มหาชน )') == 0) {
                $leasing_name = 'ธนาคารธนชาต จำกัด (มหาชน)';
            }
            if (strcmp($leasing_name, 'บริษัท กรุงไทย ไอบีเจ ลิสซิ่ง') == 0) {
                $leasing_name = 'บริษัท กรุงไทย ไอบีเจ ลีสซิ่ง จำกัด';
            }
            $leasing = Creditor::where('name', $leasing_name)->first();
            if (empty($leasing)) {
                if (!empty($leasing_name)) {
                    $this->command->warn('leasing not found : ' . $leasing_name);
                    $d1 = new Creditor();
                    $d1->name = $leasing_name;
                    $d1->save();
                    CreditorTypeRelation::insert([
                        'creditor_id' => $d1->id,
                        'creditor_type_id' => $creditor_types['' . CreditorTypeEnum::LEASING],
                    ]);
                }
            }

            $car_category = CarCategory::where('name', $car_category_name)->first();
            if (empty($car_category)) {
                if (!empty($car_category_name)) {
                    /* $this->command->warn('car_category not found : ' . $car_category_name);
                    $d1 = new CarCategory();
                    $d1->name = $car_category_name;
                    $d1->save(); */
                }
            }

            $car_characteristic = CarCharacteristic::where('name', $car_characteristic_name)->first();
            if (empty($car_characteristic)) {
                if (!empty($car_characteristic_name)) {
                    //$this->command->warn('car_characteristic not found : ' . $car_characteristic_name);
                    /* $d1 = new CarCharacteristic();
                    $d1->name = $car_category_name;
                    $d1->save(); */
                }
            }



            // prepare status
            if (empty($rental_type_name)) {
                $rental_type_name = 'รถเช่ายาว';
            }

            $rental_type = RentalTypeEnum::LONG;
            if (!isset($rental_types[$rental_type_name])) {
                //$this->command->warn('rental_types not found : ' . $rental_type_name);
            } else {
                $rental_type = $rental_types[$rental_type_name];
            }

            $status = CarEnum::READY_TO_USE;
            if (!isset($car_status[$status_name])) {
                $this->command->warn('status_name not found : ' . $status_name);
            } else {
                $status = $car_status[$status_name];
            }

            $registered_date = (!empty($registered_date) ? date('Y-m-d', strtotime($registered_date)) : null);
            $car_tax_exp_date = (!empty($car_tax_exp_date) ? date('Y-m-d', strtotime($car_tax_exp_date)) : null);
            $purchase_price = floatval($purchase_price);
            $current_mileage = floatval($current_mileage);

            $have_gps = boolval($have_gps);
            $have_dvr = boolval($have_dvr);

            //continue;

            $d = Car::where(function ($query) use ($license_plate, $engine_no, $chassis_no) {
                $query->where('license_plate', $license_plate);
                $query->orWhere('engine_no', $engine_no);
                $query->orWhere('chassis_no', $chassis_no);
            })->first();
            if (!$d) {
                $d = new Car();
                $d->license_plate = $license_plate;
                $d->engine_no = $engine_no;
                $d->chassis_no = $chassis_no;
            }
            $d->code = $code;
            $d->car_class_id = $car_class ? $car_class->id : null;
            $d->car_color_id = $car_color ? $car_color->id : null;
            $d->branch_id = $branch ? $branch->id : null;
            $d->leasing_id = $leasing ? $leasing->id : null;

            $d->car_category_id = $car_category ? $car_category->id : null;
            $d->car_characteristic_id = $car_characteristic ? $car_characteristic->id : null;

            $d->registered_date = $registered_date;
            $d->car_tax_exp_date = $car_tax_exp_date;
            $d->purchase_price = $purchase_price;

            $d->rental_type = $rental_type;
            $d->status = $status;

            $d->current_mileage = $current_mileage;
            $d->have_gps = $have_gps;
            $d->have_dvr = $have_dvr;
            if (!empty($vid)) {
                $d->vid = $vid;
            }
            $d->save();

            $this->command->info('update : ' . $index . ' ' . $d->license_plate);
            $index++;
        }
    }
}
