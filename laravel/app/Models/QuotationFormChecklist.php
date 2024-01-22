<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;

class QuotationFormChecklist extends Model
{
    use HasFactory, PrimaryUuid, UpdateStatus;

    protected $table = 'quotation_form_checklists';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];
}
