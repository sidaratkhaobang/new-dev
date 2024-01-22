<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/USERDATA.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 6) {
                continue;
            }
            $id = trim($col[0]);
            $username = trim($col[1]);
            $password = trim($col[2]);
            $name = trim($col[3]);
            $department_id = trim($col[4]);

            if (empty($username)) {
                continue;
            }

            $exists = User::where('ref_id', $id)->exists();
            if (!$exists) {
                $user_department = Department::where('ref_id', $department_id)->first();
                $d = new User();
                $d->username = $username;
                $d->password = Hash::make($password);
                $d->name = $name;
                $d->user_department_id = $user_department ? $user_department->id : null;
                $d->ref_id = $id;
                $d->save();
            }
        }

        for ($i = 1; $i <= 10; $i++) {
            $username = 'test' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $exists = User::where('username', $username)->exists();
            if (!$exists) {
                $d = new User();
                $d->username = $username;
                $d->password = Hash::make('12345678!');
                $d->name = $username;
                $d->save();
            }
        }

        foreach (config('services.account_api') as $account) {
            $exists = User::where('username', $account['username'])->exists();
            if (!$exists) {
                $d = new User();
                $d->name = $account['username'];
                $d->username = $account['username'];
                $d->password = Hash::make($account['password']);
                $d->status = $account['status'];
                $d->save();
            }
        }
    }
}
