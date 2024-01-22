<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Creator;

class CreditNote extends Model
{
    use HasFactory, PrimaryUuid, Creator, UpdateStatus, Sortable, SoftDeletes;
    protected $table = 'credit_notes';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function scopeSearch($query, $s, $request)
    {
        return $query->where(function ($q) use ($s, $request) {
            if (!empty($s)) {
                $q->where('credit_notes.credit_note_no', 'like', '%' . $s . '%');
            }
            if (!empty($request->credit_note_id)) {
                $q->where('credit_notes.id', $request->m_credit_notesflow_id);
            }
            if (!empty($request->customer_id)) {
                $q->where('credit_notes.customer_id', $request->customer_id);
            }
            if (!empty($request->status)) {
                $q->where('credit_notes.status', $request->status);
            }
        });
    }
}
