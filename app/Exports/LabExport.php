<?php

namespace App\Exports;

use Modules\Lab\Models\Lab;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Modules\Appointment\Models\Appointment;
use Modules\CatlogManagement\Models\CatlogManagement;
use App\Models\User;


class LabExport implements FromCollection, WithHeadings, WithStyles
{
    public array $columns;

    public function __construct($columns)
    {
        $this->columns = $columns;
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
        $query = Lab::myLabs()->orderBy('updated_at', 'desc')->get();

        return $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'status':
                        $selectedData[$column] = $row[$column] ? __('messages.active') : __('messages.inactive');
                        break;        
                    case 'name':
                        $selectedData[$column] = ($row->name ?? '-') . ' ' . ($row->email ?? '-');
                        break;
                    
                    case 'vendor':
                        $vendor = $row->vendor;
                        $selectedData[$column] = ($vendor->full_name ?? '-') . ' ' . ($vendor->email ?? '-');
                        break;

                    case 'booking_count':
                        $selectedData[$column] = Appointment::where('lab_id', $row->id)->count() ?: '-';
                        break;

                    case 'collectors_count':
                        $selectedData[$column] = User::where('user_type', 'collector')
                            ->whereHas('lab', function ($query) use ($row) {
                                $query->where('lab_id', $row->id);
                            })->count() ?: '-';
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
