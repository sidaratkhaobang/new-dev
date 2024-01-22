<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class ComparisonPrice extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, InteractsWithMedia;
    protected $table = 'comparison_prices';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id'
    ];

    public function creditor()
    {
        return $this->hasOne(Creditor::class, 'id', 'creditor_id');
    }
}
