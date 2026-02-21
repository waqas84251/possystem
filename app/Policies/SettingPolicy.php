<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SettingPolicy
{
    public function view(User $user)
    {
        return $user->hasRole('admin') 
            ? Response::allow()
            : Response::deny('You must be an administrator to view settings.');
    }

    public function update(User $user)
    {
        return $user->hasRole('admin')
            ? Response::allow()
            : Response::deny('You must be an administrator to update settings.');
    }
}