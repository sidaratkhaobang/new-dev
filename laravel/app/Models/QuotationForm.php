<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class QuotationForm extends Model
{
    use HasFactory, PrimaryUuid, Creator, UpdateStatus, Sortable;

    protected $table = 'quotation_forms';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function quotation_form_check_list(): HasMany
    {
        return $this->hasMany(QuotationFormChecklist::class, 'quotation_form_id');
    }
}
