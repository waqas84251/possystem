<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Fillable & Hidden
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed', // Laravel auto hash
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'created_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'created_by');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function ticketResponses()
    {
        return $this->hasMany(SupportTicketResponse::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers (Role-based Access)
    |--------------------------------------------------------------------------
    */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    // In User model
public function isSuperAdmin(): bool
{
    return $this->role === Role::SuperAdmin;
}
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}
