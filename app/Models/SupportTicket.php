<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */
    public const STATUSES = ['open', 'in_progress', 'resolved', 'closed'];
    public const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    /*
    |--------------------------------------------------------------------------
    | Fillable & Casts
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'title',
        'description',
        'user_id',
        'status',
        'priority'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name'  => 'Unknown User',
            'email' => 'unknown@example.com'
        ]);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SupportTicketResponse::class, 'ticket_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getStatusBadgeAttribute(): string
    {
        $statuses = [
            'open'        => 'primary',
            'in_progress' => 'info',
            'resolved'    => 'success',
            'closed'      => 'secondary'
        ];

        return $statuses[$this->status] ?? 'secondary';
    }

    public function getPriorityBadgeAttribute(): string
    {
        $priorities = [
            'low'    => 'secondary',
            'medium' => 'info',
            'high'   => 'warning',
            'urgent' => 'danger'
        ];

        return $priorities[$this->priority] ?? 'secondary';
    }
}
