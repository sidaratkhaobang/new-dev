<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Creator;
use App\Models\Traits\PrimaryUuid;
use Kyslik\ColumnSortable\Sortable;

class ExpenseType extends Model
{
    use HasFactory, PrimaryUuid, SoftDeletes, Creator, Sortable;

    protected $table = 'expense_types';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;
    protected $fillable = [
        'id',
    ];
}