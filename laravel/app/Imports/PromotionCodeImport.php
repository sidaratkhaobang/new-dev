<?php

namespace App\Imports;

use App\Models\PromotionCode;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PromotionCodeImport implements ToCollection
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public $promotion_id;
    public $start_sale_date;
    public $end_sale_date;

    public function __construct($promotion_id, $start_sale_date, $end_sale_date)
    {
        $this->promotion_id = $promotion_id;
        $this->start_sale_date = $start_sale_date;
        $this->end_sale_date = $end_sale_date;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            PromotionCode::create([
                'promotion_id' => $this->promotion_id,
                'code' => $row[0],
                'start_sale_date' => $this->start_sale_date,
                'end_sale_date' => $this->end_sale_date,
            ]);
        }
    }
}
