<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use App\Traits\BelongsToUser;

class Product extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser;

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost_price',
        'stock',
        'low_stock_threshold',
        'category_id',
        'barcode',
        'sku',
        'image',
        'status',
        'created_by',
        'user_id'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'stock' => 'integer',
        'low_stock_threshold' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $appends = ['profit_margin', 'stock_status'];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock <= low_stock_threshold');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('barcode', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%")
              ->orWhereHas('category', function($categoryQuery) use ($search) {
                  $categoryQuery->where('name', 'like', "%{$search}%");
              });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getProfitMarginAttribute()
    {
        if (!$this->cost_price) return null;
        
        $margin = $this->price - $this->cost_price;
        $marginPercent = ($margin / $this->price) * 100;
        
        return [
            'amount' => $margin,
            'percentage' => round($marginPercent, 2)
        ];
    }

    public function getStockStatusAttribute()
    {
        if ($this->stock == 0) {
            return 'out-of-stock';
        } elseif ($this->stock <= $this->low_stock_threshold) {
            return 'low-stock';
        }
        return 'in-stock';
    }

    public function getFormattedPriceAttribute()
    {
        $currency = Setting::getValueByKey('currency', '$');
        return $currency . number_format($this->price, 2);
    }

    public function getFormattedCostPriceAttribute()
    {
        $currency = Setting::getValueByKey('currency', '$');
        return $this->cost_price ? $currency . number_format($this->cost_price, 2) : 'N/A';
    }

    /*
    |--------------------------------------------------------------------------
    | Static Generators
    |--------------------------------------------------------------------------
    */
    public static function generateUniqueSku($name)
    {
        $baseSku = Str::upper(Str::substr(Str::slug($name), 0, 6));
        $sku = $baseSku . rand(100, 999);
        
        while (self::where('sku', $sku)->exists()) {
            $sku = $baseSku . rand(100, 999);
        }
        
        return $sku;
    }

    public static function generateUniqueBarcode()
    {
        $barcode = '88' . rand(1000000000, 9999999999);
        
        while (self::where('barcode', $barcode)->exists()) {
            $barcode = '88' . rand(1000000000, 9999999999);
        }
        
        return $barcode;
    }

    /*
    |--------------------------------------------------------------------------
    | Stock Management
    |--------------------------------------------------------------------------
    */
    public function updateStock($newQuantity, $type = 'adjustment', $remarks = 'Stock adjustment')
    {
        $oldStock = $this->stock;
        $difference = $newQuantity - $oldStock;

        $this->stock = $newQuantity;
        $this->save();

        if ($difference != 0) {
            $this->inventories()->create([
                'quantity'   => abs($difference),
                'type'       => $type,
                'remarks'    => $remarks,
                'created_by' => auth()->id(),
            ]);
        }
    }
    public function inventory()
{
    return $this->hasMany(Inventory::class);
}
}
