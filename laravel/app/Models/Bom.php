<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Kyslik\ColumnSortable\Sortable;

class Bom extends Model
{
    use HasFactory, PrimaryUuid, Sortable;
    protected $table = 'boms';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

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
                    $q2->where('boms.name', 'like', '%' . $s . '%');
                    $q2->orWhere('boms.remark', 'like', '%' . $s . '%');
                });
            }
            if (!empty($request->type)) {
                $q->where('boms.type', $request->type);
            }
            if (!empty($request->worksheet_no)) {
                $q->where('boms.id', $request->worksheet_no);
            }
        });
    }
}
