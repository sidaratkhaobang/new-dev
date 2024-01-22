<?php

namespace App\Models;

use App\Enums\ComparisonPriceStatusEnum;
use App\Enums\LongTermRentalStatusEnum;
use App\Enums\SpecStatusEnum;
use App\Models\Traits\Creator;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Builder;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class LongTermRental extends Model implements HasMedia, Auditable
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    use AuditableTrait;

    protected $table = 'lt_rentals';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = [
        'worksheet_no',
        'customer_name',
        'won_auction',
        'created_at',
        'status',
        'spec_status',
        'comparison_price_status',
        'rental_price_status',
        'offer_date',
        'job_type',
    ];

    public $sortableAs = ['qt_no', 'rental_type','lt_rental_type_name'];

    public function scopeBranch($query)
    {
        $user = Auth::user();
        return $query->where(function ($q) use ($user) {

            if ($user->branch && $user->branch->is_main == STATUS_ACTIVE) {
                // do nothing, let the query continue
            } else {
                $q->whereRaw(STATUS_DEFAULT); // return an empty result set if not main branch
            }
        });
    }

    public function scopeSearch($query, $s, $request)
    {

        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where(function ($q2) use ($s, $request) {
                    $q2->where('lt_rentals.worksheet_no', 'like', '%' . $s . '%');
                    $q2->orWhere('lt_rentals.customer_name', 'like', '%' . $s . '%');
                }
                );
            }
            if (!empty($request->worksheet_no)) {
                $q->where('lt_rentals.id', 'like', $request->worksheet_no);
            }
            if (!empty($request->customer)) {
                $q->where('lt_rentals.id', 'like', $request->customer);
            }
            if (!empty($request->lt_rental_type)) {
                $q->where('lt_rentals.lt_rental_type_id', $request->lt_rental_type);
            }
            if (!empty($request->from_offer_date) || !empty($request->to_offer_date)) {
                $q->whereBetween('lt_rentals.offer_date', [$request->from_offer_date . " 00:00:00", $request->to_offer_date . " 23:59:59"]);
            }
            if (!empty($request->status)) {
                if($request->status == LongTermRentalStatusEnum::QUOTATION_CONFIRM){
                    $q->where('quotations.status', "CONFIRM");
                }else{
                    $q->where('lt_rentals.status', $request->status);
                }
//                $q->where('lt_rentals.spec_status', $request->status);
            }
        });
    }

    public function getJobTypeName()
    {
        return __('lang.job_type_lt_rental');
    }

    /* public function registerMediaConversions(Media $media = null): void
    {
    if (strcmp(env('APP_ENV'), 'local') == 0) {
    $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff')->nonQueued();
    } else {
    $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff');
    }
    } */

    public function creditor()
    {
        return $this->belongsTo(Creditor::class, 'creditor_id', 'id');
    }

    public function rentalType()
    {
        return $this->hasOne(LongTermRentalType::class, 'id', 'lt_rental_type_id');
    }

    public function quotation()
    {
        return $this->hasOne(Quotation::class, 'id', 'quotation_id')->withDefault();
    }

    public function customer()
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    public function scopeBySpecStatus($query)
    {
        return $query->whereIn('spec_status', [SpecStatusEnum::DRAFT, SpecStatusEnum::REJECT, SpecStatusEnum::PENDING_CHECK]);
    }

    public function scopeBySpecStatusAccessory($query)
    {
        return $query->whereIn('spec_status', [SpecStatusEnum::ACCESSORY_CHECK]);
    }

    public function scopeBySpecStatusApprove($query)
    {
        return $query->whereIn('spec_status', [SpecStatusEnum::PENDING_REVIEW, SpecStatusEnum::CONFIRM, SpecStatusEnum::REJECT]);
    }

    public function scopeByComparePriceStatus($query)
    {
        return $query->where('spec_status', SpecStatusEnum::CONFIRM)
            ->whereIn('comparison_price_status', [ComparisonPriceStatusEnum::DRAFT, ComparisonPriceStatusEnum::CONFIRM]);
    }

    public function scopeByQuotationStatus($query)
    {
        return $query->where('spec_status', SpecStatusEnum::CONFIRM)
            ->where('comparison_price_status', ComparisonPriceStatusEnum::CONFIRM);
    }

    public function drivingJob()
    {
        return $this->morphMany(DrivingJob::class ,'job');
    }
    public function getLongTermRentalMonth()
    {
        return $this->hasMany(LongTermRentalMonth::class, 'lt_rental_id');
    }
}
