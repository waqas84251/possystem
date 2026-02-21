<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            // Global Scopes in Models handle user isolation automatically!
            // When Auth::check() is true, trait filters by user_id = Auth::id()
            // When Auth::check() is false, trait filters by user_id IS NULL
            
            $view->with('productCount', Product::count());
            $view->with('lowStockCount', Product::lowStock()->count());
            $view->with('recentCustomers', Customer::where('created_at', '>', now()->subDay())->count());
            $view->with('businessName', Setting::getValueByKey('business_name', 'POS System'));
            $view->with('currencySymbol', Setting::getValueByKey('currency', '$'));
            $view->with('taxRate', Setting::getValueByKey('tax_rate', 0));
            $view->with('lowStockThreshold', Setting::getValueByKey('low_stock_threshold', 10));
            $view->with('receiptHeader', Setting::getValueByKey('receipt_header', ''));
            $view->with('receiptFooter', Setting::getValueByKey('receipt_footer', 'Thank you for your business!'));
            
            // Today's Stats
            $todaySalesQuery = Sale::whereDate('created_at', today());
            $view->with('todayTotalSales', $todaySalesQuery->sum('total_amount'));
            $view->with('todayOrderCount', $todaySalesQuery->count());
            $view->with('totalCustomerCount', Customer::count());
        });

        if (!function_exists('remove_filter_url')) {
            function remove_filter_url($filterToRemove)
            {
                $currentQuery = request()->query();
                unset($currentQuery[$filterToRemove]);
                
                return url()->current() . (!empty($currentQuery) ? '?' . http_build_query($currentQuery) : '');
            }
        }
    }
}
