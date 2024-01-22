<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;

class OrderPromotionCodeLine extends Model
{
    use HasFactory, PrimaryUuid;

    protected $table = 'order_promotion_code_lines';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
    ];

    public function promotionCode()
    {
        return $this->hasMany(PromotionCode::class, 'promotion_code_id', 'id');
    }
}
