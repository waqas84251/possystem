<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class InventoryExport implements WithMultipleSheets
{
    protected $inventorySummary;
    protected $inventoryByCategory;
    protected $sections;
    protected $columns;
    
    public function __construct($inventorySummary, $inventoryByCategory, $sections, $columns)
    {
        $this->inventorySummary = $inventorySummary;
        $this->inventoryByCategory = $inventoryByCategory;
        $this->sections = $sections;
        $this->columns = $columns;
    }
    
    public function sheets(): array
    {
        $sheets = [];
        
        if (in_array('summary', $this->sections)) {
            $sheets[] = new InventorySummarySheet($this->inventorySummary);
        }
        
        if (in_array('categories', $this->sections)) {
            $sheets[] = new InventoryCategorySheet($this->inventoryByCategory, $this->inventorySummary, $this->columns);
        }
        
        return $sheets;
    }
}

class InventorySummarySheet implements FromCollection, WithHeadings, WithTitle
{
    protected $inventorySummary;
    
    public function __construct($inventorySummary)
    {
        $this->inventorySummary = $inventorySummary;
    }
    
    public function collection()
    {
        $data = collect([
            ['Total Products', $this->inventorySummary->total_products],
            ['Total Stock', $this->inventorySummary->total_stock],
            ['Total Value', '$' . number_format($this->inventorySummary->total_value, 2)],
            ['Low Stock Items', $this->inventorySummary->low_stock],
            ['Out of Stock Items', $this->inventorySummary->out_of_stock],
        ]);
        
        return $data;
    }
    
    public function headings(): array
    {
        return [
            'Inventory Summary',
            'Value'
        ];
    }
    
    public function title(): string
    {
        return 'Summary';
    }
}

class InventoryCategorySheet implements FromCollection, WithHeadings, WithTitle
{
    protected $inventoryByCategory;
    protected $inventorySummary;
    protected $columns;
    
    public function __construct($inventoryByCategory, $inventorySummary, $columns)
    {
        $this->inventoryByCategory = $inventoryByCategory;
        $this->inventorySummary = $inventorySummary;
        $this->columns = $columns;
    }
    
    public function collection()
    {
        $data = collect();
        
        foreach ($this->inventoryByCategory as $category) {
            $row = [];
            
            if (in_array('category', $this->columns)) $row[] = $category['name'];
            if (in_array('total_stock', $this->columns)) $row[] = $category['total_stock'];
            if (in_array('total_value', $this->columns)) $row[] = '$' . number_format($category['total_value'], 2);
            if (in_array('avg_value', $this->columns)) $row[] = '$' . number_format($category['average_value'], 2);
            
            $data->push($row);
        }
        
        // Add total row
        $totalRow = ['Total'];
        if (in_array('total_stock', $this->columns)) $totalRow[] = $this->inventorySummary->total_stock;
        if (in_array('total_value', $this->columns)) $totalRow[] = '$' . number_format($this->inventorySummary->total_value, 2);
        if (in_array('avg_value', $this->columns)) $totalRow[] = '$' . number_format($this->inventorySummary->total_stock > 0 ? $this->inventorySummary->total_value / $this->inventorySummary->total_stock : 0, 2);
        
        $data->push([]);
        $data->push($totalRow);
        
        return $data;
    }
    
    public function headings(): array
    {
        $headings = [];
        
        if (in_array('category', $this->columns)) $headings[] = 'Category';
        if (in_array('total_stock', $this->columns)) $headings[] = 'Total Stock';
        if (in_array('total_value', $this->columns)) $headings[] = 'Total Value';
        if (in_array('avg_value', $this->columns)) $headings[] = 'Average Value';
        
        return $headings;
    }
    
    public function title(): string
    {
        return 'By Category';
    }
}