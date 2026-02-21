<?php

namespace App\Helpers;

use App\Models\Setting;

class SettingsHelper
{
    public static function get($key, $default = null)
    {
        return Setting::getValue($key, $default);
    }

    public static function businessName()
    {
        return static::get('business_name', 'POS System');
    }

    public static function currency()
    {
        return static::get('currency', '$');
    }

    public static function taxRate()
    {
        return static::get('tax_rate', 0);
    }

    public static function receiptHeader()
    {
        return static::get('receipt_header', 'Thank you for your purchase!');
    }

    public static function receiptFooter()
    {
        return static::get('receipt_footer', 'Please come again!');
    }
}