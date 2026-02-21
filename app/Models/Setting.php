<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'order',
        'user_id',
    ];

    protected $casts = [
        'order'      => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Boot the model. 
     * We don't use BelongsToUser trait here because settings need fallback logic 
     * (showing global defaults if no user-specific setting exists).
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-assign user_id on creation if not provided
        static::creating(function ($model) {
            if (empty($model->user_id) && Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */
    public function getValueAttribute($value)
    {
        switch ($this->type ?? 'text') {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'number':
                return is_numeric($value) ? $value + 0 : null;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    public function setValueAttribute($value)
    {
        if (is_array($value) || is_object($value)) {
            $this->attributes['value'] = json_encode($value);
            $this->attributes['type'] = 'json';
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Static Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get a setting value by key (user-specific with fallback to global).
     */
    public static function getValueByKey($key, $default = null, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        $setting = static::where('key', $key)
            ->where(function ($q) use ($userId) {
                if ($userId) {
                    $q->where('user_id', $userId)
                      ->orWhereNull('user_id');
                } else {
                    $q->whereNull('user_id');
                }
            })
            ->orderByRaw('CASE WHEN user_id IS NULL THEN 1 ELSE 0 END') // User-specific first (0 < 1)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value (user-specific).
     */
    public static function setValueByKey($key, $value, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        // Try to find if user already has this setting
        $setting = static::where('key', $key)->where('user_id', $userId)->first();

        if (!$setting) {
            // If user doesn't have it, find the global setting to copy metadata
            $globalSetting = static::where('key', $key)->whereNull('user_id')->first();
            
            $setting = new static();
            $setting->key = $key;
            $setting->user_id = $userId;
            
            if ($globalSetting) {
                $setting->label = $globalSetting->label;
                $setting->type = $globalSetting->type;
                $setting->group = $globalSetting->group;
                $setting->description = $globalSetting->description;
                $setting->order = $globalSetting->order;
            } else {
                // Fallback for completely new settings
                $setting->label = ucwords(str_replace('_', ' ', $key));
                $setting->type = 'text';
                $setting->group = 'general';
            }
        }

        $setting->value = $value;
        $setting->save();

        return $setting;
    }

    /**
     * Get all settings grouped by category (user-specific + global fallbacks).
     */
    public static function getGroupedSettings($userId = null)
    {
        $userId = $userId ?? Auth::id();

        // Get all applicable settings (user-specific and global)
        $settings = static::where(function ($q) use ($userId) {
                if ($userId) {
                    $q->where('user_id', $userId)
                      ->orWhereNull('user_id');
                } else {
                    $q->whereNull('user_id');
                }
            })
            ->orderBy('group')
            ->orderBy('order')
            ->get();

        // If user is logged in, we need to handle duplicates (one global, one user-specific)
        // We want to keep the user-specific one.
        if ($userId) {
            $settings = $settings->groupBy('key')->map(function ($items) {
                // If more than one (global and user), pick the one with user_id
                return $items->sortBy(function ($item) {
                    return $item->user_id === null ? 1 : 0;
                })->first();
            });
        }

        return $settings->groupBy('group');
    }
}
