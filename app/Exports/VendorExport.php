<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\User;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\UserCommissionMapping;
use Modules\Document\Models\Document;
use Modules\vendor\Models\VendorDocument;
use Currency;
use Modules\Lab\Models\Lab;
class VendorExport implements FromCollection, WithHeadings, WithStyles
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
      
        $query = User::where('user_type','vendor');

        $previousUrl = url()->previous() ?? '';
        $segments = !empty($previousUrl) ? explode('/', $previousUrl) : [];
        $lastSegment = !empty($segments) ? end($segments) : '';
        // Check if we have a valid previous URL and last segment
        if (!empty($lastSegment) && $lastSegment === 'pending') {
            $query->where('status', 0); // Show only inactive vendors
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

                    case 'vendor':
                        $selectedData[$column] = ($row->full_name ?? '-') . ' ' . ($row->email ?? '-');
                        break;

                    case 'commission':
                        $commission = $row->userCommissionMapping;
                        $selectedData[$column] = $commission->map(function ($commission) {
                            return $commission->commission_type == 'Percentage' 
                                ? $commission->commission.'%' 
                                : Currency::format($commission->commission);
                        })->implode(', ');
                        break;
                        case 'labs_count':
                            $count = Lab::where('vendor_id', $row->id)->count(); // Adjust model name if different
                            
                            $selectedData[$column] = $count > 0 ? $count : '-';
                            break;
                        
                            case 'collectors_count':
                                $count = User::whereHas('collectorVendorMapping', function($query) use ($row) {
                                    $query->where('vendor_id', $row->id);
                                })->where('user_type', 'collector')->count();
                                
                                $selectedData[$column] = $count > 0 ? $count : '-';
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
    public function styles(Worksheet $sheet)
    {
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->columns));      
        $rowCount = User::where('user_type', 'vendor')->count() + 1;
        $sheet->getStyle("A1:{$lastColumn}{$rowCount}")->getAlignment()->setHorizontal('center');
    }
    
}
