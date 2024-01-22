<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptLine extends Model
{
    use HasFactory, PrimaryUuid;

    protected $table = 'receipt_lines';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];

    public function reference()
    {
        return $this->morphTo();
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'id', 'receipt_id');
    }
}
