<?php
namespace App\Exports;

use \Modules\Subscriptions\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Currency;
use App\Models\Setting;

class VendorSubscriptionExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $columns;
    protected Collection $subscriptions;

    public function __construct(array $columns)
    {
        $this->columns = $columns;
        $this->subscriptions = $this->fetchData(); // Fetch data once
    }

    public function headings(): array
    {
        return array_map(fn($column) => ucwords(str_replace('_', ' ', $column)), $this->columns);
    }

    public function collection(): Collection
    {
        return $this->subscriptions;
    }

    private function fetchData(): Collection
    {
        return Subscription::with(['user', 'subscription_transaction', 'plan'])
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function map($subscription): array
    {
        $mappedData = [];

        foreach ($this->columns as $column) {
            switch ($column) {
                case 'name':
                    $mappedData[] = $subscription->user->full_name ?? '-';
                    break;
                
                case 'plan':
                    $mappedData[] = $subscription->plan->name ?? '-';
                    break;

                case 'duration':
                    $mappedData[] = $subscription->duration . ' ' . ucfirst($subscription->type);
                    break;

                case 'total_amount':
                    $mappedData[] = \Currency::format($subscription->subscription_transaction->amount ?? 0);
                    break;

                case 'start_date':
                    $mappedData[] = Setting::formatDate($subscription->start_date);
                    break;

                case 'end_date':
                    $mappedData[] = Setting::formatDate($subscription->end_date);
                    break;

                case 'status':
                    $mappedData[] = strip_tags($subscription->getStatusLabelAttribute());
                    break;

                default:
                    $mappedData[] = $subscription->{$column} ?? '-';
                    break;
            }
        }

        return $mappedData;
    }
}
?>