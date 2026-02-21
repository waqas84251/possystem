<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $sales;
    protected $columns;
    
    public function __construct($sales, $columns)
    {
        $this->sales = $sales;
        $this->columns = $columns;
    }
    
    public function collection()
    {
        return $this->sales;
    }
    
    public function headings(): array
    {
        $headers = [];
        if (in_array('sale_number', $this->columns)) $headers[] = 'Sale Number';
        if (in_array('date', $this->columns)) $headers[] = 'Date & Time';
        if (in_array('customer', $this->columns)) $headers[] = 'Customer';
        if (in_array('items', $this->columns)) $headers[] = 'Items';
        if (in_array('amount', $this->columns)) $headers[] = 'Total Amount';
        if (in_array('payment', $this->columns)) $headers[] = 'Payment Method';
        
        return $headers;
    }
    
    public function map($sale): array
    {
        $row = [];
        if (in_array('sale_number', $this->columns)) $row[] = $sale->sale_number;
        if (in_array('date', $this->columns)) $row[] = $sale->created_at->format('M d, Y h:i A');
        if (in_array('customer', $this->columns)) $row[] = $sale->customer ? $sale->customer->name : 'Walk-in';
        if (in_array('items', $this->columns)) $row[] = $sale->items->sum('quantity') . ' items';
        if (in_array('amount', $this->columns)) $row[] = '$' . number_format($sale->total_amount, 2);
        if (in_array('payment', $this->columns)) $row[] = $sale->payment_method;
        
        return $row;
    }
}