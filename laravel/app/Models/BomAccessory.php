<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;

class BomAccessory extends Model
{
    use HasFactory, PrimaryUuid, Sortable ;
    protected $table = 'bom_accessories';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];

    public function carAccessory()
    {
        return $this->hasOne(Accessories::class, 'id', 'accessories_id');
    }
}
