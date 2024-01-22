<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RentalDriver extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, Sortable, InteractsWithMedia;
    protected $table = 'rental_drivers';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'rental_id',
        'customer_driver_id',
        'name',
        'tel',
        'email',
        'citizen_id'
    ];

    /* public function registerMediaConversions(Media $media = null): void
    {
        if (strcmp(env('APP_ENV'), 'local') == 0) {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff')->nonQueued();
        } else {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff');
        }
    } */
}
