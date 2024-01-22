<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerGroup;

class CustomerGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/customer_relations.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 3) {
                continue;
            }
            $customer_code = trim($col[0]);
            $customer_name = trim($col[1]);
            $customer_group_name = trim($col[2]);

            if (empty($customer_code) || empty($customer_name) || empty($customer_group_name)) {
                continue;
            }

            $exists = CustomerGroup::where('name', $customer_group_name)->exists();
            if (!$exists) {
                $d = new CustomerGroup();
                $d->name = $customer_group_name;
                $d->status = STATUS_ACTIVE;
                $d->save();
            }
        }
    }
}
