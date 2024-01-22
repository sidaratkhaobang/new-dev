<?php

namespace Database\Seeders;

use App\Enums\CustomerTypeEnum;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Province;

class CustomersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $provinces = Province::select('ref_id', 'id')->pluck('id', 'ref_id')->toArray();

        $handle = fopen(storage_path('olddb/DBCustomer.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 96) {
                continue;
            }
            $id = trim($col[0]);
            $name = trim($col[13]);
            $customer_code = trim($col[2]);
            $debtor_code = trim($col[4]);
            //$tax_no = trim($col[5]);

            $_customer_type = trim($col[3]);
            $_customer_grade = trim($col[12]);
            $prefixname_th = trim($col[14]);
            $fullname_th = trim($col[15]);
            $prefixname_en = trim($col[17]);
            $fullname_en = trim($col[16]);
            $province_id = trim($col[20]);
            $address = trim($col[21]);
            $tel = trim($col[24]);
            $email = trim($col[22]);
            $status = trim($col[96]);
            $account_code = trim($col[113]);

            $status = ((strcmp($status, '1') == 0) ? STATUS_ACTIVE : STATUS_INACTIVE);

            if (empty($name)) {
                continue;
            }

            if (strcmp($status, STATUS_INACTIVE) == 0) {
                continue;
            }

            $customer_grade = 4;
            switch ($_customer_grade) {
                case 'A':
                    $customer_grade = 1;
                    break;
                case 'B':
                    $customer_grade = 2;
                    break;
                case 'C':
                    $customer_grade = 3;
                    break;
                case 'D':
                    $customer_grade = 4;
                    break;
            }

            $customer_type = CustomerTypeEnum::OTHER;
            if (strcmp($_customer_type, '200') == 0) {
                $customer_type = CustomerTypeEnum::GOVERNMENT;
            } else if (strcmp($_customer_type, '201') == 0) {
                $customer_type = CustomerTypeEnum::CORPORATION;
            } else if (strcmp($_customer_type, '202') == 0) {
                $customer_type = CustomerTypeEnum::PERSONAL;
            } else if (strcmp($_customer_type, '203') == 0) {
                $customer_type = CustomerTypeEnum::ANTICIPATE;
            }

            $exists = Customer::where('ref_id', $id)->exists();
            if (!$exists) {
                $d = new Customer();
                $d->name = $name;
                $d->customer_code = $customer_code;
                $d->debtor_code = $debtor_code;
                $d->account_code = $account_code;
                $d->customer_type = $customer_type;
                $d->customer_grade = $customer_grade;
                $d->prefixname_th = $prefixname_th;
                $d->fullname_th = $fullname_th;
                $d->prefixname_en = $prefixname_en;
                $d->fullname_en = $fullname_en;
                $d->province_id = (isset($provinces[intval($province_id)]) ? $provinces[intval($province_id)] : null);
                $d->address = $address;
                $d->tel = $tel;
                $d->email = $email;
                $d->status = $status;
                $d->ref_id = $id;
                $d->save();
            }
        }
    }
}
