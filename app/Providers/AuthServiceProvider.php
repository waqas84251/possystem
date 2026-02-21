<?php

namespace App\Providers;

use App\Enums\Role;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider

{

   
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    Setting::class => SettingPolicy::class,
];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define Gates (High-level permissions)
        // Implicitly grant "Admin" role all permissions
        Gate::before(function ($user, $ability) {
            if ($user->role === Role::ADMIN) {
                return true;
            }
        });

        // Now define more specific gates
        Gate::define('manage-products', function ($user) {
            return in_array($user->role, [Role::ADMIN, Role::MANAGER]);
        });

        Gate::define('manage-categories', function ($user) {
            return in_array($user->role, [Role::ADMIN, Role::MANAGER]);
        });

        Gate::define('manage-users', function ($user) {
            return $user->role === Role::ADMIN;
        });

        Gate::define('view-reports', function ($user) {
            return in_array($user->role, [Role::ADMIN, Role::MANAGER]);
        });

        Gate::define('manage-settings', function ($user) {
            return $user->role === Role::ADMIN;
        });

        Gate::define('manage-inventory', function ($user) {
            return in_array($user->role, [Role::ADMIN, Role::MANAGER]);
        });

        // A cashier can only view the dashboard and process sales
        Gate::define('process-sales', function ($user) {
            // Everyone except maybe a hypothetical 'guest' can process sales
            return in_array($user->role, [Role::ADMIN, Role::MANAGER, Role::CASHIER]);
        });
    }
}