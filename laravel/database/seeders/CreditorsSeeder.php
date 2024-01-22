<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Creditor;
use App\Models\CreditorType;
use Illuminate\Support\Facades\DB;
use App\Enums\CreditorTypeEnum;
use App\Models\Province;

class CreditorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $creditor_types = CreditorType::select('type', 'id')->pluck('id', 'type')->toArray();
        $provinces = Province::select('ref_id', 'id')->pluck('id', 'ref_id')->toArray();

        $handle = fopen(storage_path('olddb/DBCreditor.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 25) {
                continue;
            }
            $id = trim($col[0]);
            $code = trim($col[1]);
            $name = trim($col[2]);
            $region_id = trim($col[3]);
            $province_id = trim($col[4]);
            $address = trim($col[5]);
            $tel = trim($col[6]);
            $mobile = trim($col[7]);
            $fax = trim($col[8]);

            $contact_name = trim($col[9]);
            $contact_position = trim($col[10]);
            $tax_no = trim($col[11]);
            $credit_terms = trim($col[12]);

            $type_leasing = trim($col[13]);
            $type_service = trim($col[14]);
            $type_dealer = trim($col[15]);
            $type_other = trim($col[16]);
            $type_accessory = trim($col[17]);

            $payment_condition = trim($col[19]);
            $remark = trim($col[20]);
            $status = trim($col[21]);
            $authorized_sign = trim($col[22]);
            $contact_address = trim($col[25]);

            $status = ((strcmp($status, '1') == 0) ? STATUS_ACTIVE : STATUS_INACTIVE);

            if (empty($name)) {
                continue;
            }

            $exists = Creditor::where('ref_id', $id)->exists();
            if (!$exists) {
                $d = new Creditor();
                $d->code = $code;
                $d->name = $name;
                $d->province_id = (isset($provinces[intval($province_id)]) ? $provinces[intval($province_id)] : null);
                $d->address = $address;
                $d->tel = $tel;
                $d->mobile = $mobile;
                $d->fax = $fax;
                $d->contact_name = $contact_name;
                $d->contact_position = $contact_position;
                $d->contact_address = $contact_address;
                $d->tax_no = $tax_no;
                $d->credit_terms = $credit_terms;
                $d->payment_condition = $payment_condition;
                $d->authorized_sign = $authorized_sign;
                $d->remark = $remark;
                $d->status = STATUS_ACTIVE;
                $d->ref_id = $id;
                $d->save();

                // maping type
                if (strcmp($type_leasing, '3101') == 0) {
                    DB::table('creditors_types_relation')->insert([
                        'creditor_id' => $d->id,
                        'creditor_type_id' => $creditor_types['' . CreditorTypeEnum::LEASING],
                    ]);
                }
                if (strcmp($type_service, '3201') == 0) {
                    DB::table('creditors_types_relation')->insert([
                        'creditor_id' => $d->id,
                        'creditor_type_id' => $creditor_types['' . CreditorTypeEnum::SERVICE],
                    ]);
                }
                if (strcmp($type_dealer, '3401') == 0) {
                    DB::table('creditors_types_relation')->insert([
                        'creditor_id' => $d->id,
                        'creditor_type_id' => $creditor_types['' . CreditorTypeEnum::DEALER],
                    ]);
                }
                if (strcmp($type_other, '3301') == 0) {
                    DB::table('creditors_types_relation')->insert([
                        'creditor_id' => $d->id,
                        'creditor_type_id' => $creditor_types['' . CreditorTypeEnum::OTHER],
                    ]);
                }
                if (strcmp($type_accessory, '3501') == 0) {
                    DB::table('creditors_types_relation')->insert([
                        'creditor_id' => $d->id,
                        'creditor_type_id' => $creditor_types['' . CreditorTypeEnum::ACCESSORIES],
                    ]);
                }
            }
        }
    }
}
