<?php
namespace App\Exports;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Setting;

class TopTestCaseBookedExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $columns;
    protected Collection $appointments;
    protected int $totalBookings;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
        $this->totalBookings = \Modules\Appointment\Models\Appointment::count(); // Get total bookings
        $this->appointments = $this->fetchData(); // Fetch data once
    }

    public function headings(): array
    {
        return array_map(fn($column) => ucwords(str_replace('_', ' ', $column)), $this->columns);
    }

    public function collection(): Collection
    {
        return $this->appointments;
    }

    private function fetchData(): Collection
    {
        return \Modules\Appointment\Models\Appointment::selectRaw('test_id, test_type, COUNT(*) as booking_count, MAX(created_at) as last_booking_date')
            ->whereIn('test_type', ['test_case', 'test_package'])
            ->groupBy('test_id', 'test_type')
            ->with([
                'catlog.category:id,name', // For test_case
                'package:id,name' // For test_package
            ])
            ->get();
    }

    public function map($appointment): array
    {
        $mappedData = [];

        foreach ($this->columns as $column) {
            switch ($column) {
                case 'test_case':
                    $mappedData[] = $appointment->test_type == 'test_case'
                        ? optional($appointment->catlog)->name
                        : optional($appointment->package)->name ?? '-';
                    break;
                
                case 'test_category':
                    $mappedData[] = $appointment->test_type == 'test_case'
                        ? optional($appointment->catlog->category)->name ?? '-'
                        : '-';
                    break;

                case 'booking_count':
                    $mappedData[] = $appointment->booking_count ?? 0;
                    break;

                case 'booking_percentage':
                    $bookingPercentage = ($this->totalBookings > 0)
                        ? ($appointment->booking_count / $this->totalBookings) * 100
                        : 0;
                    $mappedData[] = number_format($bookingPercentage, 2) . '%';
                    break;

                case 'last_booking_date':
                    $mappedData[] = $appointment->last_booking_date
                        ? Setting::formatDate($appointment->last_booking_date)
                        : '-';
                    break;

                default:
                    $mappedData[] = $appointment->{$column} ?? '-';
                    break;
            }
        }

        return $mappedData;
    }
}
