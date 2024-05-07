<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentRetails extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'rent_id',
        'product_id',
        'quantity',
        'per_day_price',
        'total',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $with = ['product'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function rent(): BelongsTo
    {
        return $this->belongsTo(Rent::class);
    }

}
