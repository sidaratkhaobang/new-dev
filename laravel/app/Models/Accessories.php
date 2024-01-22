<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Accessories extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'accessories';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'price',
        'version',
    ];
    public $sortable = ['code', 'name', 'version'];
    public $sortableAs = ['dealer_name'];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('accessories.code', 'like', '%' . $s . '%');
                $q->orWhere('accessories.name', 'like', '%' . $s . '%');
                $q->orWhere('accessories.version', 'like', '%' . $s . '%');
                $q->orWhere('accessories.price', 'like', '%' . $s . '%');
            }
            if (!empty($request->creditor_id)) {
                $q->where('creditors.id', $request->creditor_id);
            }
        });
    }

    public function creditor()
    {
        return $this->hasOne(Creditor::class, 'id', 'creditor_id');
    }
}
