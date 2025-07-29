<?php

namespace App\Exports;

use Modules\Coupon\Models\Coupon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CouponExport implements FromCollection, WithHeadings
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
        $query = Coupon::query();

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
                        case 'lab':
                            $lab = $row->lab;
                            $selectedData[$column] = $lab
                                ? ($lab->name ?? '-') . ' ' . ($lab->email ?? '-')
                                : '-';
                            break;
                            case 'vendor': 
                                $vendor = $row->vendor;
                                $selectedData[$column] = $vendor ? $vendor->full_name : '-';
                                break;
                                case 'discount_value':
                                    if ($row->discount_type === 'percentage') {
                                        $selectedData[$column] = $row->discount_value . ' %';
                                    } else {
                                        $selectedData[$column] = \Currency::format($row->discount_value);
                                    }
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
