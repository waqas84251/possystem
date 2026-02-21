<?php

namespace App\Enums;

class Role
{
    const ADMIN = 'admin';
    const MANAGER = 'manager';
    const CASHIER = 'cashier';

    /**
     * Get all available roles.
     *
     * @return array
     */
    public static function all()
    {
        return [
            self::ADMIN,
            self::MANAGER,
            self::CASHIER,
        ];
    }

    /**
     * Get role display name.
     *
     * @param string $role
     * @return string
     */
    public static function displayName($role)
    {
        return ucfirst($role);
    }

    /**
     * Get badge color for role.
     *
     * @param string $role
     * @return string
     */
    public static function badgeColor($role)
    {
        $colors = [
            self::ADMIN => 'bg-red-100 text-red-800',
            self::MANAGER => 'bg-blue-100 text-blue-800',
            self::CASHIER => 'bg-green-100 text-green-800',
        ];

        return $colors[$role] ?? 'bg-gray-100 text-gray-800';
    }
}