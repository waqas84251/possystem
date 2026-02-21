<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's metrics - Global Scope handles user isolation
        $today = Carbon::today();
        $todaySales = Sale::whereDate('created_at', $today)->sum('total_amount');
        $todayOrders = Sale::whereDate('created_at', $today)->count();
        $todayCustomers = Customer::whereDate('created_at', $today)->count();

        // Weekly metrics
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $weeklySales = Sale::whereBetween('created_at', [$weekStart, $weekEnd])->sum('total_amount');

        // Monthly metrics
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        $monthlySales = Sale::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
        $monthlyOrders = Sale::whereBetween('created_at', [$monthStart, $monthEnd])->count();

        // Total customers
        $totalCustomers = Customer::count();

        // Recent sales (last 10)
        $recentSales = Sale::with('customer')
            ->latest()
            ->take(10)
            ->get();

        // Top selling products (last 30 days)
        $topProducts = Product::withCount(['saleItems as total_sold' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'))
                    ->whereHas('sale', function($q) {
                        $q->where('created_at', '>=', Carbon::now()->subDays(30));
                    });
            }])
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // Low stock alert
        $lowStockThreshold = config('inventory.low_stock_threshold', 10);
        $lowStockProducts = Product::with('category')
            ->where('stock', '<=', $lowStockThreshold)
            ->where('status', 'active')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        // Sales chart data (last 7 days)
        $salesChartData = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Payment method breakdown
        $paymentMethods = Sale::select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('payment_method')
            ->get();

        return view('dashboard', compact(
            'todaySales',
            'todayOrders',
            'todayCustomers',
            'weeklySales',
            'monthlySales',
            'monthlyOrders',
            'totalCustomers',
            'recentSales',
            'topProducts',
            'lowStockProducts',
            'lowStockThreshold',
            'salesChartData',
            'paymentMethods'
        ));
    }
}