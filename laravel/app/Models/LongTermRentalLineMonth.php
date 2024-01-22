<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LongTermRentalLineMonth extends Model
{
    use HasFactory;

    protected $table = 'lt_rental_lines_months';
    protected $primaryKey = 'lt_rental_line_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
