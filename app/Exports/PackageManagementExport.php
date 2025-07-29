<?php

namespace App\Exports;

use Modules\PackageManagement\Models\PackageManagement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Currency;
use Modules\PackageManagement\Models\PackageCatlogMapping;

class PackageManagementExport implements FromCollection, WithHeadings
{
    public array $columns;

    public function __construct($columns, $dateRange)
    {
        $this->columns = $columns;
    }

    public function headings(): array
    {
        $modifiedHeadings = [];

        foreach ($this->columns as $column) {
            // Capitalize each word and replace underscores with spaces
            $modifiedHeadings[] = ucwords(str_replace('_', ' ', $column));
        }

        return $modifiedHeadings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = PackageManagement::myPackageManagement();

        $query = $query->orderBy('updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {

                    case 'status':
                        $selectedData[$column] = __('messages.inactive');
                        if ($row[$column]) {
                            $selectedData[$column] = __('messages.active');
                        }
                        break;
                        case 'vendor':
                            $vendor = $row->vendor;
                            $selectedData[$column] = ($vendor->full_name ?? '-' ). '' . ($vendor->email ?? '-');
                        break;
                        case 'price':
                            $selectedData[$column] = Currency::format($row->price ?? 0);
                            break;
                        case 'discount_price':
                            if ($row->discount_type === 'percentage') {
                                $selectedData[$column] = ($row->discount_price ?? 0) . '%';
                            } else {
                                $selectedData[$column] = Currency::format($row->discount_price ?? 0);
                            }
                            break;
        
                        case 'discount_type':
                            $selectedData[$column] = ucfirst($row->discount_type ?? 'fixed');
                            break;
                            case 'lab_count':
                                // Get count of labs for this package
                                $labIds = PackageManagement::where('name', $row->name)
                                    ->distinct()
                                    ->pluck('lab_id');
                                $selectedData[$column] = count($labIds);
                                break;
                            
                            case 'test_case_count':
                                // Get count of test cases/catalogs for this package
                                $count = PackageCatlogMapping::where('package_id', $row->id)->count();
                                $selectedData[$column] = $count;
                                break;
                    default:
                        $selectedData[$column] = $row[$column];
                        break;
                }
            }

            return $selectedData;
        });

        return $newQuery;
    }
}
