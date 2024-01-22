<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DriverDrivingSkills extends Model implements HasMedia
{
    use HasFactory,  PrimaryUuid, InteractsWithMedia;

    protected $table = 'drivers_driving_skills';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
    ];

    public function driving_skill()
    {
        return $this->hasOne(DrivingSkill::class, 'id', 'driving_skill_id');
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
