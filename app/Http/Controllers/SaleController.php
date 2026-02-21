<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    public function findByBarcode($barcode)
    {
        // Global Scope handles user isolation
        $product = Product::where('barcode', $barcode)->first();
        
        if ($product) {
            return response()->json($product);
        }
        
        return response()->json(['error' => 'Product not found'], 404);
    }

    public function fetchProductByBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);
        
        $product = Product::where('barcode', $request->barcode)->first();
        
        if ($product) {
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Product not found'
        ], 404);
    }
    
    public function scanBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $product = Product::where('barcode', $request->barcode)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'barcode' => $product->barcode
            ]
        ]);
    }

    public function scan()
    {
        $customers = Customer::all();
        return view('sales.scan', compact('customers'));
    }

    public function index()
    {
        $sales = Sale::with(['customer', 'user'])->latest()->paginate(10);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('sales.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card,transfer',
            'notes' => 'nullable|string',
            'discount_amount' => 'nullable|numeric|min:0',
            'items.*.name' => 'required_if:items.*.is_manual,1',
            'items.*.price' => 'required_if:items.*.is_manual,1|numeric|min:0',
        ]);

        $subtotal = 0;
        $items = [];

        foreach ($request->items as $itemData) {
            $quantity = $itemData['quantity'];
            $isManual = isset($itemData['is_manual']) && $itemData['is_manual'] == '1';
            
            if ($isManual) {
                $price = $itemData['price'];
                $totalPrice = $price * $quantity;
                $subtotal += $totalPrice;

                $items[] = [
                    'is_manual' => true,
                    'name' => $itemData['name'],
                    'price' => $price,
                    'quantity' => $quantity,
                    'total_price' => $totalPrice
                ];
            } else {
                $product = Product::find($itemData['product_id']);
                
                if (!$product) {
                    return back()->withErrors(['product' => "Product not found."]);
                }

                if ($product->stock < $quantity) {
                    return back()->withErrors(['stock' => "Insufficient stock for {$product->name}."]);
                }

                $totalPrice = $product->price * $quantity;
                $subtotal += $totalPrice;

                $items[] = [
                    'is_manual' => false,
                    'product' => $product,
                    'name' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total_price' => $totalPrice
                ];
            }
        }

        $discount = $request->discount_amount ?? 0;
        $totalAmount = max(0, $subtotal - $discount);

        // BelongsToUser trait automatically handles user_id
        $sale = Sale::create([
            'customer_id' => $request->customer_id,
            'subtotal' => $subtotal,
            'tax_amount' => 0,
            'discount_amount' => $discount,
            'total_amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'completed',
            'notes' => $request->notes,
        ]);

        foreach ($items as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['is_manual'] ? null : $item['product']->id,
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['is_manual'] ? $item['price'] : $item['unit_price'],
                'total_price' => $item['total_price'],
                'is_manual' => $item['is_manual']
            ]);

            if (!$item['is_manual']) {
                $item['product']->decrement('stock', $item['quantity']);
            }
        }

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Sale completed successfully!');
    }

    public function show(Sale $sale)
    {
        // Global Scope handles user isolation
        $sale->load(['items.product', 'customer', 'user']);
        return view('sales.show', compact('sale'));
    }

    public function destroy(Sale $sale)
    {
        foreach ($sale->items as $item) {
            if ($item->product_id) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $sale->delete();

        return redirect()->route('sales.index')
            ->with('success', 'Sale deleted successfully!');
    }

    public function findProductByBarcode(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);

        $product = Product::where('barcode', $request->barcode)->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        if ($product->stock < 1) {
            return response()->json(['success' => false, 'message' => 'Product is out of stock.'], 400);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'barcode' => $product->barcode
            ]
        ]);
    }

    public function receipt(Sale $sale)
    {
        $sale->load(['items', 'customer']);
        return view('sales.receipt', compact('sale'));
    }
}
