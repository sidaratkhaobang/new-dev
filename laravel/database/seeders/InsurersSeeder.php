<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Insurer;

class InsurersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $handle = fopen(storage_path('olddb/DBInsurance.csv'), "r");
        $header = true;
        while ($col = fgetcsv($handle, 20000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 11) {
                continue;
            }
            $ref_id = trim($col[0]);
            $code = trim($col[1]);
            $insurance_name_th = trim($col[2]);
            $insurance_name_en = trim($col[3]);
            $insurance_tel = trim($col[4]);
            $insurance_fax = trim($col[5]);
            $insurance_address = trim($col[6]);
            $contact_name = trim($col[7]);
            $contact_tel = trim($col[9]);
            $contact_email = trim($col[10]);
            $insurance_web = trim($col[11]);
            $remark = trim($col[12]);

            $exists = Insurer::where('insurance_name_th', $insurance_name_th)->exists();
            if (!$exists) {
                $d = new Insurer();
                $d->code = $code;
                $d->insurance_name_th = $insurance_name_th;
                $d->insurance_name_en = $insurance_name_en;
                $d->insurance_tel = $insurance_tel;
                $d->insurance_fax = $insurance_fax;
                $d->insurance_address = $insurance_address;
                $d->contact_name = $contact_name;
                $d->contact_tel = $contact_tel;
                $d->contact_email = $contact_email;
                $d->insurance_web = $insurance_web;
                $d->remark = $remark;
                $d->save();
            }
        }
    }
}
