<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ImportCar extends Model
{
    use HasFactory, PrimaryUuid, Sortable, Creator;
    protected $table = 'import_cars';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
    ];

    // public $sortable = ['purchase_count'];
    public $sortableAs = ['total_amount', 'pr_no','po_no', 'rental_type','total','creditor_name'];

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

    public function purchaseOrder()
    {
        return $this->hasOne(PurchaseOrder::class, 'id', 'po_id');
    }

}
