<?php

namespace App\Exports;

use App\Models\User;
use Modules\Review\Models\Review;
use Modules\Subscriptions\Models\PlanLimitation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
class ActivityLogExport implements FromCollection, WithHeadings
{
    public array $columns;

    public $user_type;

    public function __construct($columns,$dateRange = null,$user_type)
    {
        $this->columns = $columns;
        $this->user_type = $user_type;
    }

    public function headings(): array
    {
        $modifiedHeadings = [];

        foreach ($this->columns as $column) {
            if ($column == 'causer_id') {
                $modifiedHeadings[] = __('messages.created_by'); // Rename it
            } elseif ($column == 'subject_type') {
                $modifiedHeadings[] = __('messages.table_name');
            } else {
                $modifiedHeadings[] = ucwords(str_replace('_', ' ', $column));
            }
        }

        return $modifiedHeadings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Activity::query();

        if ($this->user_type) {
            // Apply filtering logic based on user_type if needed
            $query->where('causer_type', $this->user_type);
        }

        $query = $query->orderBy('updated_at', 'desc');

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {

                    case 'causer_id':
                        $user = User::find($row->causer_id);
                        $selectedData[$column] = $user ? $user->first_name . ' ' . $user->last_name : __('messages.N/A');
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
