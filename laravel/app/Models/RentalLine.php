<?php

namespace App\Models;

use App\Models\Traits\PrimaryUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kyslik\ColumnSortable\Sortable;
use App\Models\Traits\Creator;
use App\Models\Product;
use App\Models\ProductAdditional;

class RentalLine extends Model
{
    use HasFactory, PrimaryUuid, Sortable;

    protected $table = 'rental_lines';
    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'name',
        'description',
        'amount',
        'subtotal',
        'unit_price',
        'discount',
        'vat',
        'total',
    ];

    /**
     * Get the parent item.
     */
    public function item()
    {
        return $this->morphTo();
    }

    public function car()
    {
        return $this->hasOne(Car::class, 'id', 'car_id');
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function productAdditional()
    {
        return $this->hasOne(ProductAdditional::class, 'id', 'item_id');
    }

    /**
     * Get display name.
     *
     * @return string
     */
    public function getSummaryDisplayNameAttribute()
    {
        $display_name = '';
        if (strcmp($this->item_type, Product::class) == 0) {
            $display_name = $this->item ? $this->item->name : '';
        } else {
            if (strcmp($this->item_type, ProductAdditional::class) == 0) {
                $display_name = $this->item ? $this->item->name : '';
            }
        }
        return $display_name;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getSummaryDescriptionAttribute()
    {
        $summary_description = '';
        if (strcmp($this->item_type, Product::class) == 0) {
            $summary_description = $this->car ? $this->car->license_plate : '';
        }
        return $summary_description;
    }
}
