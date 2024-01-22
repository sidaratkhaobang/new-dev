<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomerGroup;
use App\Models\Customer;
use App\Models\CustomerGroupRelation;

class CustomersGroupsRelationSeeder extends Seeder
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

            $customer = Customer::where('customer_code', $customer_code)->first();
            if ($customer) {
                $customer_group = CustomerGroup::where('name', $customer_group_name)->first();
                if ($customer_group) {
                    $exists = CustomerGroupRelation::where('customer_id', $customer->id)->where('customer_group_id', $customer_group->id)->exists();
                    if (!$exists) {
                        $d = new CustomerGroupRelation();
                        $d->customer_id = $customer->id;
                        $d->customer_group_id = $customer_group->id;
                        $d->save();
                    }
                } else {
                    $this->command->info('customer_group not found : ' . $customer_group_name);
                }
            } else {
                //$this->command->info('chassis_no not found : ' . $chassis_no);
            }
        }
    }
}
