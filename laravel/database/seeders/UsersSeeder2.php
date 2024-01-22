<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use App\Models\Role;
use App\Models\Section;
use App\Models\Department;

class UsersSeeder2 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/users2.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }
            $name = trim($col[0]);
            $email = trim($col[1]);
            $role_name = trim($col[2]);
            $section_code = trim($col[3]);
            $department_code = trim($col[4]);
            $branch_name = trim($col[5]);

            /* $this->command->info('role_name : ' . $role_name);
            $this->command->info('branch_name : ' . $branch_name);
            $this->command->info('department_name : ' . $department_name); */

            $role = Role::where('name', $role_name)->first();
            /* if (!empty($role)) {
                $this->command->info('role_name : ' . $role_name);
            } else {
                $this->command->error('role_name : ' . $role_name);
            } */

            $section = Section::where('code', $section_code)->first();
            /* if (!empty($section)) {
                $this->command->info('section : ' . $section_code);
            } else {
                $this->command->error('section : ' . $section_code);
            } */

            $department = Department::where('code', $department_code)->first();
            /* if (!empty($department)) {
                $this->command->info('department : ' . $department_code);
            } else {
                $this->command->error('department : ' . $department_code);
            } */

            $branch = Branch::where('name', $branch_name)->first();

            if (empty($name)) {
                continue;
            }

            $d = User::where('name', $name)->first();
            if ($d) {
                $d->branch_id = $branch ? $branch->id : null;
                $d->role_id = $role ? $role->id : null;
                $d->department_id = $department ? $department->id : null;
                $d->section_id = $section ? $section->id : null;
                if (empty($d->email)) {
                    $exists = User::where('email', $email)->first();
                    if (!$exists) {
                        $d->email = $email;
                    }
                }
                $d->save();
            } else {
                $this->command->info('not exists : ' . $name);
            }
        }
    }
}
