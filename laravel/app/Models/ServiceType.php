<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use App\Models\Traits\UpdateStatus;
use Illuminate\Database\Eloquent\Builder;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ServiceType extends Model implements HasMedia
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, UpdateStatus, Sortable, InteractsWithMedia;

    protected $table = 'service_types';
    public $incrementing = false;
    protected $hidden = array('media');
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'can_rental_over_days',
        'transportation_type',
        'can_add_stopover',

    ];

    public $sortable = ['name', 'transportation_type', 'can_rental_over_days', 'can_add_stopover'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('service_types.name', 'like', '%' . $s . '%');
            }
        });
    }

    public function scopeStatusActive(Builder $query): Builder
    {
        return $query->where('service_types.status', STATUS_ACTIVE)->whereNull('deleted_at');
    }

    function getImageUrlAttribute()
    {
        $medias = $this->getMedia('service_images');
        $files = get_medias_detail($medias);
        return isset($files[0]['url']) ? $files[0]['url'] : asset('images/place_holder_service_type.png');
    }

    /* public function registerMediaConversions(Media $media = null): void
    {
        if (strcmp(env('APP_ENV'), 'local') == 0) {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff')->nonQueued();
        } else {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff');
        }
    } */
}
