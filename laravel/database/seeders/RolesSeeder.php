<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\Department;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/roles.csv'), "r");

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
            $section_code = trim($col[2]);
            $department_code = trim($col[3]);

            if (empty($name)) {
                continue;
            }

            $department = Department::where('code', $department_code)->first();
            if (empty($department)) {
                continue;
            }

            $section = Section::where('code', $section_code)->first();
            /* if (empty($section)) {
                continue;
            } */

            $exists = Role::where('name', $name)->exists();
            if (!$exists) {
                $d = new Role();
                $d->name = $name;
                $d->department_id = $department ? $department->id : null;
                $d->section_id = $section ? $section->id : null;
                $d->status = STATUS_ACTIVE;
                $d->save();
            }
        }
    }
}
