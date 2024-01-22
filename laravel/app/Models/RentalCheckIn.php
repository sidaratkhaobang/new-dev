<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;

class RentalCheckIn extends Model
{
    use HasFactory, PrimaryUuid, Sortable;

    protected $table = 'rental_checkins';
    public $incrementing = false;
    public $timestamps = true;
    protected $keyType = 'string';
}
