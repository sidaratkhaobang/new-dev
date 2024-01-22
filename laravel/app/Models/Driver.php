<?php

namespace App\Models;

use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Kyslik\ColumnSortable\Sortable;

class Driver extends Model implements HasMedia
{
    use HasFactory,  PrimaryUuid, SoftDeletes, Creator, InteractsWithMedia, Sortable;

    protected $table = 'drivers';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'code',
        'emp_status',
        'position_id',
        'tel',
        'phone',
        'citizen_id',
        'start_working_time',
        'end_working_time',
        'working_day_mon',
        'working_day_tue',
        'working_day_wed',
        'working_day_thu',
        'working_day_fri',
        'working_day_sat',
        'working_day_sun',
    ];

    public $sortable = ['name', 'code', 'emp_status', 'created_at'];
    public $sortableAs = ['province', 'driving_skill_name'];

    public function scopeSearch($query, $s)
    {
        return $query->where(function ($q) use ($s) {
            if (!empty($s)) {
                $q->where('drivers.name', 'like', '%' . $s . '%');
                $q->orWhere('drivers.code', 'like', '%' . $s . '%');
                $q->orWhere('positions.name', 'like', '%' . $s . '%');
                $q->orWhere('provinces.name_th', 'like', '%' . $s . '%');
            }
        });
    }

    /* public function registerMediaConversions(Media $media = null): void
    {
        if (strcmp(env('APP_ENV'), 'local') == 0) {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff')->nonQueued();
        } else {
            $this->addMediaConversion('thumb')->fit(Manipulations::FIT_FILL, 100, 100)->background('ffffff');
        }
    } */

    public function province()
    {
        return $this->hasOne(Province::class, 'id', 'province_id');
    }

    public function position()
    {
        return $this->hasOne(Position::class, 'id', 'position_id');
    }

    function getProfileUrlAttribute()
    {
        $medias = $this->getMedia('profile_image');
        $files = get_medias_detail($medias);
        return isset($files[0]['url']) ? $files[0]['url'] : asset('images/user/user.png');
    }
}
