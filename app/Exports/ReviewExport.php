<?php

namespace App\Exports;

use Modules\Review\Models\Review;
use Modules\Subscriptions\Models\PlanLimitation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReviewExport implements FromCollection, WithHeadings
{
    public array $columns;

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    public function headings(): array
    {
        $modifiedHeadings = [];

        foreach ($this->columns as $column) {
            $modifiedHeadings[] = ucwords(str_replace('_', ' ', $column));
        }

        return $modifiedHeadings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Review::query();

        $query = $query->orderBy('updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'customer':
                        $customer = $row->user;
                        $selectedData[$column] = $customer
                            ? ($customer->full_name ?? '-') . ' ' . ($customer->email ?? '-')
                            : '-';
                        break;

                    case 'lab':
                        $lab = $row->lab;
                        $selectedData[$column] = $lab
                            ? ($lab->name ?? '-') . ' ' . ($lab->email ?? '-')
                            : '-';
                        break;
                    case 'collector':
                        $collectorMapping = $row->collector;
                        $collector = $collectorMapping ? $collectorMapping : null;
                        $selectedData[$column] = $collector
                            ? ($collector->full_name ??  '-') . '' . ($collector->email ?? '-')
                            : '-';
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
