<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class LongTermRentalPRLine extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, InteractsWithMedia;

    protected $table = 'lt_rental_pr_lines';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];

    public function ltMonth()
    {
        return $this->hasOne(LongTermRentalMonth::class, 'id', 'lt_rental_month_id');
    }

    public function ltLine()
    {
        return $this->hasOne(LongTermRentalLine::class, 'id', 'lt_rental_line_id');
    }
}