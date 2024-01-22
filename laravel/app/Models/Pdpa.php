<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;

class Pdpa extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable;

    protected $table = 'pdpas';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'version',
        'desciption_th',
        'desciption_en',
    ];

    public $sortable = ['consent_type','version'];


    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('pdpas.version', 'like', '%' . $s . '%');
                $q->orWhere('pdpas.consent_type', 'like', '%' . $s . '%');
            }
        });
    }
}
