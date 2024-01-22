<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class AuctionPlace extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'auction_places';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public $sortable = ['name', 'contact_name', 'address', 'status'];

    public function scopeSearch($query, $request)
    {
        return $query->where(function ($q) use ($request) {
            if (!empty($request->name)) {
                $q->orWhere('auction_places.name', $request->name);
            }
            if (!empty($request->contact_name)) {
                $q->orWhere('auction_places.contact_name', $request->contact_name);
            }
            if (!empty($request->status)) {
                $q->orWhere('auction_places.status', $request->status);
            }
        });
    }
}
