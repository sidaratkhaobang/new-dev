<?php

namespace App\Models;

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

class PurchaseRequisition extends Model implements HasMedia, Auditable
{
    use HasFactory,  PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;
    use AuditableTrait;

    protected $table = 'purchase_requisitions';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = ['pr_no', 'rental_type', 'request_date', 'require_date', 'status'];
    public $sortableAs = ['total_amount', 'po_count'];

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
                    $q2->where('purchase_requisitions.pr_no', 'like', '%' . $s . '%');
                });
            }
            if (!empty($request->pr_no)) {
                $q->where('purchase_requisitions.id', $request->pr_no);
            }
            if (!empty($request->from_request_date) || !empty($request->to_request_date)) {
                $q->whereBetween('purchase_requisitions.request_date', [$request->from_request_date . " 00:00:00", $request->to_request_date . " 23:59:59"]);
            }
            if (!empty($request->from_require_date) || !empty($request->to_require_date)) {
                $q->whereBetween('purchase_requisitions.require_date', [$request->from_require_date . " 00:00:00", $request->to_require_date . " 23:59:59"]);
            }
        });
    }

    public function parent_pr()
    {
        return $this->hasOne(PurchaseRequisition::class, 'id', 'parent_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /* public function registerMediaConversions(Media $media = null): void
    {
        if (strcmp(env('APP_ENV'), 'local') == 0) {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff')->nonQueued();
        } else {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff');
        }
    } */

    public function purchaseRequisitionLine()
    {
        return $this->hasMany(PurchaseRequisitionLine::class, 'purchase_requisition_id', 'id');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function creditor()
    {
        return $this->belongsTo(Creditor::class, 'creditor_id', 'id');
    }
}
