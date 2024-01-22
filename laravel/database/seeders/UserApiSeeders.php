<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserApiSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = config('services.account_api');

        foreach ($users as $user) {
            $username = trim($user['username']);
            $password = trim($user['password']);

            $d = User::where('username', $username)->first();
            if (empty($d)) {
                $d = new User();
                $d->username = $username;
                $d->password = Hash::make($password);
                $d->name = $username;
                $d->save();
            } else {
                $d->password = Hash::make($password);
                $d->name = $username;
                $d->save();
            }
        }
    }
}
