<?php

namespace App\Exports;

use Modules\Review\Models\Review;
use Modules\Subscriptions\Models\PlanLimitation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Appointment\Models\AppointmentTransaction;
use App\Models\Setting;

class CashPaymentExport implements FromCollection, WithHeadings
{
    public array $columns;

    public function __construct($columns)
    {
        $this->columns = $columns;
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
        $query = AppointmentTransaction::with(['appointment.customer', 'appointment.package', 'appointment.catlog'])->where('payment_type', 'cash');
        
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
                        case 'customer':
                            $customer = $row->appointment->customer ?? null;
                            $selectedData[$column] = $customer
                                ? ($customer->full_name ?? '-') . ' ' . ($customer->email ?? '-')
                                : '-';
                            break;
        
                        case 'test':
                            if (!empty($row->appointment)) {
                                if ($row->appointment->test_type == 'test_package') {
                                    $test_case = optional($row->appointment->package)->name ?? '-';
                                } elseif ($row->appointment->test_type == 'test_case') {
                                    $test_case = optional($row->appointment->catlog)->name ?? '-';
                                }
                            }
                            $selectedData[$column] = $test_case ?? '-';
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
