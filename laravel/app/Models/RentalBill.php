<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use App\Models\Traits\Creator;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalBill extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, Sortable, Creator, SoftDeletes, UpdateStatus, InteractsWithMedia;
    protected $table = 'rental_bills';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'payment_method',
        'payment_remark',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class)->withDefault();
    }

    public function rentalLines()
    {
        return $this->hasMany(RentalLine::class, 'rental_bill_id', 'id');
    }

    public function quotation()
    {
        return $this->hasOne(Quotation::class, 'rental_bill_id');
    }

    public function rentalBillLines()
    {
        return $this->hasMany(RentalBillLine::class, 'rental_bill_id', 'id');
    }
}
