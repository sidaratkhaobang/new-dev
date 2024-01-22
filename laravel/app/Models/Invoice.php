<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;
    protected $table = 'invoices';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];
    
    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('invoices.invoice_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->invoice_id)) {
                $q->where('invoices.id', 'like', $request->invoice_id);
            }
        });
    }
}
