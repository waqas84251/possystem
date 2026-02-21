<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CustomersExport implements WithMultipleSheets
{
    protected $customerActivity;
    protected $topCustomers;
    protected $sections;
    protected $columns;
    
    public function __construct($customerActivity, $topCustomers, $sections, $columns)
    {
        $this->customerActivity = $customerActivity;
        $this->topCustomers = $topCustomers;
        $this->sections = $sections;
        $this->columns = $columns;
    }
    
    public function sheets(): array
    {
        $sheets = [];
        
        if (in_array('summary', $this->sections)) {
            $sheets[] = new CustomerSummarySheet($this->customerActivity, $this->topCustomers);
        }
        
        if (in_array('top_customers', $this->sections)) {
            $sheets[] = new TopCustomersSheet($this->topCustomers, $this->columns);
        }
        
        return $sheets;
    }
}

class CustomerSummarySheet implements FromCollection, WithHeadings, WithTitle
{
    protected $customerActivity;
    protected $topCustomers;
    
    public function __construct($customerActivity, $topCustomers)
    {
        $this->customerActivity = $customerActivity;
        $this->topCustomers = $topCustomers;
    }
    
    public function collection()
    {
        $avgCustomerValue = $this->customerActivity->total_customers > 0 ? 
            $this->topCustomers->sum('total_spent') / $this->customerActivity->total_customers : 0;
            
        $data = collect([
            ['Total Customers', $this->customerActivity->total_customers],
            ['Active Customers', $this->customerActivity->active_customers],
            ['New Customers (30 days)', $this->customerActivity->new_customers],
            ['Average Customer Value', '$' . number_format($avgCustomerValue, 2)],
        ]);
        
        return $data;
    }
    
    public function headings(): array
    {
        return [
            'Customer Summary',
            'Value'
        ];
    }
    
    public function title(): string
    {
        return 'Summary';
    }
}

class TopCustomersSheet implements FromCollection, WithHeadings, WithTitle
{
    protected $topCustomers;
    protected $columns;
    
    public function __construct($topCustomers, $columns)
    {
        $this->topCustomers = $topCustomers;
        $this->columns = $columns;
    }
    
    public function collection()
    {
        $data = collect();
        
        foreach ($this->topCustomers as $customer) {
            $row = [];
            
            if (in_array('customer_name', $this->columns)) $row[] = $customer->name;
            if (in_array('email', $this->columns)) $row[] = $customer->email ?? 'No email';
            if (in_array('phone', $this->columns)) $row[] = $customer->phone ?? 'No phone';
            if (in_array('total_orders', $this->columns)) $row[] = $customer->sales_count;
            if (in_array('total_spent', $this->columns)) $row[] = '$' . number_format($customer->total_spent, 2);
            if (in_array('avg_order_value', $this->columns)) {
                $avgValue = $customer->sales_count > 0 ? $customer->total_spent / $customer->sales_count : 0;
                $row[] = '$' . number_format($avgValue, 2);
            }
            
            $data->push($row);
        }
        
        return $data;
    }
    
    public function headings(): array
    {
        $headings = [];
        
        if (in_array('customer_name', $this->columns)) $headings[] = 'Customer Name';
        if (in_array('email', $this->columns)) $headings[] = 'Email';
        if (in_array('phone', $this->columns)) $headings[] = 'Phone';
        if (in_array('total_orders', $this->columns)) $headings[] = 'Total Orders';
        if (in_array('total_spent', $this->columns)) $headings[] = 'Total Spent';
        if (in_array('avg_order_value', $this->columns)) $headings[] = 'Avg. Order Value';
        
        return $headings;
    }
    
    public function title(): string
    {
        return 'Top Customers';
    }
}