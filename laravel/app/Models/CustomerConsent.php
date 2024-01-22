<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerConsent extends Model
{
    use HasFactory,  PrimaryUuid;

    protected $table = 'customer_consents';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'customer_id',
        'pdpa_id',
    ];
}
