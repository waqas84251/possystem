<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use App\Traits\BelongsToUser;

class Category extends Model
{
    use HasFactory, SoftDeletes, BelongsToUser;

    protected $fillable = [
        'name',
        'user_id',
        'description',
        'status',
        'image',
        'parent_id',
        'sort_order'
    ];

    protected $casts = [
        'status' => 'string',
        'sort_order' => 'integer',
        'parent_id' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function activeProducts(): HasMany
    {
        return $this->hasMany(Product::class)->where('status', 'active');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return asset('assets/img/no-image.png');
        }

        $disk = config('filesystems.default', 'public');

        return Storage::disk($disk)->exists($this->image)
            ? Storage::disk($disk)->url($this->image)
            : asset('assets/img/no-image.png');
    }

    public function getProductsCountAttribute(): int
    {
        return $this->products()->count();
    }

    public function getActiveProductsCountAttribute(): int
    {
        return $this->activeProducts()->count();
    }

    public function getChildrenCountAttribute(): int
    {
        return $this->children()->count();
    }

    public function getSlugAttribute(): string
    {
        return Str::slug($this->name);
    }

    public function getIsRootAttribute(): bool
    {
        return is_null($this->parent_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasParent(): bool
    {
        return !is_null($this->parent_id);
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Model Events
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        // Set defaults before creating
        static::creating(function ($category) {
            if (empty($category->sort_order)) {
                $maxOrder = static::max('sort_order') ?? 0;
                $category->sort_order = $maxOrder + 1;
            }

            if (empty($category->status)) {
                $category->status = 'active';
            }
        });

        // Prevent deleting if category has children or products
        static::deleting(function ($category) {
            if (!$category->isForceDeleting()) {
                if ($category->products()->exists()) {
                    throw new \Exception('Cannot delete category with associated products. Please reassign or delete the products first.');
                }

                if ($category->children()->exists()) {
                    throw new \Exception('Cannot delete category with child categories. Please remove or reassign them first.');
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Dropdown Options
    |--------------------------------------------------------------------------
    */

    public static function getParentOptions($excludeId = null): array
    {
        $query = static::whereNull('parent_id')->orderBy('sort_order')->orderBy('name');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->get()->pluck('name', 'id')->toArray();
    }
    public function scopeAccessible($query)
{
    if (auth()->check()) {
        return $query->where('user_id', auth()->id())->orWhereNull('user_id');
    }
    return $query->whereNull('user_id');
}
}
