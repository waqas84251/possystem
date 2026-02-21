<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index()
    {
        // Global Scope handles user isolation
        $products = Product::with('category')->orderBy('name')->get();
        $categories = Category::withCount('products')->get();
        
        $totalValue = Product::sum(DB::raw('price * stock'));
        $totalItems = Product::sum('stock');
        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::where('stock', '=', 0)->count();

        return view('inventory.index', compact(
            'products', 
            'categories',
            'totalValue',
            'totalItems',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    public function lowStock()
    {
        $lowStockProducts = Product::with('category')
            ->lowStock()
            ->orderBy('stock')
            ->get();

        return view('inventory.low-stock', compact('lowStockProducts'));
    }

    public function showAdjust(Product $product)
    {
        // Model binding handles access via Global Scope
        return view('inventory.adjust', compact('product'));
    }

    public function adjust(Request $request, Product $product)
    {
        $request->validate([
            'adjustment_type' => 'required|in:add,remove,set',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string'
        ]);

        $oldStock = $product->stock;
        
        switch ($request->adjustment_type) {
            case 'add':
                $product->stock += $request->quantity;
                break;
            case 'remove':
                if ($request->quantity > $product->stock) {
                    return back()->withErrors(['quantity' => 'Cannot remove more than current stock.']);
                }
                $product->stock -= $request->quantity;
                break;
            case 'set':
                $product->stock = $request->quantity;
                break;
        }

        $product->save();

        return redirect()->route('inventory.index')
            ->with('success', "Stock updated successfully! {$product->name}: {$oldStock} → {$product->stock}");
    }

    public function stockHistory(Product $product)
    {
        return view('inventory.stock-history', compact('product'));
    }
}