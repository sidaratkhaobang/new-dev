<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/banks.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 4) {
                continue;
            }
            $code = trim($col[0]);
            $name = trim($col[1]);
            $key = trim($col[2]);
            $detail = trim($col[3]);

            if (empty($name) || empty($key)) {
                continue;
            }

            $bank = Bank::firstOrNew(['key' => $key]);
            $bank->key = $key;
            $bank->name = $name;
            $bank->code = $code;
            $bank->detail = $detail;
            $bank->status = STATUS_ACTIVE;
            $bank->save();
        }
    }
}