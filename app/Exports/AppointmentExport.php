<?php

namespace App\Exports;

use Modules\Appointment\Models\Appointment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Setting;
use Modules\Appointment\Models\AppointmentCollectorMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Currency;
class AppointmentExport implements FromCollection, WithHeadings,WithStyles
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
        $query = Appointment::myAppointment();

        $query = $query->MyAppointment();

        $query = $query->orderBy('updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                     case 'appointment_id':
                        $selectedData[$column] = $row->id ?? '-';
                        break;
                    case 'datetime':
                        $appointmentDate = $row->appointment_date ?? '-';
                        $appointmentTime = $row->appointment_time ?? '-';
                        $appointmentDate = Setting::formatDate($appointmentDate);
                        $appointmentTime = Setting::formatTime($appointmentTime);
                        $selectedData[$column] = $appointmentDate.' '. $appointmentTime;
                        break;
                                            
                        case 'customer':
                            $customer = $row->customer;
                            $selectedData[$column] = $customer
                                ? ($customer->full_name ?? '-') . ' ' . ($customer->email ?? '-')
                                : '-';
                            break;

                        case 'lab':
                            $lab = $row->lab;
                            $selectedData[$column] = $lab
                                ? ($lab->name ?? '-') . ' ' . ($lab->email ?? '-')
                                : '-';
                            break;
                            case 'total_amount':
                                $selectedData[$column] = Currency::format($row->total_amount ?? 0);
                                break;
                        case 'collector':
                            $collectorMapping = $row->appointmentCollectorMapping;
                            $collector = $collectorMapping ? $collectorMapping->collector : null;
                            $selectedData[$column] = $collector
                                ? ($collector->full_name ??  '-') . '' . ($collector->email ?? '-')
                                : '-';
                            break;

                        case 'vendor':
                            $vendor = $row->vendor;
                            $selectedData[$column] = $vendor
                                ?  ($vendor->full_name ?? '-') . ' ' . ($vendor->email ?? '-')
                                : '-';
                            break;
                        case 'test':
                            $test = $row->getTestAttribute();
                            $selectedData[$column] = $test->name ?? '-';
                            break;
                        case 'payment_status':
                            $transactions = $row->transactions;
                            $selectedData[$column] = $transactions->payment_status;
                            break;
                            case 'total_amount':
                                $selectedData[$column] = Currency::format($row->total_amount ?? 0);
                                break;
                            case 'payment_type':
                                $transactions = $row->transactions;
                                $selectedData[$column] = $transactions->payment_type ?? '-';
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
        applyExcelStyles($sheet);
    }
    
}
