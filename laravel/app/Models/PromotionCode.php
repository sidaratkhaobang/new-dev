<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class PromotionCode extends Model
{
    use HasFactory,  PrimaryUuid;

    protected $table = 'promotion_codes';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'promotion_id',
        'code',
        'start_sale_date',
        'end_sale_date',
    ];

    public function promotion()
    {
        return $this->hasOne(Promotion::class, 'id', 'promotion_id');
    }
}
