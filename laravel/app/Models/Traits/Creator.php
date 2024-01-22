<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait Creator
{
    /**
     * @return void
     */
    public static function bootCreator()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (empty($model->created_by)) {
                    $model->created_by = $user->id;
                }
                if (empty($model->updated_by)) {
                    $model->updated_by = $user->id;
                }
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (empty($model->updated_by)) {
                    $model->updated_by = $user->id;
                }
            }
        });

        static::deleting(function ($model) {
            if (Auth::check()) {
                $user = Auth::user();
                if (empty($model->deleted_by)) {              
                    $model->deleted_by = $user->id;
                    $model->save();
                    // Log::info([$model]);
                }
            }
        });
    }
}
