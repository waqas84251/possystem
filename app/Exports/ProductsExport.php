<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductsExport implements WithMultipleSheets
{
    protected $topProducts;
    protected $lowStockProducts;
    protected $productsByCategory;
    protected $sections;
    protected $columns;
    
    public function __construct($topProducts, $lowStockProducts, $productsByCategory, $sections, $columns)
    {
        $this->topProducts = $topProducts;
        $this->lowStockProducts = $lowStockProducts;
        $this->productsByCategory = $productsByCategory;
        $this->sections = $sections;
        $this->columns = $columns;
    }
    
    public function sheets(): array
    {
        $sheets = [];
        
        if (in_array('top_products', $this->sections)) {
            $sheets[] = new TopProductsSheet($this->topProducts, $this->columns);
        }
        
        if (in_array('low_stock', $this->sections) && $this->lowStockProducts->count() > 0) {
            $sheets[] = new LowStockProductsSheet($this->lowStockProducts);
        }
        
        if (in_array('categories', $this->sections)) {
            $sheets[] = new ProductsByCategorySheet($this->productsByCategory);
        }
        
        return $sheets;
    }
}

class TopProductsSheet implements FromArray, WithHeadings, WithTitle
{
    protected $topProducts;
    protected $columns;
    
    public function __construct($topProducts, $columns)
    {
        $this->topProducts = $topProducts;
        $this->columns = $columns;
    }
    
    public function array(): array
    {
        $data = [];
        
        foreach ($this->topProducts as $product) {
            $row = [];
            if (in_array('product_name', $this->columns)) $row[] = $product->name;
            if (in_array('category', $this->columns)) $row[] = $product->category->name;
            if (in_array('units_sold', $this->columns)) $row[] = $product->total_sold;
            if (in_array('revenue', $this->columns)) $row[] = '$' . number_format($product->total_revenue, 2);
            if (in_array('stock', $this->columns)) $row[] = $product->stock;
            
            $data[] = $row;
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        $headers = [];
        if (in_array('product_name', $this->columns)) $headers[] = 'Product Name';
        if (in_array('category', $this->columns)) $headers[] = 'Category';
        if (in_array('units_sold', $this->columns)) $headers[] = 'Units Sold';
        if (in_array('revenue', $this->columns)) $headers[] = 'Revenue';
        if (in_array('stock', $this->columns)) $headers[] = 'Stock';
        
        return $headers;
    }
    
    public function title(): string
    {
        return 'Top Products';
    }
}

class LowStockProductsSheet implements FromArray, WithHeadings, WithTitle
{
    protected $lowStockProducts;
    
    public function __construct($lowStockProducts)
    {
        $this->lowStockProducts = $lowStockProducts;
    }
    
    public function array(): array
    {
        $data = [];
        
        foreach ($this->lowStockProducts as $product) {
            $status = $product->stock == 0 ? 'Out of Stock' : 'Low Stock';
            $data[] = [
                $product->name,
                $product->category->name,
                $product->stock,
                '$' . number_format($product->price, 2),
                $status
            ];
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        return ['Product Name', 'Category', 'Current Stock', 'Price', 'Status'];
    }
    
    public function title(): string
    {
        return 'Low Stock';
    }
}

class ProductsByCategorySheet implements FromArray, WithHeadings, WithTitle
{
    protected $productsByCategory;
    
    public function __construct($productsByCategory)
    {
        $this->productsByCategory = $productsByCategory;
    }
    
    public function array(): array
    {
        $data = [];
        
        foreach ($this->productsByCategory as $category) {
            $data[] = [
                $category->name,
                $category->products_count,
                $category->total_stock,
                '$' . number_format($category->total_value, 2)
            ];
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        return ['Category', 'Number of Products', 'Total Stock', 'Total Value'];
    }
    
    public function title(): string
    {
        return 'By Category';
    }
}