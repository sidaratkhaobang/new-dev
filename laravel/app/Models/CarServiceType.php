<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarServiceType extends Model
{
    use HasFactory;

    protected $table = 'cars_service_types';
    protected $keyType = 'string';
    public $timestamps = false;
}