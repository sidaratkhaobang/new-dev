<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditorTypeRelation extends Model
{
    use HasFactory;
    protected $table = 'creditors_types_relation';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
}
