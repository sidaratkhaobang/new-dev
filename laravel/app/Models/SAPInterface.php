<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SAPInterface extends Model
{
    use HasFactory, PrimaryUuid;

    protected $table = 'sap_interfaces';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($request->transfer_type)) {
                $q->where('sap_interfaces.transfer_type', $request->transfer_type);
            }

            if (!empty($request->transfer_sub_type)) {
                $q->where('sap_interfaces.transfer_sub_type', $request->transfer_sub_type);
            }

            if (!empty($request->status)) {
                $q->where('sap_interfaces.status', $request->status);
            }
        });
    }
}
