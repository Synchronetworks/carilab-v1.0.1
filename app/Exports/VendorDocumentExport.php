<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Vendor\Models\VendorDocument;

class VendorDocumentExport implements FromCollection, WithHeadings, WithStyles
{
    public array $columns;

    public function __construct($columns, $dataRange)
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
        $query = VendorDocument::query()->orderBy('updated_at', 'desc')->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'status':
                        $selectedData[$column] = $row[$column] ? __('messages.active') : __('messages.inactive');
                        break;

                    case 'vendor':
                        $vendor = $row->vendors;
                        $selectedData[$column] = ($vendor->full_name ?? '-') . ' ' . ($vendor->email ?? '-');
                        break;

                    case 'document':
                        $document = $row->document;
                        $selectedData[$column] = ($document->name ?? '-');
                        break;

                    case 'is_verified':
                        $selectedData[$column] = $row[$column] == 1 ? __('messages.verifed') : __('messages.not_verifed'); // Explicitly set 'Not Verified' for 0
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

    /**
     * Apply styles to the spreadsheet
     */
    public function styles(Worksheet $sheet)
    {
        $columnIndex = array_search('is_verified', $this->columns);

        if ($columnIndex !== false) {
            $excelColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex + 1);
            $sheet->getStyle($excelColumn . ':' . $excelColumn)->getAlignment()->setHorizontal('center');
        }
    }
}
