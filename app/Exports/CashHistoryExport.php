<?php

namespace App\Exports;

use Modules\Review\Models\Review;
use Modules\Subscriptions\Models\PlanLimitation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Appointment\Models\AppointmentTransaction;
use App\Models\Setting;
use Modules\Appointment\Models\CashPaymentHistories;

class CashHistoryExport implements FromCollection, WithHeadings
{
    public array $columns;

    public $id;

    public function __construct($columns,$dateRange = null, $user_type = null , $id)
    {
        $this->columns = $columns;
        $this->id = $id;
    }

    public function headings(): array
    {
        $modifiedHeadings = [];

        foreach ($this->columns as $column) {
            $modifiedHeadings[] = ucwords(str_replace('_', ' ', $column));
        }

        return $modifiedHeadings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = CashPaymentHistories::where('transaction_id', $this->id);
        
        $query = $query->orderBy('updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'datetime':
                        $paymentDate = $row->updated_at ?? '-';
                        $paymentDate = Setting::formatDate($paymentDate);
                        $paymentTime = Setting::formatTime($paymentDate);
                        $selectedData[$column] = $paymentDate.' '. $paymentTime;
                        break;
                    case 'sender':
                        $selectedData[$column] = ($row->sender != null) 
                            ? $row->sender->first_name . ' ' . $row->sender->last_name 
                            : '-';
                        break;
    
                    case 'receiver':
                        $selectedData[$column] = ($row->receiver != null) 
                            ? $row->receiver->first_name . ' ' . $row->receiver->last_name 
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
