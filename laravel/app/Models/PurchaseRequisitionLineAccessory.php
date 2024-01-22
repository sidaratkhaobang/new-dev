<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\PrimaryUuid;

class PurchaseRequisitionLineAccessory extends Model
{
    use PrimaryUuid;

    protected $table = 'purchase_requisition_line_accessories';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
    ];

    public function accessory()
    {
        return $this->hasOne(Accessories::class, 'id', 'accessory_id');
    }
}
