<?php

namespace Database\Seeders;

use App\Models\Leasing;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeasingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $datas = [
            [
                'name' => 'ธนาคารธนชาต จำกัด (มหาชน)',
                'is_true_leasing' => false,
            ],
            [
                'name' => 'บริษัท ฮิตาชิ แคปปิตอล (ประเทศไทย) จำกัด',
                'is_true_leasing' => false,
            ],
            [
                'name' => 'บริษัท โตโยต้า ลีสซิ่ง (ประเทศไทย) จำกัด',
                'is_true_leasing' => false,
            ],
            [
                'name' => 'บริษัท ลีสซิ่งกสิกรไทย จำกัด',
                'is_true_leasing' => false,
            ],
            [
                'name' => 'บริษัท ทรู ลีสซิ่ง จำกัด',
                'is_true_leasing' => true,
            ],
            [
                'name' => 'บริษัท กรุงไทย ไอ บี เจ ลีสซิ่ง จำกัด',
                'is_true_leasing' => false,
            ],
        ];

        // To be removed
        DB::table('leasings')->delete();

        foreach ($datas as $data) {
            $d = Leasing::firstOrNew(['name' => $data['name']]);
            $d->name = $data['name'];
            $d->is_true_leasing = $data['is_true_leasing'];
            $d->save();
        }
    }
}