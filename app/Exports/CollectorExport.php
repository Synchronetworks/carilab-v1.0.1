<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;
class CollectorExport implements FromCollection, WithHeadings
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
        $query = User::where('user_type','collector');
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
                        case 'details':
                            $selectedData[$column] = ($row->full_name ?? '-') . ' ' . ($row->email ?? '-');
                            break;
                        
                            case 'vendor':
                                $vendorMapping = $row->collectorVendorMapping;
                                $vendor = $vendorMapping ? $vendorMapping->vendor : null;
                                $selectedData[$column] = $vendor
                                ?  ($vendor->full_name ?? '-') . ' ' . ($vendor->email ?? '-')
                                : '-';
                                break;
                                case 'lab':
                                    $labMapping = $row->lab;
                                    $lab = $labMapping ? $labMapping->lab : null;
                                    $selectedData[$column] = $lab
                                    ?  ($lab->full_name ?? '-') . ' ' . ($lab->email ?? '-')
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
