<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLine extends Model
{
    use HasFactory, PrimaryUuid;
    protected $table = 'purchase_order_lines';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id'
    ];

    public function purchaseRequisitionLine() {
        return $this->hasOne(PurchaseRequisitionLine::class, 'id', 'item_id');
    }

    public function importCarLine(){
        return $this->hasOne(ImportCarLine::class, 'id', 'po_line_id');
    }
}
