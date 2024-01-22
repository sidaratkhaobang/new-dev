<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/departments.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 2) {
                continue;
            }
            $name = trim($col[0]);
            $code = trim($col[1]);

            if (empty($name) || empty($code)) {
                continue;
            }

            $exists = Department::where('code', $code)->exists();
            if (!$exists) {
                $d = new Department();
                $d->name = $name;
                $d->code = $code;
                $d->status = STATUS_ACTIVE;
                $d->save();
            }
        }
    }
}
