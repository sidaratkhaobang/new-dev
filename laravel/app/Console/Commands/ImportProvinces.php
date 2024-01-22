<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportProvinces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ud:import_provinces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Provinces and all data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->importGeographies();
        $this->importProvinces();
        $this->importAmphures();
        $this->importDistricts();

        return 0;
    }

    private function importGeographies()
    {
        $handle = fopen(storage_path('init/geographies.csv'), "r");

        $map_ref_id = [
            '1' => '4',
            '2' => '3',
            '3' => '5',
            '4' => '7',
            '5' => '2',
            '6' => '6',
        ];

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 2) {
                continue;
            }
            $id = trim($col[0]);
            $name = trim($col[1]);

            if (empty($id) || empty($name)) {
                continue;
            }

            $exists = DB::table('geographies')->where('id', $id)->exists();
            if (!$exists) {
                DB::table('geographies')->insert([
                    'id' => $id,
                    'name' => $name,
                    'ref_id' => $map_ref_id['' . $id]
                ]);
            }
        }
        $exists = DB::table('geographies')->where('id', '7')->exists();
        if (!$exists) {
            DB::table('geographies')->insert([
                'id' => '7',
                'name' => 'กรุงเทพและปริมณฑล',
                'ref_id' => '1'
            ]);
        }
    }

    private function importProvinces()
    {
        $handle = fopen(storage_path('init/provinces.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }
            $id = trim($col[0]);
            $code = trim($col[1]);
            $name_th = trim($col[2]);
            $name_en = trim($col[3]);
            $geography_id = trim($col[4]);

            if (empty($id) || empty($code) || empty($name_th)) {
                continue;
            }

            $exists = DB::table('provinces')->where('id', $id)->exists();
            if (!$exists) {
                DB::table('provinces')->insert([
                    'id' => $id,
                    'code' => $code,
                    'name_th' => $name_th,
                    'name_en' => $name_en,
                    'geography_id' => $geography_id,
                ]);
            }
        }

        // map ref_id

        $handle = fopen(storage_path('olddb/DBProvince.csv'), "r");

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
            $region_id = trim($col[1]);
            $name = trim($col[2]);

            if (empty($name)) {
                continue;
            }

            $exists = DB::table('provinces')->where('name_th', $name)->exists();
            if ($exists) {
                DB::table('provinces')->where('name_th', $name)->update([
                    'ref_id' => $id,
                ]);
            }
        }
    }

    private function importAmphures()
    {
        $handle = fopen(storage_path('init/amphures.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }
            $id = trim($col[0]);
            $code = trim($col[1]);
            $name_th = trim($col[2]);
            $name_en = trim($col[3]);
            $province_id = trim($col[4]);

            if (empty($id) || empty($code) || empty($name_th) || empty($name_en)) {
                continue;
            }

            $exists = DB::table('amphures')->where('id', $id)->exists();
            if (!$exists) {
                DB::table('amphures')->insert([
                    'id' => $id,
                    'code' => $code,
                    'name_th' => $name_th,
                    'name_en' => $name_en,
                    'province_id' => $province_id
                ]);
            }
        }
    }

    private function importDistricts()
    {
        $handle = fopen(storage_path('init/districts.csv'), "r");

        $header = true;
        while ($col = fgetcsv($handle, 5000, ",")) {
            if ($header) {
                $header = false;
                continue;
            }
            if (sizeof($col) < 5) {
                continue;
            }
            $id = trim($col[0]);
            $zip_code = trim($col[1]);
            $name_th = trim($col[2]);
            $name_en = trim($col[3]);
            $amphure_id = trim($col[4]);

            if (empty($id) || empty($zip_code) || empty($name_th) || empty($name_en)) {
                continue;
            }
            $exists = DB::table('districts')->where('id', $id)->exists();

            if (!$exists) {
                DB::table('districts')->insert([
                    'id' => $id,
                    'zip_code' => $zip_code,
                    'name_th' => $name_th,
                    'name_en' => $name_en,
                    'amphure_id' => $amphure_id
                ]);
            }
        }
    }
}
