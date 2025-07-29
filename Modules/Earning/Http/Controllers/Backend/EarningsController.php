<?php

namespace Modules\Earning\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Modules\Earning\Models\Earning;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Trait\ModuleTrait;
use App\Models\User;
use Currency;
use App\Trait\ActivityLogger;
use App\Models\Setting;

class EarningsController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\EarningExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.lbl_earning', // module title
            'earnings', // module name
            'fa-solid fa-clipboard-list' // module icon
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = __('messages.list');

        return view('earning::backend.index', compact('module_action', 'filter'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.lbl_earning'); // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Earning::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = User::MyCollector()->SetRole(auth()->user())->withCommissionData('collector');
        if($request->ajax()) {
        return $datatable->eloquent($query)
            ->addColumn('action', function ($data) {
                return $data->total_commission_amount > 0
                    ? '<a href=' . route('backend.payouts.create', ['id' => $data->id]) . '><i class="fas fa-money-bill-alt earning-icon"></i></a>'
                    : '-';
            })
            ->editColumn('user_id', fn($data) => view('user::backend.users.user_details', compact('data')))
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                }
            })
            ->orderColumn('user_id', fn($query, $order) => $query->orderBy('first_name', $order)->orderBy('last_name', $order), 1)
            ->editColumn('total_appointment', function ($data) {
                $title = __('messages.view_collector_appointments');
                return $data->total_appointments > 0
                    ? "<b><a href='" . route('backend.appointments.index', ['collector_id' => encrypt($data->id)]) . "' class='text-primary text-nowrap px-1' title='{$title}'>{$data->total_appointments}</a></b>"
                    : "<b><span class='text-primary text-nowrap px-1' title='{$title}'>0</span></b>";
            })
            ->editColumn('total_service_amount', fn($data) => Currency::format($data->total_service_amount))
            ->editColumn('total_admin_earning', fn($data) => Currency::format($data->total_admin_earnings))
            ->editColumn('total_vendor_earning', fn($data) => Currency::format($data->total_vendor_earnings))
            ->editColumn('collector_pay_due', fn($data) => Currency::format($data->total_commission_amount))
            ->editColumn('collector_paid_earning', function ($data) {
                return "<b><a href='" . route('backend.payouts.index', $data->id) . "' class='text-primary text-nowrap px-1' title='View Collector Payout'>" . Currency::format($data->collector_paid_earnings) . "</a></b>";
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'user_id', 'total_appointment', 'collector_paid_earning'])
            ->toJson();
        }
            if($request->is('api/*')) {
                if (auth()->user()->hasAnyRole(['collector'])) {
                    $noOfDecimal = Setting::getSettings('digitafter_decimal_point') ?? 2;
                    $collector_list = $query->get()->map(function ($collector) use($noOfDecimal){
                        return [
                            'collector_id' => $collector->id,
                            'collector_name' => $collector->first_name . ' ' . $collector->last_name,
                            'collector_image' => getSingleMedia(optional($collector),'profile_image', null),
                            'email' => $collector->email,
                            'mobile' => $collector->mobile,
                            'total_appointments' => $collector->total_appointments ?? 0,
                            'total_earning' => round($collector->total_service_amount ?? 0, $noOfDecimal),
                            'total_admin_earnings' => round($collector->total_admin_earnings ?? 0, $noOfDecimal),
                            'total_vendor_earnings' => round($collector->total_vendor_earnings ?? 0, $noOfDecimal),
                            'collector_pay_due' => round($collector->total_commission_amount ?? 0, $noOfDecimal),
                            'collector_paid_earnings' => round($collector->collector_paid_earnings ?? 0, $noOfDecimal),
                        ];
                    });
                }
                
                return response()->json($collector_list);
            }
    }    


    public function vendorEarning(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = __('messages.list');

        $module_title = __('messages.vendor_earnings');
        return view('earning::backend.vendor_earning', compact('module_action', 'filter','module_title'));
    }

    public function vendor_index_data(Datatables $datatable, Request $request)
    {
        

        $query = User::SetRole(auth()->user())->withCommissionData('vendor');

        return $datatable->eloquent($query)
            ->addColumn('action', function ($data) {
                return $data->total_commission_amount > 0
                    ? '<a href=' . route('backend.payouts.vendor_create', ['id' => $data->id]) . '><i class="fas fa-money-bill-alt earning-icon"></i></a>'
                    : '-';
            })
            ->editColumn('user_id', fn($data) => view('user::backend.users.user_details', compact('data')))
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', "%{$keyword}%")
                        ->orWhere('last_name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%");
                }
            })
            ->orderColumn('user_id', fn($query, $order) => $query->orderBy('first_name', $order)->orderBy('last_name', $order), 1)
            

            ->editColumn('total_appointment', function ($data) {
                $title = __('messages.view_vendor_appointments');
                return $data->total_appointments > 0
                    ? "<b><a href='" . route('backend.appointments.index', ['vendor_id' => encrypt($data->id)]) . "' class='text-primary text-nowrap px-1' title='{$title}'>{$data->total_appointments}</a></b>"
                    : "<b><span class='text-primary text-nowrap px-1' title='{$title}'>0</span></b>";
            })
            ->editColumn('total_service_amount', fn($data) => Currency::format($data->total_service_amount))
            
            ->editColumn('total_admin_earning', fn($data) => Currency::format($data->total_admin_earnings))
            ->editColumn('vendor_pay_due', fn($data) => Currency::format($data->total_commission_amount))
         
            ->editColumn('vendor_paid_earning', function ($data) {
                return "<b><a href='" . route('backend.payouts.vendor_index', $data->id) . "' class='text-primary text-nowrap px-1' title='View Vendor Payout'>" . Currency::format($data->vendor_paid_earnings) . "</a></b>";
            })
            ->editColumn('total_collector_earning', fn($data) => Currency::format($data->total_collector_earnings))
            ->addIndexColumn()
            ->rawColumns(['action', 'image','user_id','total_commission_earn','total_appointment','vendor_paid_earning'])
            ->toJson();
    }

   
}
