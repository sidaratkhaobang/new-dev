<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amphure extends Model
{
    use HasFactory;
    protected $table = 'amphures';
    protected $fillable = [
        'id',
        'name_th',
        'name_en',
    ];
}
