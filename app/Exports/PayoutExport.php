<?php

namespace App\Exports;

use Modules\Subscriptions\Models\Plan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Currency;
use App\Models\Setting;
use Modules\Payout\Models\Payout;

class PayoutExport implements FromCollection, WithHeadings
{
    public array $columns;

    public $user_type;

    public function __construct($columns, $dateRange = null,$user_type)
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
        $query = Payout::query()->where('user_type',$this->user_type);

        $query = $query->orderBy('updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {

                        case 'amount':
                            $selectedData[$column] = Currency::format($row->amount ?? 0);
                            break;
                        case 'paid_date':
                            $paidDate = $row->paid_date ?? '-';
                            $paidDate = Setting::formatDate($paidDate);
                            $selectedData[$column] = $paidDate;   
                            break;
                        case 'name':
                            $user = $row->user;
                            $selectedData[$column] = $user->full_name ?? '-' . ' ' . $user->email ?? '-';
                            break;
                        case 'payment_method':
                            $selectedData[$column] = !empty($row->payment_method) ? ucfirst($row->payment_method) : '-';
                            break; 

                    default:
                        $selectedData[$column] =  $row->$column ?? '-';
                        break;
                }
            }

            return $selectedData;
        });

        return $newQuery;
    }
}
