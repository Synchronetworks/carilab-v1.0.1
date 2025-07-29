<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
// use Illuminate\Routing\Controller;
use Modules\Subscriptions\Models\Subscription;
use Currency;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
class ActivityLogController extends Controller
{
    protected string $exportClass = '\App\Exports\ActivityLogExport';
    public function __construct()
    {
        // Page Title

        $this->module_title = 'messages.lbl_activityLog';

        // module name
        $this->module_name = 'activitylog';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }
    public function index(Request $request)
    {
        $user_type = $request->route('user_type');
        $module_title = __('messages.lbl_activityLog');

        if ($user_type === 'vendor') {
            $module_title = __('messages.vendor_history');
        } elseif ($user_type === 'collector') {
            $module_title = __('messages.collector_history');
        } 
    
    
        $module_action = 'User List';
        $export_import = true;
        $export_columns = [
            [
                'value' => 'id',
                'text' => __('messages.id'),
            ],
            [
                'value' => 'causer_id',
                'text' => __('messages.created_by'),
            ],
            [
                'value' => 'log_name',
                'text' => __('messages.log_name'),
            ],
            [
                'value' => 'subject_type',
                'text' => __('messages.table_name'),
            ],
            [
                'value' => 'description',
                'text' => __('messages.description'),
            ],
            [
                'value' => 'created_at',
                'text' => __('messages.created_at'),
            ],
        ];
        $export_url = route('backend.activityLog.export',['user_type' => $user_type]);

        return view('backend.activity-log.index', compact('module_action','export_import', 'export_columns', 'export_url','user_type','module_title'));
    }

    public function index_data(Datatables $datatable,Request $request)
    {
        
        $query = Activity::query()
                ->leftJoin('users', 'activity_log.causer_id', '=', 'users.id')
                ->select([
                    'activity_log.id',
                    'activity_log.log_name',
                    'activity_log.description',
                    'activity_log.subject_type',
                    'activity_log.created_at',
                    'activity_log.properties',
                    'activity_log.causer_id',
                    'activity_log.causer_type',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    
                ]);
                $filter = $request->filter;
                if(!empty($filter['user_type'])){
                    $query->where('activity_log.causer_type', $filter['user_type']);
                }
                if (isset($filter['created_by'])) {
                    $fullName = $filter['created_by'];
        
                    $query->where(function ($query) use ($fullName) {
                        $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$fullName%"]);
                    });
                }
    $datatable = $datatable->eloquent($query)
        ->addColumn('check', function ($row) {
            return '<input type="checkbox" class="form-check-input select-table-row"  
                id="datatable-row-' . $row->id . '"  
                name="datatable_ids[]" 
                value="' . $row->id . '" 
                data-type="activity_logs" 
                onclick="dataTableRowCheck(' . $row->id . ', this)">';
        })
        ->editColumn('id', function ($data){
            return $data->id;
        })
        ->editColumn('log_name', function ($data){
            return  ucfirst(str_replace('_', ' ', $data->log_name));
        })
        ->editColumn('created_at', function ($data) {
            return Setting::formatDate($data->created_at);
        })
        ->editColumn('subject_type', function ($data){
            return $data->subject_type;
        })
        ->editColumn('description', function ($data) {
            return ucfirst(str_replace('_',' ',$data->description));
        })
        ->editColumn('created_by', function ($data) {
            $data = User::find($data->causer_id);
            return view('user::backend.users.user_details', compact('data'));
        })
        ->filterColumn('created_by', function ($query, $keyword) {
            // Applying the filter logic for the 'created_by' column
            $query->whereRaw("CONCAT(UPPER(users.first_name), ' ', UPPER(users.last_name)) LIKE ?", ['%' . strtoupper($keyword) . '%'])
                ->orWhere('email', 'like', '%' . $keyword . '%');
        })
        ->orderColumn('created_by', function ($query, $order) {
            $query->orderByRaw("CONCAT(users.first_name, ' ', users.last_name) {$order}, users.id {$order}");
        });
        
        

    return $datatable->rawColumns(['check', 'created_by'])
                     ->orderColumns([''], ':column $1')
                     ->toJson();
}
}