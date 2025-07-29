<?php

namespace App\Exports;

use Modules\Lab\Models\LabSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class LabSessionExport implements FromCollection, WithHeadings
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
        $user = auth()->user();
        $vendorId = $user->id;
        $query = LabSession::query();
        if (!in_array($user->user_type, ['admin', 'demo_admin'])) {
            $query->whereHas('lab', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });
        }
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
                            $selectedData[$column] = ($lab->name ?? '-').' '.($lab->email ?? '-');
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
