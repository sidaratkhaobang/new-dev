<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAdditionalRelation extends Model
{
    use HasFactory, PrimaryUuid;
    protected $table = 'products_addtionals_relation';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function product_additional()
    {
        return $this->hasOne(ProductAdditional::class, 'id', 'product_addtional_id');
    }
}
