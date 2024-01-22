<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;
use App\Enums\RentalBillTypeEnum;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\UpdateStatus;
use App\Enums\ReceiptTypeEnum;

class Rental extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, Sortable, Creator, InteractsWithMedia, SoftDeletes, UpdateStatus;

    protected $table = 'rentals';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'worksheet_no',
        'rental_type',
        'order_channel',
        'rental_state',
        'service_type_id',
        'pickup_date',
        'return_date',
        'branch_id',
        'product_id',
        'origin_id',
        'origin_lat',
        'origin_lng',
        'origin_name',
        'origin_address',
        'destination_id',
        'destination_lat',
        'destination_lng',
        'destination_name',
        'destination_address',
        'avg_distance',
        'customer_id',
        'customer_name',
        'customer_address',
        'customer_tel',
        'customer_email',
        'customer_zipcode',
        'customer_province_id',
        'is_required_tax_invoice',
        'promotion_id',
        'promotion_code_id',
        'subtotal',
        'discount',
        'coupon_discount',
        'vat',
        'total',
        'payment_method',
        'payment_remark',
        'payment_gateway',
        'is_paid',
        'payment_date',
        'payment_response_desc',
        'remark',
        'contract_no',
        'receipt_no',
        'status',
        'quotation_id',
        'payment_channel',
        'type_package',
    ];

    public $sortableAs = ['customer_name', 'worksheet_no', 'branch_name', 'service_type_name', 'created_at'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where(function ($q2) use ($s) {
                    $q2->where('rentals.worksheet_no', 'like', '%' . $s . '%');
                    $q2->orWhere('branches.name', 'like', '%' . $s . '%');
                    // $q2->orWhere('rentals.name', 'like', 'like', '%' . $s . '%');
                    $q2->orWhere('rentals.customer_name', 'like', '%' . $s . '%');
                    $q2->orWhere('service_types.name', 'like', '%' . $s . '%');
                });
            }
            if (!empty($request->branch_id)) {
                $q->where('branches.id', 'like', $request->branch_id);
            }
            if (!empty($request->worksheet_id)) {
                $q->orWhere('rentals.id', 'like', $request->worksheet_id);
            }
            if (!empty($request->customer_id)) {
                $q->orWhere('rentals.id', 'like', $request->customer_id);
            }
            if (!empty($request->service_type_id)) {
                $q->orWhere('service_types.id', 'like', $request->service_type_id);
            }
            if (!empty($request->from_date) || !empty($request->to_date)) {
                $q->orWhereBetween('rentals.created_at', [$request->from_date . " 00:00:00", $request->to_date . " 23:59:59"]);
            }
            if (!empty($request->status)) {
                $q->orWhere('rentals.status', 'like', $request->status);
            }
        });
    }

    // public function scopeInYear($query, $year)
    // {
    //     return $query->whereBetween('rentals.created_at', [
    //         Carbon::create($year)->startOfYear(),
    //         Carbon::create($year)->endOfYear(),
    //     ]);
    // }

    public function getJobTypeName()
    {
        return __('lang.job_type_rental');
    }

    public function getReceiptType()
    {
        return (boolval($this->is_required_tax_invoice)) ? ReceiptTypeEnum::TAX_INVOICE : ReceiptTypeEnum::RECEIPT;
    }

    public function getServiceTypeEnumAttribute()
    {
        return $this->serviceType->service_type;
    }

    public function getQuotationPrimaryAttribute()
    {
        return $this->quotations()->where('qt_type', RentalBillTypeEnum::PRIMARY)->orderBy('qt_no')->first();
    }

    public function quotation()
    {
        return $this->hasOne(Quotation::class, 'reference_id', 'id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'reference_id', 'id');
    }

    public function rentalLines()
    {
        return $this->hasMany(RentalLine::class, 'rental_id', 'id');
    }

    public function serviceType()
    {
        return $this->hasOne(ServiceType::class, 'id', 'service_type_id')->withDefault();
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function origin()
    {
        return $this->hasOne(Location::class, 'id', 'origin_id');
    }

    public function destination()
    {
        return $this->hasOne(Location::class, 'id', 'destination_id');
    }

    public function drivingJob()
    {
        return $this->morphMany(DrivingJob::class, 'job');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function createdBy()
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
