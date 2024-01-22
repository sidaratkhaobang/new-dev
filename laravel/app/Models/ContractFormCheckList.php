<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractFormCheckList extends Model
{
    use HasFactory, PrimaryUuid, UpdateStatus;

    protected $table = 'contract_form_check_lists';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];
}
