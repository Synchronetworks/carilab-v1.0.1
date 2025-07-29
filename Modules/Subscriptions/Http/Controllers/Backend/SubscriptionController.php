<?php

namespace Modules\Subscriptions\Http\Controllers\Backend;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
// use Illuminate\Routing\Controller;
use Modules\Subscriptions\Models\Subscription;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Currency;
use Illuminate\Http\Request;


class SubscriptionController extends Controller
{
    protected string $exportClass = '\App\Exports\SubscriptionExport';
    public function __construct()
    {
        // Page Title
        $this->module_title =__('messages.subscriptions');

        // module name
        $this->module_name = 'subscriptions';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(Request $request)
    {
        $module_action = __('messages.user_list');
        $export_import = true;
        $export_columns = [

            [
                'value' => 'user_details',
                'text' => __('messages.user'),
            ],
            [
                'value' => 'name',
                'text' => __('messages.plan'),
            ],
            [
                'value' => 'start_date',
                'text' => __('messages.purchase_date'),
            ],
            [
                'value' => 'end_date',
                'text' => __('messages.expire_date'),
            ],
            [
                'value' => 'amount',
                'text' => __('messages.lbl_amount'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
        ];
        $export_url = route('backend.subscriptions.export');

        return view('subscriptions::backend.subscriptions.index', compact('module_action','export_import', 'export_columns', 'export_url'));
    }

    public function index_data(Datatables $datatable,Request $request)
    {
        $query = Subscription::query()
        ->with('user')
        ->whereHas('user', function ($query) {
            $query->where('user_type', 'vendor');
        });
        $datatable = $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="subscriptions" onclick="dataTableRowCheck('.$row->id.', this)">';
            })

            ->editColumn('user_id', function ($data) {
                $data = $data->user;
                return view('user::backend.users.user_details', compact('data'));
           
            })
            ->orderColumn('user_id', function ($query, $direction) {
                $query->select('subscriptions.*')
                    ->leftJoin('users', 'subscriptions.user_id', '=', 'users.id')
                    ->orderBy('users.first_name', $direction)
                    ->orderBy('subscriptions.id', $direction); // Add secondary sorting
            })
            ->editColumn('duration', function ($data) {
                return $data->duration . ' '. $data->type;
            })
            ->editColumn('start_date', function ($data) {
                return Setting::formatDate($data->start_date);
            })            
            ->editColumn('end_date', function ($data) {
                return Setting::formatDate($data->end_date);
            })
            ->editColumn('amount', function ($data) {
                return Currency::format($data->amount);
            })
            ->editColumn('tax_amount', function ($data) {
                return Currency::format($data->tax_amount);
            })
            ->editColumn('total_amount', function ($data) {
                return Currency::format($data->total_amount);
            })
            ->editColumn('name', function ($data) {
                return $data->name;
            })
            ->editColumn('status', function ($data) {
                return $data->status;
            })
            ->filterColumn('status', function($query, $keyword) {
                if ($keyword == 'inactive') {
                    $query->where('status', 'inactive');
                } else if ($keyword == 'active') {
                    $query->where('status', 'active');
                }
            })
            ->filterColumn('user_id', function($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function($q) use ($keyword) {

                        $q->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');

                    });
                }
            })
            ->filterColumn('start_date', function($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(start_date, '%D %M %Y') like ?", ["%$keyword%"]);
            })
            ->filterColumn('end_date', function($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(end_date, '%D %M %Y') like ?", ["%$keyword%"]);
            })
            ->filterColumn('amount', function($query, $keyword) {
                // Remove any non-numeric characters except for the decimal point
                $cleanedKeyword = preg_replace('/[^0-9.]/', '', $keyword);

                // Check if the cleaned keyword is not empty
                if ($cleanedKeyword !== '') {
                    // Filter the query by removing non-numeric characters from the amount column
                    $query->whereRaw("CAST(REGEXP_REPLACE(amount, '[^0-9.]', '') AS DECIMAL(10, 2)) LIKE ?", ["%{$cleanedKeyword}%"]);
                }
            })
            ->filterColumn('total_amount', function($query, $keyword) {

                $cleanedKeyword = preg_replace('/[^0-9.]/', '', $keyword);

                if ($cleanedKeyword !== '') {
                    $query->whereRaw("CAST(REGEXP_REPLACE(total_amount, '[^0-9.]', '') AS DECIMAL(10, 2)) LIKE ?", ["%{$cleanedKeyword}%"]);
                }
            })


            ->orderColumns(['id'], '-:column $1');

        return $datatable->rawColumns(array_merge(['check','user_id', 'start_date', 'end_date', 'amount', 'name','action']))
            ->toJson();
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'subscription';
        $messageKey = __('messages.bulk_action');


        return $this->performBulkAction(subscription::class, $ids, $actionType, $messageKey, $moduleName);
    }

}