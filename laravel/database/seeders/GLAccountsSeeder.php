<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GLAccount;
use App\Models\Branch;

class GLAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('init/database/gl_accounts_2.csv'), "r");

        $count = 0;
        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 4) {
                continue;
            }
            $name = trim($col[0]);
            $account = trim($col[1]);
            $type = trim($col[2]);
            $branch_name = trim($col[3]);
            $description = trim($col[4]);

            if (empty($name)) {
                continue;
            }
            $branch = Branch::where('name', $branch_name)->first();

            $exists = GLAccount::where('name', $name)->exists();
            if (!$exists) {
                $d = new GLAccount();
                $d->name = $name;
                $d->account = $account;
                $d->type = $type;
                $d->description = $description;
                $d->branch_id = $branch ? $branch->id : null;
                $d->save();
            } else {
                $d = GLAccount::where('name', $name)->first();
                $d->name = $name;
                $d->account = $account;
                $d->type = $type;
                $d->description = $description;
                $d->branch_id = $branch ? $branch->id : null;
                $d->save();
            }

            $count++;
        }
    }
}
