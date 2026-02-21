<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesExport;
use App\Exports\ProductsExport;
use App\Exports\InventoryExport;
use App\Exports\CustomersExport;
use PDF;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $dateRange = $request->input('date_range', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                if ($startDate && $endDate) {
                    $startDate = Carbon::parse($startDate);
                    $endDate = Carbon::parse($endDate)->endOfDay();
                } else {
                    $startDate = Carbon::now()->subMonth();
                    $endDate = Carbon::now();
                }
                break;
            default:
                $startDate = Carbon::now()->subMonth();
                $endDate = Carbon::now();
                break;
        }

        // Global Scope handles user isolation
        $sales = Sale::with(['customer', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        $averageSale = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        $dailySales = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('SUM(total_amount) as total_amount')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.sales', compact(
            'sales',
            'totalSales',
            'totalRevenue',
            'averageSale',
            'dailySales',
            'dateRange',
            'startDate',
            'endDate'
        ));
    }

    public function productsReport()
    {
        // Global Scope handles user isolation
        $topProducts = Product::with('category')
            ->withCount(['saleItems as total_sold' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'));
            }])
            ->addSelect(['total_revenue' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(total_price), 0)'))
                      ->from('sale_items')
                      ->whereColumn('sale_items.product_id', 'products.id');
            }])
            ->orderBy('total_sold', 'desc')
            ->take(10)
            ->get();

        $productsByCategory = Category::withCount('products')
            ->addSelect(['total_stock' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(stock), 0)'))
                      ->from('products')
                      ->whereColumn('products.category_id', 'categories.id');
            }])
            ->addSelect(['total_value' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(price * stock), 0)'))
                      ->from('products')
                      ->whereColumn('products.category_id', 'categories.id');
            }])
            ->having('products_count', '>', 0)
            ->orderBy('products_count', 'desc')
            ->get();

        $lowStockProducts = Product::with('category')
            ->lowStock()
            ->orderBy('stock')
            ->get();

        return view('reports.products', compact(
            'topProducts',
            'productsByCategory',
            'lowStockProducts'
        ));
    }

    public function inventoryReport()
    {
        // Global Scope handles user isolation
        $inventorySummary = (object) [
            'total_products' => Product::count(),
            'total_stock' => Product::sum('stock'),
            'total_value' => Product::all()->sum(function($product) {
                return $product->price * $product->stock;
            }),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::lowStock()->where('stock', '>', 0)->count()
        ];

        $categories = Category::all();
        $inventoryByCategory = [];
        
        foreach ($categories as $category) {
            $totalStock = $category->products()->sum('stock');
            $totalValue = $category->products()->get()->sum(function($product) {
                return $product->price * $product->stock;
            });
            
            if ($totalStock > 0) {
                $inventoryByCategory[] = [
                    'name' => $category->name,
                    'total_stock' => $totalStock,
                    'total_value' => $totalValue,
                    'average_value' => $totalStock > 0 ? $totalValue / $totalStock : 0
                ];
            }
        }

        usort($inventoryByCategory, function($a, $b) {
            return $b['total_value'] <=> $a['total_value'];
        });

        return view('reports.inventory', compact(
            'inventorySummary',
            'inventoryByCategory'
        ));
    }

    public function customersReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Global Scope handles user isolation
        $topCustomers = Customer::withCount(['sales' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['sales' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total_amount')
            ->having('sales_count', '>', 0)
            ->orderByDesc('sales_sum_total_amount')
            ->limit(10)
            ->get();

        $customerActivity = (object)[
            'total_customers' => Customer::count(),
            'active_customers' => Customer::has('sales')->count(),
            'new_customers' => Customer::whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])->count(),
        ];
        
        return view('reports.customers', compact('topCustomers', 'customerActivity', 'startDate', 'endDate'));
    }

    // Export methods
    public function exportSalesReport(Request $request)
    {
        $format = $request->input('format', 'excel');
        $dateRange = $request->input('date_range', 'today');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $columns = $request->input('columns', ['sale_number', 'date', 'customer', 'items', 'amount', 'payment']);

        switch ($dateRange) {
            case 'today':
                $startDate = Carbon::today();
                $endDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $startDate = Carbon::yesterday();
                $endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                $startDate = Carbon::parse($startDate);
                $endDate = Carbon::parse($endDate)->endOfDay();
                break;
            default:
                $startDate = Carbon::now()->subMonth();
                $endDate = Carbon::now()->endOfDay();
                break;
        }

        $sales = Sale::with(['customer', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total_amount');
        $currencySymbol = \App\Models\Setting::getValueByKey('currency', '$');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.exports.sales-pdf', compact(
                'sales', 'startDate', 'endDate', 'totalSales', 'totalRevenue', 'columns', 'currencySymbol'
            ));
            return $pdf->download('sales-report-' . now()->format('Y-m-d') . '.pdf');
        }

        $extension = ($format === 'csv') ? 'csv' : 'xlsx';
        $fileName = 'sales-report-' . now()->format('Y-m-d') . '.' . $extension;
        return Excel::download(new SalesExport($sales, $columns), $fileName);
    }

    public function exportProductsReport(Request $request)
    {
        $format = $request->input('format', 'excel');
        $sections = $request->input('sections', ['top_products', 'low_stock', 'categories']);
        $columns = $request->input('columns', ['product_name', 'category', 'units_sold', 'revenue', 'stock']);

        $topProducts = Product::with('category')
            ->withCount(['saleItems as total_sold' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(quantity), 0)'));
            }])
            ->addSelect(['total_revenue' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(total_price), 0)'))
                      ->from('sale_items')
                      ->whereColumn('sale_items.product_id', 'products.id');
            }])
            ->orderBy('total_sold', 'desc')
            ->take(20)
            ->get();

        $productsByCategory = Category::withCount('products')
            ->addSelect(['total_stock' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(stock), 0)'))
                      ->from('products')
                      ->whereColumn('products.category_id', 'categories.id');
            }])
            ->addSelect(['total_value' => function($query) {
                $query->select(DB::raw('COALESCE(SUM(price * stock), 0)'))
                      ->from('products')
                      ->whereColumn('products.category_id', 'categories.id');
            }])
            ->having('products_count', '>', 0)
            ->orderBy('products_count', 'desc')
            ->get();

        $lowStockProducts = Product::with('category')
            ->lowStock()
            ->orderBy('stock')
            ->get();

        $currencySymbol = \App\Models\Setting::getValueByKey('currency', '$');
        $generatedAt = now()->format('M d, Y h:i A');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.exports.products-pdf', compact(
                'topProducts', 'productsByCategory', 'lowStockProducts', 'sections', 'columns', 'currencySymbol', 'generatedAt'
            ));
            return $pdf->download('products-report-' . now()->format('Y-m-d') . '.pdf');
        }

        $extension = ($format === 'csv') ? 'csv' : 'xlsx';
        $fileName = 'products-report-' . now()->format('Y-m-d') . '.' . $extension;
        return Excel::download(new ProductsExport($topProducts, $lowStockProducts, $productsByCategory, $sections, $columns), $fileName);
    }

    public function exportInventoryReport(Request $request)
    {
        $format = $request->input('format', 'excel');
        $sections = $request->input('sections', ['summary', 'categories']);
        $columns = $request->input('columns', ['category', 'total_stock', 'total_value', 'avg_value']);

        $inventorySummary = (object) [
            'total_products' => Product::count(),
            'total_stock' => Product::sum('stock'),
            'total_value' => Product::all()->sum(function($product) {
                return $product->price * $product->stock;
            }),
            'out_of_stock' => Product::where('stock', 0)->count(),
            'low_stock' => Product::lowStock()->where('stock', '>', 0)->count()
        ];

        $categories = Category::all();
        $inventoryByCategory = [];
        
        foreach ($categories as $category) {
            $totalStock = $category->products()->sum('stock');
            $totalValue = $category->products()->get()->sum(function($product) {
                return $product->price * $product->stock;
            });
            
            if ($totalStock > 0) {
                $inventoryByCategory[] = [
                    'name' => $category->name,
                    'total_stock' => $totalStock,
                    'total_value' => $totalValue,
                    'average_value' => $totalStock > 0 ? $totalValue / $totalStock : 0
                ];
            }
        }

        usort($inventoryByCategory, function($a, $b) {
            return $b['total_value'] <=> $a['total_value'];
        });

        $currencySymbol = \App\Models\Setting::getValueByKey('currency', '$');
        $generatedAt = now()->format('M d, Y h:i A');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.exports.inventory-pdf', compact(
                'inventorySummary', 'inventoryByCategory', 'sections', 'columns', 'currencySymbol', 'generatedAt'
            ));
            return $pdf->download('inventory-report-' . now()->format('Y-m-d') . '.pdf');
        }

        $extension = ($format === 'csv') ? 'csv' : 'xlsx';
        $fileName = 'inventory-report-' . now()->format('Y-m-d') . '.' . $extension;
        return Excel::download(new InventoryExport($inventorySummary, $inventoryByCategory, $sections, $columns), $fileName);
    }

    public function exportCustomersReport(Request $request)
    {
        $format = $request->input('format', 'excel');
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $sections = $request->input('sections', ['summary', 'top_customers']);
        $columns = $request->input('columns', ['customer_name', 'email', 'phone', 'total_orders', 'total_spent', 'avg_order_value']);

        $topCustomers = Customer::withCount(['sales' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['sales' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total_amount')
            ->having('sales_count', '>', 0)
            ->orderByDesc('sales_sum_total_amount')
            ->limit(10)
            ->get();

        // Map spending sum for the export which expects 'total_spent' attribute
        $topCustomers->each(function($customer) {
            $customer->total_spent = $customer->sales_sum_total_amount;
        });

        $customerActivity = (object)[
            'total_customers' => Customer::count(),
            'active_customers' => Customer::has('sales')->count(),
            'new_customers' => Customer::whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])->count(),
        ];

        $avgCustomerValue = $customerActivity->total_customers > 0 ? 
            $topCustomers->sum('total_spent') / $customerActivity->total_customers : 0;

        $currencySymbol = \App\Models\Setting::getValueByKey('currency', '$');
        $generatedAt = now()->format('M d, Y h:i A');

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.exports.customers-pdf', compact(
                'customerActivity', 'topCustomers', 'startDate', 'endDate', 'sections', 'columns', 'currencySymbol', 'generatedAt', 'avgCustomerValue'
            ));
            return $pdf->download('customers-report-' . now()->format('Y-m-d') . '.pdf');
        }

        $extension = ($format === 'csv') ? 'csv' : 'xlsx';
        $fileName = 'customers-report-' . now()->format('Y-m-d') . '.' . $extension;
        return Excel::download(new CustomersExport($customerActivity, $topCustomers, $sections, $columns), $fileName);
    }
}