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

class BillSlip extends Model
{
    use HasFactory, PrimaryUuid, Sortable, Creator, UpdateStatus, InteractsWithMedia, SoftDeletes;

    protected $table = 'billing_slips';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function bill_slip_line()
    {
        return $this->hasMany(BillSlipLine::class, 'billing_slip_id', 'id');
    }

    public function creditor()
    {
        return $this->hasOne(Creditor::class, 'id', 'center_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'bill_recipient');
    }
}
