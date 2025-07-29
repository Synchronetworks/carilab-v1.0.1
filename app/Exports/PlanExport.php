<?php

namespace App\Exports;

use Modules\Subscriptions\Models\Plan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Currency;
class PlanExport implements FromCollection, WithHeadings
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
        $query = Plan::query();

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
                        case 'price':
                            $selectedData[$column] = Currency::format($row->price ?? 0);
                            break;
                        case 'total_price':
                            $selectedData[$column] = Currency::format($row->total_price ?? 0);
                            break;
                            case 'discount_percentage':
                                $selectedData[$column] = number_format($row->discount_percentage ?? 0, 2) . '%';
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
