<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rent extends Model
{
    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'user_id',
        'customer_id',
        'rent_date',
        'return_date',
        'total_products',
        'sub_total',
        'vat',
        'total',
        'invoice_no',
        'payment_type',
        'rent_type',
        'uuid',
    ];

    protected $casts = [
        'rent_date' => 'date',
        'return_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(RentDetails::class);
    }

    public function scopeSearch($query, $value): void
    {
        $query->where('invoice_no', 'like', "%{$value}%")
              ->orWhere('payment_type', 'like', "%{$value}%")
              ->orWhere('rent_type', 'like', "%{$value}%");
    }

     /**
     * Get the user that owns the Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
