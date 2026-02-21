@extends('layouts.app')

@section('title', 'Documentation')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">POS System Documentation</h1>
                <a href="{{ route('help.index') }}" class="btn btn-secondary">Back to Help Center</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="list-group">
                <a href="#getting-started" class="list-group-item list-group-item-action">Getting Started</a>
                <a href="#products" class="list-group-item list-group-item-action">Managing Products</a>
                <a href="#sales" class="list-group-item list-group-item-action">Processing Sales</a>
                <a href="#inventory" class="list-group-item list-group-item-action">Inventory Management</a>
                <a href="#reports" class="list-group-item list-group-item-action">Generating Reports</a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <h3 id="getting-started" class="mb-4">Getting Started</h3>
                    <p>Welcome to the POS System documentation. This guide will help you understand how to use all the features of our point-of-sale system.</p>
                    
                    <h5>System Requirements</h5>
                    <ul>
                        <li>Modern web browser (Chrome, Firefox, Safari, Edge)</li>
                        <li>Internet connection</li>
                        <li>Screen resolution of 1024x768 or higher</li>
                    </ul>
                    
                    <h5>First Time Setup</h5>
                    <p>To get started with your POS system:</p>
                    <ol>
                        <li>Add your products and categories</li>
                        <li>Set up your tax rates in Settings</li>
                        <li>Configure your payment methods</li>
                        <li>Add your staff users with appropriate permissions</li>
                    </ol>
                    
                    <h3 id="products" class="mb-4 mt-5">Managing Products</h3>
                    <p>Learn how to add, edit, and manage your product catalog.</p>
                    
                    <h5>Adding a New Product</h5>
                    <ol>
                        <li>Navigate to the Products section</li>
                        <li>Click the "Add New Product" button</li>
                        <li>Fill in the product details (name, price, category, etc.)</li>
                        <li>Set the initial stock quantity</li>
                        <li>Click "Save" to add the product</li>
                    </ol>
                    
                    <h5>Managing Categories</h5>
                    <p>Categories help organize your products. You can create, edit, and delete categories from the Categories section.</p>
                    
                    <!-- Add more documentation sections as needed -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection