<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToUser;

class Sale extends Model
{
    use HasFactory, BelongsToUser;

    protected $fillable = [
        'sale_number',
         'name',
        'customer_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount'    => 'decimal:2',
        'created_at'      => 'datetime:Y-m-d H:i:s',
        'updated_at'      => 'datetime:Y-m-d H:i:s',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            if (empty($sale->sale_number)) {
                $sale->sale_number = 'SALE-' . date('Ymd') . '-' . strtoupper(uniqid());
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getFormattedTotalAttribute()
    {
        $currency = Setting::getValueByKey('currency', '$');
        return $currency . number_format($this->total_amount, 2);
    }

    public function getFormattedSubtotalAttribute()
    {
        $currency = Setting::getValueByKey('currency', '$');
        return $currency . number_format($this->subtotal, 2);
    }

    public function getFormattedTaxAttribute()
    {
        $currency = Setting::getValueByKey('currency', '$');
        return $currency . number_format($this->tax_amount, 2);
    }

    public function getFormattedDiscountAttribute()
    {
        $currency = Setting::getValueByKey('currency', '$');
        return $currency . number_format($this->discount_amount, 2);
    }
}
