<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
use App\Models\User;
use Currency;

class ReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $columns;
    protected Collection $reports;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
        $this->reports = $this->fetchData(); // Fetch data once
    }

    public function headings(): array
    {
        return array_map(fn($column) => ucwords(str_replace('_', ' ', $column)), $this->columns);
    }

    public function collection(): Collection
    {
        return $this->reports;
    }

    private function fetchData(): Collection
    {
        return User::MyVendor()
            ->SetRole(auth()->user())
            ->withCommissionData('vendor')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function map($report): array
    {
        $mappedData = [];

        foreach ($this->columns as $column) {
            switch ($column) {

                case 'name':
                    $mappedData[] = $report->first_name . ' ' . $report->last_name; // Full name
                    break;
    
                case 'total_appointment':
                    $mappedData[] = $report->total_appointments > 0
                        ? $report->total_appointments
                        : '0';
                    break;

                case 'total_service_amount':
                    $mappedData[] = Currency::format($report->total_service_amount);
                    break;

                case 'total_admin_earning':
                    $mappedData[] = Currency::format($report->total_admin_earnings);
                    break;

                case 'total_vendor_earning':
                    $mappedData[] = Currency::format($report->total_vendor_earnings);
                    break;

                case 'total_collector_earning':
                    $mappedData[] = Currency::format($report->total_collector_earnings);
                    break;

                case 'total_tax':
                    $mappedData[] = Currency::format($report->total_tax_amount);
                    break;

                default:
                    $mappedData[] = $report->{$column} ?? '';
                    break;
            }
        }

        return $mappedData;
    }
}
?>
