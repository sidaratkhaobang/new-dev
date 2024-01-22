<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionIncompatible extends Model
{
    use HasFactory;
    protected $table = 'promotions_incompatible';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'promotion_id',
        'promotion_incompatible_id',
    ];

    public function promotion()
    {
        return $this->hasOne(Promotion::class, 'id', 'promotion_incompatible_id');
    }
}
