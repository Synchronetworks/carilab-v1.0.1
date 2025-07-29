<?php

namespace App\Exports;

use Modules\Bank\Models\Bank;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class BankExport implements FromCollection, WithHeadings
{
    public array $columns;
    public string $user_type;
    public function __construct($columns,$dataRange,string $user_type)
    {
        $this->columns = $columns;
        $this->user_type = $user_type;
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
        $query = Bank::where('user_type',$this->user_type)->with('user');
        
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
                        case 'user_name':
                            $user = $row->user;
                            $selectedData[$column] = $user->full_name ?? '-' . ' ' . $user->email ?? '-';
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
