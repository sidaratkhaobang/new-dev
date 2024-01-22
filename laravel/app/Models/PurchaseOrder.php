<?php

namespace App\Models;

use App\Enums\POStatusEnum;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatusConfirm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Kyslik\ColumnSortable\Sortable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PurchaseOrder extends Model implements Auditable, HasMedia
{
    use HasFactory, PrimaryUuid, Creator, Sortable, InteractsWithMedia, SoftDeletes;
    use AuditableTrait;

    protected $table = 'purchase_orders';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];
    public $sortable = ['po_no', 'request_date', 'require_date', 'status'];
    public $sortableAs = ['total_amount', 'pr_no', 'rental_type',];

    // protected static function booted()
    // {
    //     static::addGlobalScope('mainBranchOnly', function (Builder $builder) {
    //         $user = Auth::user();
    //         if ($user->branch && $user->branch->is_main == STATUS_ACTIVE) {
    //             // do nothing, let the query continue
    //         } else {
    //             $builder->whereRaw(STATUS_DEFAULT); // return an empty result set if not main branch
    //         }
    //     });
    // }

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
                    $q2->where('purchase_orders.po_no', 'like', '%' . $s . '%');
                });
            }
            if (!empty($request->purchase_order_no)) {
                $q->where('purchase_orders.po_no', 'like', '%' . $request->purchase_order_no . '%');
            }

            if (!empty($request->from_delivery_date) || !empty($request->to_delivery_date)) {
                $q->whereBetween('purchase_orders.require_date', [$request->from_delivery_date . " 00:00:00", $request->to_delivery_date . " 23:59:59"]);
            }
        });
    }

    public function purchaseOrderLines()
    {
        return $this->hasMany(PurchaseOrderLine::class, 'purchase_order_id', 'id');
    }

    public function creditor()
    {
        return $this->belongsTo(Creditor::class, 'creditor_id', 'id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function purchaseRequisiton()
    {
        return $this->belongsTo(PurchaseRequisition::class, 'pr_id');
    }
}
