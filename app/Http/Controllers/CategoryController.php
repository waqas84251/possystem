<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        // Global Scope in Category model handles user isolation
        $query = Category::with(['parent'])
            ->withCount(['products', 'activeProducts', 'children']);
        
        // Search functionality
        if ($search = request('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Status filter
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        
        // Parent filter
        if (request('parent') === 'null') {
            $query->whereNull('parent_id');
        }
        
        $categories = $query->ordered()->paginate(15);
        
        // Stats
        $stats = [
            'active_count' => Category::active()->count(),
            'inactive_count' => Category::inactive()->count(),
            'total_products' => \App\Models\Product::count(),
        ];
        
        return view('categories.index', compact('categories', 'stats'));
    }

    public function create()
    {
        $parentOptions = Category::whereNull('parent_id')
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend('Select Parent Category', '');
        
        $defaults = [
            'status' => 'active',
            'sort_order' => (Category::max('sort_order') ?? 0) + 1
        ];
        
        return view('categories.create', compact('parentOptions', 'defaults'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        DB::beginTransaction();
        
        try {
            $categoryData = $request->only(['name', 'description', 'status', 'parent_id', 'sort_order']);
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
                $categoryData['image'] = $imagePath;
            }

            // BelongsToUser trait automatically handles user_id on creation
            $category = Category::create($categoryData);

            DB::commit();

            return redirect()->route('categories.index')
                ->with('success', 'Category created successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category creation failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error creating category: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Category $category)
    {
        // Category model-binding handles user isolation (fails with 404 if outside scope)
        $category->load([
            'products' => function($query) {
                $query->with('category')->latest()->take(10);
            },
            'children' => function($query) {
                $query->withCount('products')->ordered();
            },
            'parent'
        ]);
        
        $products = $category->products()->with('category')->paginate(20);
        $childCategories = $category->children()->withCount('products')->ordered()->get();
        
        return view('categories.show', compact('category', 'products', 'childCategories'));
    }

    public function edit(Category $category)
    {
        $parentOptions = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend('No Parent (Top Level)', '');
        
        $category->loadCount(['products', 'activeProducts', 'children']);
        
        return view('categories.edit', compact('category', 'parentOptions'));
    }

    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('categories')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                })->ignore($category->id)
            ],
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        if ($request->parent_id == $category->id) {
            $validator->errors()->add('parent_id', 'Category cannot be its own parent.');
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        DB::beginTransaction();
        
        try {
            $categoryData = $request->only(['name', 'description', 'status', 'parent_id', 'sort_order']);
            
            if ($request->hasFile('image')) {
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                $categoryData['image'] = $request->file('image')->store('categories', 'public');
            }
            
            if ($request->has('remove_image') && $request->remove_image) {
                if ($category->image) {
                    Storage::disk('public')->delete($category->image);
                }
                $categoryData['image'] = null;
            }

            $category->update($categoryData);

            DB::commit();

            return redirect()->route('categories.index')
                ->with('success', 'Category updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error updating category: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Category $category)
    {
        DB::beginTransaction();
        
        try {
            if ($category->products()->count() > 0) {
                return redirect()->route('categories.index')
                    ->with('error', 'Cannot delete category with products.');
            }
            
            if ($category->children()->count() > 0) {
                return redirect()->route('categories.index')
                    ->with('error', 'Cannot delete category with subcategories.');
            }

            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            DB::commit();

            return redirect()->route('categories.index')
                ->with('success', 'Category deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category deletion failed: ' . $e->getMessage());
            
            return redirect()->route('categories.index')
                ->with('error', 'Error deleting category.');
        }
    }
}