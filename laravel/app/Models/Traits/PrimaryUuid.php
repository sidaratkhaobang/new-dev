<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait PrimaryUuid
{
    /**
     * @return void
     */
    public static function bootPrimaryUuid()
    {
        /**
         * Attach to the 'creating' Model Event to provide a UUID
         * for the `id` field (provided by $model->getKeyName())
         */
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::orderedUuid();
            }
        });
    }
}
