<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\Department;

class SectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/sections.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 2) {
                continue;
            }
            $name = trim($col[1]);
            $code = trim($col[2]);
            $department_code = substr($code, 0, 3);

            if (empty($name) || empty($code)) {
                continue;
            }

            $department = Department::where('code', $department_code)->first();
            if (empty($department)) {
                continue;
            }

            $exists = Section::where('code', $code)->exists();
            if (!$exists) {
                $d = new Section();
                $d->name = $name;
                $d->code = $code;
                $d->department_id = $department ? $department->id : null;
                $d->status = STATUS_ACTIVE;
                $d->save();
            }
        }
    }
}
