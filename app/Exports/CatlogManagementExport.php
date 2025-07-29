<?php

namespace App\Exports;

use Modules\CatlogManagement\Models\CatlogManagement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Lab\Models\Lab;
use Currency;

class CatlogManagementExport implements FromCollection, WithHeadings
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
        $query = CatlogManagement::MyCatlogManagement()->whereNull('parent_id');

        $query = $query->myCatlogManagement();

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
                            $selectedData[$column] = ($vendor->full_name ?? '-' ). ' ' . ($vendor->email ?? '-');
                        break;
                        case 'price':
                            $selectedData[$column] = Currency::format($row->price ?? 0);
                            break;
                        case 'test':
                            $category = $row->category;
                            $selectedData[$column] = $category->name ?? '-';
                        break;
                        case 'lab_count':
                            $selectedData[$column] = CatlogManagement::where('name', $row->name)
                                ->where('code', $row->code)
                                ->distinct()
                                ->count('lab_id') ?: '-';
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
