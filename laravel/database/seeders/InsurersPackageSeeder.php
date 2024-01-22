<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InsurancePackage;
class InsurersPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $handle = fopen(storage_path('olddb/DBInsurersPackage.csv'), "r");
        $header = false;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            
            $exists = InsurancePackage::where('name', $col[0])->exists();
            if (!$exists) {
                $d = new InsurancePackage;
                $d->name = $col[0];
                $d->tpbi_person = str_replace(',', '', $col[1]);
                $d->tpbi_aggregate = str_replace(',', '', $col[2]);
                $d->tppd_aggregate = str_replace(',', '', $col[3]);
                $d->pa_driver = str_replace(',', '', $col[4]);
                $d->pa_passenger = str_replace(',', '', $col[5]);
                $d->medical_exp = str_replace(',', '', $col[6]);
                $d->baibond = str_replace(',', '', $col[7]);
                $d->deductible = str_replace(',', '', $col[8]);
                $d->save();
            }
        }
    }
}
