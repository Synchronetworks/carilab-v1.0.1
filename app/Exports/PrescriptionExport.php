<?php

namespace App\Exports;

use Modules\Prescription\Models\Prescription;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PrescriptionExport implements FromCollection, WithHeadings, WithStyles
{
    public array $columns;

    public function __construct($columns,$dateRange = null,$user_type = null, $prescriptions_status = null, $id = null)
    {
        $this->columns = $columns;
        $this->dateRange = $dateRange;
        $this->user_type = $user_type;  
        $this->prescriptions_status = $prescriptions_status;
        $this->id = $id;
    }

    public function headings(): array
    {
        return array_map(function ($column) {
            return ucwords(str_replace('_', ' ', $column)); // Format headings properly
        }, $this->columns);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Prescription::MyPrescription()->orderBy('updated_at', 'desc')->get();
        $query = $query->where('prescription_status', $this->prescriptions_status == 'pending' ? 0 : 1);
        return $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'status':
                        $selectedData[$column] = $row[$column] ? __('messages.active') : __('messages.inactive');
                        break;        
                    
                    case 'customer':
                        $user = $row->user;
                        $selectedData[$column] = ($user->full_name ?? '-') . ' ' . ($user->email ?? '-');
                        break;

                    case 'lab':
                        $labMapping = $row->labMappings->first(); // Get the first lab mapping
                        $lab = optional($labMapping?->lab)->name; // Get lab name safely
                        $selectedData[$column] = $lab ?: '-';
                        break;

                    case 'test_case_counter':
                        $selectedData[$column] = CatlogManagement::where('lab_id', $row->id)->count() ?: '-';
                        break;

                    default:
                        $selectedData[$column] = $row[$column] ?? '-';
                        break;
                }
            }

            return $selectedData;
        });
    }

    /**
     * Apply styles to the sheet.
     */
    public function styles(Worksheet $sheet)
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Apply center alignment to all data cells
        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getAlignment()->setHorizontal('center');

        // Ensure that numbers are treated as text to prevent misalignment
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getStyle("{$col}2:{$col}{$highestRow}")->getNumberFormat()->setFormatCode('@');
        }

        return [
            // Center align all headings
            1 => ['alignment' => ['horizontal' => 'center', 'vertical' => 'center']],
        ];
    }
}
