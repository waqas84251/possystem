@extends('layouts.app')

@section('title', $category->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categories</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
                    </ol>
                </nav>
                <div class="btn-group">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Category Details -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            @if($category->image)
                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" 
                                     class="img-fluid rounded mb-3" style="max-height: 200px;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" 
                                     style="height: 200px;">
                                    <i class="fas fa-folder fa-3x text-secondary"></i>
                                </div>
                            @endif
                            
                            <h3>{{ $category->name }}</h3>
                            <span class="badge bg-{{ $category->status == 'active' ? 'success' : 'secondary' }} mb-2">
                                {{ ucfirst($category->status) }}
                            </span>
                            
                            @if($category->description)
                                <p class="text-muted mt-3">{{ $category->description }}</p>
                            @endif
                            
                            <div class="row mt-4">
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <h5 class="text-primary mb-0">{{ $category->products_count }}</h5>
                                        <small>Total Products</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <h5 class="text-primary mb-0">{{ $category->children_count }}</h5>
                                        <small>Subcategories</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Parent Category -->
                    @if($category->parent)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Parent Category</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-level-up-alt text-muted me-2"></i>
                                <a href="{{ route('categories.show', $category->parent) }}">
                                    {{ $category->parent->name }}
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Products and Subcategories -->
                <div class="col-md-8">
                    <!-- Subcategories -->
                    @if($childCategories->count() > 0)
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Subcategories</h5>
                            <span class="badge bg-primary">{{ $childCategories->count() }}</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($childCategories as $child)
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $child->name }}</h6>
                                            <span class="badge bg-secondary">{{ $child->products_count }} products</span>
                                        </div>
                                        <div class="mt-2">
                                            <a href="{{ route('categories.show', $child) }}" class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Products -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Products</h5>
                            <span class="badge bg-primary">{{ $products->total() }}</span>
                        </div>
                        <div class="card-body">
                            @if($products->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($products as $product)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($product->image)
                                                            <img src="{{ Storage::url($product->image) }}" 
                                                                 alt="{{ $product->name }}" 
                                                                 class="rounded me-2" width="40" height="40">
                                                        @endif
                                                        <div>
                                                            <strong>{{ $product->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $product->sku }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>${{ number_format($product->price, 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                                                        {{ $product->stock }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $product->status == 'active' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($product->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('products.show', $product) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Pagination -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $products->links() }}
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">No products found in this category.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection