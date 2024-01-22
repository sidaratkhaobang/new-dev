<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

trait UpdateStatus
{
    /**
     * @return void
     */
    public static function bootUpdateStatus()
    {
        static::deleting(function ($model) {
            // if (Auth::check()) {
                $model->status = STATUS_INACTIVE;
                $model->save();
            // }
        });
    }
}
