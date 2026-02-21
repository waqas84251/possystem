<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\BelongsToUser;

class Customer extends Model
{
    use HasFactory, BelongsToUser;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * A customer can have many sales.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class, 'customer_id');
    }

    /**
     * Get the latest sale of this customer.
     */
    public function latestSale()
    {
        return $this->hasOne(Sale::class, 'customer_id')->latestOfMany();
    }

    /**
     * Check if customer has any sales.
     */
    public function hasSales(): bool
    {
        return $this->sales()->exists();
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
}
