<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Collector\Models\CollectorDocument;
class CollectorDocumentExport implements FromCollection, WithHeadings
{
    public array $columns;

    public function __construct($columns,$dataRange)
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
        $query = collectorDocument::query();

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

                        case 'collector':
                            $collector = $row->collectors;
                            $selectedData[$column] = ($collector->full_name ?? '-') . ' ' . ($collector->email ?? '-');
                        break; 

                        case 'document':
                            $document = $row->document;
                            $selectedData[$column] = ($document->name ?? '-');
                        break; 
                        case 'is_verified':
                            $selectedData[$column] = $row->is_verified ? __('messages.yes') : __('messages.no');
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
