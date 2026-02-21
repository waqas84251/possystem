<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToUser()
    {
        // Add global scope to filter by user_id
        static::addGlobalScope('user_id', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('user_id', Auth::id());
            } else {
                // If not logged in, we assume isolation applies to public data (null user_id)
                // or no data at all depending on the app's requirement.
                // For this POS, the user wants strict user isolation.
                $builder->whereNull('user_id');
            }
        });

        // Add automatic user_id assignment when creating
        static::creating(function ($model) {
            if (empty($model->user_id) && Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    /**
     * Define the user relationship.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
