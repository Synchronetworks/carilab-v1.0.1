<?php

namespace App\Exports;

use Modules\Location\Models\Location;
use Maatwebsite\Excel\Concerns\FromCollection;

class LocationExport implements FromCollection
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
        $query = Location::query();

        $query = $query->orderBy('updated_at', 'desc');

         $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                $selectedData[$column] = $row[$column];
            }
            return $selectedData;
        });

        return $newQuery;
    }
}
