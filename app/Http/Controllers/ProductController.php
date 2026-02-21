<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a simple list of all product names
     */
    public function index()
    {
        $search = request('search');
        $status = request('status');
        $category = request('category');
        $stockStatus = request('stock_status');
        
        // Build query - Global Scope handles user isolation
        $query = Product::with('category');
        
        $query->when($search, function($q) use ($search) {
            $q->search($search);
        })
        ->when($status, function($q) use ($status) {
            $q->where('status', $status);
        })
        ->when($category, function($q) use ($category) {
            $q->where('category_id', $category);
        })
        ->when($stockStatus, function($q) use ($stockStatus) {
            if ($stockStatus === 'low') {
                $q->lowStock();
            } elseif ($stockStatus === 'out') {
                $q->where('stock', 0);
            }
        });
        
        $products = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Scope categories for the filter dropdown
        $categories = Category::active()->orderBy('name')->get();
        
        $lowStockCount = Product::lowStock()->count();
        
        return view('products.index', compact('products', 'search', 'status', 'category', 'stockStatus', 'categories', 'lowStockCount'));
    }

    public function create()
    {
        $categories = Category::active()->withCount('products')->orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'cost_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
            ],
            'barcode' => [
                'nullable', 
                'string',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'sku' => [
                'nullable', 
                'string',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status' => 'required|in:active,inactive'
        ], [
            'cost_price.lt' => 'Cost price must be less than selling price.',
            'price.min' => 'Price must be at least $0.01.',
            'category_id.exists' => 'The selected category is invalid or does not belong to you.'
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        
        try {
            $product = new Product();
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->cost_price = $request->cost_price;
            $product->stock = $request->stock;
            $product->low_stock_threshold = $request->low_stock_threshold ?? 10;
            $product->category_id = $request->category_id;
            $product->status = $request->status;
            $product->created_by = auth()->id();
            
            // Generate SKU if not provided
            $product->sku = $request->sku ?: Product::generateUniqueSku($request->name);
            
            // Generate barcode if not provided
            $product->barcode = $request->barcode ?: Product::generateUniqueBarcode();

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
                $product->image = $imagePath;
            }

            $product->save();

            // Create initial inventory record if stock is added
            if ($request->stock > 0) {
                $product->inventory()->create([
                    'quantity' => $request->stock,
                    'type' => 'initial',
                    'remarks' => 'Initial stock',
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully.',
                    'redirect' => route('products.index')
                ]);
            }

            if ($request->has('add_another')) {
                return redirect()->route('products.create')
                    ->with('success', 'Product created successfully. Add another one!');
            }

            return redirect()->route('products.index')
                ->with('success', 'Product created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product creation failed: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating product: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error creating product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Product $product)
    {
        
        $product->load(['category', 'inventory' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'inventory.user']);
        
        // Generate barcode SVG for display
        $barcodeSvg = '';
        if ($product->barcode) {
            try {
                $generator = new BarcodeGeneratorSVG();
                $barcodeSvg = $generator->getBarcode($product->barcode, $generator::TYPE_EAN_13, 2, 60);
            } catch (\Exception $e) {
                Log::error('Barcode generation failed: ' . $e->getMessage());
                $barcodeSvg = '';
            }
        }
        
        return view('products.show', compact('product', 'barcodeSvg'));
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        
        // Generate barcode SVG for display
        $barcodeSvg = '';
        if ($product->barcode) {
            try {
                $generator = new BarcodeGeneratorSVG();
                $barcodeSvg = $generator->getBarcode($product->barcode, $generator::TYPE_EAN_13, 2, 60);
            } catch (\Exception $e) {
                Log::error('Barcode generation failed: ' . $e->getMessage());
                $barcodeSvg = '';
            }
        }
        
        return view('products.edit', compact('product', 'categories', 'barcodeSvg'));
    }

    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0.01',
            'cost_price' => 'nullable|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                }),
            ],
            'barcode' => [
                'nullable', 
                'string',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })->ignore($product->id),
            ],
            'sku' => [
                'nullable', 
                'string',
                Rule::unique('products')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })->ignore($product->id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'status' => 'required|in:active,inactive',
            'remove_image' => 'nullable|boolean',
            'old_stock' => 'required|integer|min:0'
        ], [
            'cost_price.lt' => 'Cost price must be less than selling price.',
            'price.min' => 'Price must be at least $0.01.',
            'category_id.exists' => 'The selected category is invalid or does not belong to you.'
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        
        try {
            $oldStock = $product->stock;
            $newStock = $request->stock;
            
            // Verify the old stock matches what we expect
            if ($request->old_stock != $oldStock) {
                throw new \Exception('Stock data mismatch. Please refresh the page and try again.');
            }
            
            $product->name = $request->name;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->cost_price = $request->cost_price;
            $product->stock = $newStock;
            $product->low_stock_threshold = $request->low_stock_threshold ?? 10;
            $product->category_id = $request->category_id;
            $product->status = $request->status;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;

            // Handle image removal
            if ($request->remove_image && $product->image) {
                Storage::disk('public')->delete($product->image);
                $product->image = null;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image) {
                    Storage::disk('public')->delete($product->image);
                }
                
                $imagePath = $request->file('image')->store('products', 'public');
                $product->image = $imagePath;
            }

            $product->save();

            // Create inventory adjustment record if stock changed
            if ($oldStock != $newStock) {
                $type = $newStock > $oldStock ? 'restock' : 'adjustment';
                $quantity = abs($newStock - $oldStock);
                $remarks = $newStock > $oldStock ? 'Stock adjustment during product update' : 'Stock adjustment during product update';
                
                $product->inventory()->create([
                    'quantity' => $quantity,
                    'type' => $type,
                    'remarks' => $remarks,
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully.',
                    'redirect' => route('products.show', $product->id)
                ]);
            }
            
            return redirect()->route('products.show', $product->id)
                ->with('success', 'Product updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product update failed: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating product: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error updating product: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            // Load product with relations (Global Scope filters by user)
            $product = Product::with(['saleItems', 'inventory'])->findOrFail($id);

            // Check sales records
            if ($product->saleItems->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "Product '{$product->name}' cannot be deleted because it has sales records."
                ], 422);
            }

            // Delete product image
            if ($product->image) {
                if (Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
            }

            // Delete inventory records
            if ($product->inventory->isNotEmpty()) {
                $product->inventory()->delete();
            }

            // Delete product itself
            $product->delete();

            DB::commit();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Product '{$product->name}' deleted successfully."
                ]);
            }

            return redirect()->route('products.index')
                ->with('success', "Product '{$product->name}' deleted successfully.");
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found.'
                ], 404);
            }
            return redirect()->route('products.index')->with('error', 'Product not found.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product deletion failed: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting the product. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred while deleting the product.');
        }
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        
        // Build query with user scoping
        $query = Product::with('category');
        $this->applyProductsScope($query);
        
        $products = $query->search($search)
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['search' => $search]);
    
        return view('products.index', compact('products', 'search'));
    }

    public function lowStock()
    {
        // Get low stock threshold from settings or use default
        $lowStockThreshold = config('inventory.low_stock_threshold', 10);
        
        // Build query with user scoping
        $query = Product::with('category')
            ->where('stock', '<=', $lowStockThreshold)
            ->where('status', 'active');
        
        $this->applyProductsScope($query);
        
        $products = $query->orderBy('stock', 'asc')->paginate(15);
        
        // Get low stock count for the badge (with user scoping)
        $lowStockCountQuery = Product::where('stock', '<=', $lowStockThreshold)
            ->where('status', 'active');
        
        $this->applyProductsScope($lowStockCountQuery);
        $lowStockCount = $lowStockCountQuery->count();
        
        return view('products.low-stock', compact('products', 'lowStockCount', 'lowStockThreshold'));
    }
    
    // Barcode generation
    public function generateBarcode(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'barcode' => 'nullable|string|size:13|regex:/^[0-9]+$/'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid barcode format. Must be exactly 13 digits.',
                    'messages' => $validator->errors()
                ], 422);
            }

            // Use provided barcode or generate a new one
            $barcode = $request->input('barcode', Product::generateUniqueBarcode());

            // Generate barcode SVG
            $generator = new BarcodeGeneratorSVG();
            $barcodeSVG = $generator->getBarcode($barcode, $generator::TYPE_EAN_13, 2, 60);

            return response()->json([
                'success' => true,
                'barcode' => $barcode,
                'image' => 'data:image/svg+xml;base64,' . base64_encode($barcodeSVG)
            ]);

        } catch (\Exception $e) {
            Log::error('Barcode generation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => 'Failed to generate barcode. Please try again.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Download barcode as SVG image
    public function downloadBarcode($barcode)
    {
        try {
            // Verify the barcode exists with user scoping
            $query = Product::where('barcode', $barcode);
            $this->applyProductsScope($query);
            $product = $query->first();
            
            if (!$product) {
                return response()->json([
                    'error' => 'Product not found'
                ], 404);
            }
            
            $generator = new BarcodeGeneratorSVG();
            $barcodeSVG = $generator->getBarcode($barcode, $generator::TYPE_EAN_13, 2, 60);
            
            return response($barcodeSVG)
                ->header('Content-Type', 'image/svg+xml')
                ->header('Content-Disposition', 'attachment; filename="barcode-' . $barcode . '.svg"');
                
        } catch (\Exception $e) {
            Log::error('Barcode download failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate barcode'
            ], 500);
        }
    }

    // Print barcode with product information
    public function printBarcode($id)
    {
        try {
            $product = Product::findOrFail($id);
            
            // NEW: Authorization check
            $this->authorizeProductAccess($product);
            
            $barcode = $product->barcode;
            
            // Generate barcode SVG
            $generator = new BarcodeGeneratorSVG();
            $barcodeSVG = $generator->getBarcode($barcode, $generator::TYPE_EAN_13, 2, 60);
            
            return view('products.barcode-print', [
                'barcode' => $barcode,
                'barcodeSvg' => $barcodeSVG,
                'product' => $product
            ]);
            
        } catch (\Exception $e) {
            Log::error('Barcode print failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to generate barcode: ' . $e->getMessage());
        }
    }

    // Bulk delete products
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:products,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid product selection.'
            ], 422);
        }

        DB::beginTransaction();
        
        try {
            // Global Scope handles user isolation
            $products = Product::with(['saleItems', 'inventory'])
                ->whereIn('id', $request->ids)
                ->get();
                
            $deletedCount = 0;
            $productsWithSales = 0;
            $imagePathsToDelete = [];
            
            foreach ($products as $product) {
                // Check if product has sales records
                if ($product->saleItems->isNotEmpty()) {
                    $productsWithSales++;
                    continue;
                }

                // Collect image paths for batch deletion
                if ($product->image) {
                    $imagePathsToDelete[] = $product->image;
                }
                
                // Delete inventory records if they exist
                if ($product->inventory->isNotEmpty()) {
                    $product->inventory()->delete();
                }
                
                $product->delete();
                $deletedCount++;
            }

            // Delete all images in one operation (more efficient)
            if (!empty($imagePathsToDelete)) {
                Storage::disk('public')->delete($imagePathsToDelete);
            }

            DB::commit();

            if ($deletedCount > 0) {
                $message = $deletedCount . ' product' . ($deletedCount === 1 ? '' : 's') . ' deleted successfully.';
                
                if ($productsWithSales > 0) {
                    $message .= ' ' . $productsWithSales . ' product' . 
                               ($productsWithSales === 1 ? '' : 's') . 
                               ' skipped due to sales records.';
                }
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => $message
                    ]);
                }

                return redirect()->route('products.index')->with('success', $message);
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No products could be deleted. All selected products have sales records.'
                    ], 422);
                }
                return redirect()->route('products.index')->with('error', 'No products could be deleted. All selected products have sales records.');
            }
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk product deletion failed: ' . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while deleting products. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred while deleting products.');
        }
    }

    // Quick stock update
    public function quickRestock(Request $request, Product $product)
    {
        // NEW: Authorization check
        $this->authorizeProductAccess($product);

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input: ' . $validator->errors()->first()
            ], 422);
        }

        try {
            $quantity = $request->quantity;
            
            // Update product stock
            $product->stock += $quantity;
            $product->save();

            // Create inventory record
            $product->inventory()->create([
                'quantity' => $quantity,
                'type' => 'restock',
                'remarks' => $request->remarks ?? 'Quick restock',
                'created_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully restocked {$quantity} units of {$product->name}",
                'new_stock' => $product->stock
            ]);
        } catch (\Exception $e) {
            Log::error('Quick restock failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error restocking product: ' . $e->getMessage()
            ], 500);
        }
    }

    public function quickUpdateStock(Request $request, Product $product)
    {
        // NEW: Authorization check
        $this->authorizeProductAccess($product);

        $validator = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0',
            'type' => 'required|in:restock,adjustment',
            'remarks' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $product->updateStock(
                $request->stock, 
                $request->type, 
                $request->remarks ?? 'Quick stock update'
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock updated successfully.',
                'stock' => $product->stock
            ]);
        } catch (\Exception $e) {
            Log::error('Quick stock update failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating stock: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get product data for API
    public function getProductData(Product $product)
    {
        // NEW: Authorization check
        $this->authorizeProductAccess($product);
        
        return response()->json([
            'success' => true,
            'product' => $product->load('category')
        ]);
    }

    // Product status toggle
    public function toggleStatus(Product $product)
    {
        try {
            $product->status = $product->status === 'active' ? 'inactive' : 'active';
            $product->save();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product status updated successfully.',
                    'status' => $product->status
                ]);
            }

            return redirect()->back()
                ->with('success', "Product '{$product->name}' status updated to {$product->status}.");
                
        } catch (\Exception $e) {
            Log::error('Product status toggle failed: ' . $e->getMessage());
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating product status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Error updating product status.');
        }
    }

    public function searchSuggestions(Request $request)
    {
        $query = $request->input('query');
        $type = $request->input('type', 'all');
        
        $suggestions = [];
        
        switch ($type) {
            case 'category':
                // Scope categories for suggestions
                $categoryQuery = Category::where('name', 'like', "%$query%");
                $this->applyCategoriesScope($categoryQuery);
                
                $categories = $categoryQuery->limit(10)->get();
                
                foreach ($categories as $category) {
                    $suggestions[] = [
                        'value' => $category->id,
                        'label' => $category->name
                    ];
                }
                break;
                
            case 'status':
                $statuses = ['active', 'inactive'];
                foreach ($statuses as $status) {
                    if (stripos($status, $query) !== false) {
                        $suggestions[] = [
                            'value' => $status,
                            'label' => ucfirst($status)
                        ];
                    }
                }
                break;
                
            case 'stock':
                if (stripos('low', $query) !== false) {
                    $suggestions[] = [
                        'value' => 'low',
                        'label' => 'Low Stock'
                    ];
                }
                if (stripos('out', $query) !== false || stripos('zero', $query) !== false) {
                    $suggestions[] = [
                        'value' => 'out',
                        'label' => 'Out of Stock'
                    ];
                }
                break;
                
            default:
                // Global Scope in Product model handles user isolation
                $products = Product::where('name', 'like', "%$query%")
                    ->orWhere('sku', 'like', "%$query%")
                    ->orWhere('barcode', 'like', "%$query%")
                    ->limit(10)
                    ->get();
                
                foreach ($products as $product) {
                    $suggestions[] = [
                        'value' => $product->name,
                        'label' => $product->name . ' (SKU: ' . $product->sku . ')'
                    ];
                }
                break;
        }
        
        return response()->json($suggestions);
    }

    /**
     * Scope the query to products owned by the authenticated user.
     */
    protected function applyProductsScope($query)
    {
        return $query->where('user_id', auth()->id());
    }

    /**
     * Scope the query to categories owned by the authenticated user.
     */
    protected function applyCategoriesScope($query)
    {
        return $query->where('user_id', auth()->id());
    }

    /**
     * Authorize that the product belongs to the authenticated user.
     */
    protected function authorizeProductAccess(Product $product)
    {
        if ($product->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }
    }
}