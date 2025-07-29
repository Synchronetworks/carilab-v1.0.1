<?php

namespace Modules\Report\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Trait\ModuleTrait;
use App\Models\User;
use Modules\Appointment\Models\Appointment;
use Currency;
use Modules\Subscriptions\Models\Subscription;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Commision\Models\CommissionEarning;
use Modules\Subscriptions\Models\SubscriptionTransactions;
use App\Models\Setting;
use Modules\CatlogManagement\Models\CatlogManagement;

class ReportsController extends Controller
{
    protected string $exportClass = '\App\Exports\ReportExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct(Request $request)
    {
        $routeName = $request->route()->getName();

        if ($routeName === 'backend.reports.earning_report_export') {
            $this->exportClass = '\App\Exports\ReportExport';
        } elseif ($routeName === 'backend.reports.subscription_report_export') {
            $this->exportClass = '\App\Exports\VendorSubscriptionExport';
        } elseif ($routeName === 'backend.reports.top_testcase_report_export') {
            $this->exportClass = '\App\Exports\TopTestCaseBookedExport';
        } else {
            $this->exportClass = '\App\Exports\ReportExport'; // Fallback
        }
        $this->traitInitializeModuleTrait(
            __('messages.top_test_case'),
            'Top Booked Test Case', // module name
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
        $module_title = __('messages.earning_report');
        $module_name = __('messages.earning_report');
        $userid = $request->user_id ?? auth()->id();
        $user = User::find($userid);
        $module_action = __('messages.earning_list');
        $commissionearning = new CommissionEarning();
        $totalrevenue = $commissionearning->rolewiseCommission($user,'paid');
        $subscription_transaction = new SubscriptionTransactions();
        $totalsubscriptionrevenue = $subscription_transaction->where('payment_status', 'paid')->sum('amount');

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),


            ],
            [
                'value' => 'total_appointment',
                'text' => __('messages.total_appointment'),


            ],
            [
                'value' => 'total_service_amount',
                'text' => __('messages.lbl_total_amount'),


            ],
            [
                'value' => 'total_admin_earning',
                'text' => __('messages.lbl_admin_earnings'),


            ],
            [
                'value' => 'total_collector_earning',
                'text' => __('messages.lbl_collector_earnings'),

            ],
            [
                'value' => 'total_tax',
                'text' => __('messages.tax'),
            ],

        ];
        if (multivendor() == 1 && auth()->user()->hasRole(['admin', 'demo_admin'])) {
            $export_columns[] = [
                'value' => 'total_vendor_earning',
                'text' => __('messages.lbl_vendor_earnings'),

            ];
        }
        $export_url = route('backend.reports.earning_report_export');

        return view('report::backend.index', compact('module_action', 'filter', 'export_import', 'module_title', 'export_columns', 'export_url', 'totalrevenue', 'totalsubscriptionrevenue','module_name'));
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        
        $query = User::MyVendor()->SetRole(auth()->user())->withCommissionData('vendor');

        return $datatable->eloquent($query)
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
            return $data->total_appointments > 0
                ? "<b><a href='" . route('backend.appointments.index', ['vendor_id' => encrypt($data->id)]) . "' class='text-primary text-nowrap px-1' title='View Collector Appointments'>{$data->total_appointments}</a></b>"
                : "<b><span class='text-primary text-nowrap px-1' title='View Collector Appointments'>0</span></b>";
        })
        ->editColumn('total_service_amount', fn($data) => Currency::format($data->total_service_amount))

        ->editColumn('total_admin_earning', fn($data) => Currency::format($data->total_admin_earnings))

        ->editColumn('total_vendor_earning', fn($data) => Currency::format($data->total_vendor_earnings))
        ->editColumn('total_collector_earning', fn($data) => Currency::format($data->total_collector_earnings))
        ->editColumn('total_tax', fn($data) => Currency::format($data->total_tax_amount))
            ->addIndexColumn()
            ->rawColumns(['action', 'image', 'user_id', 'total_commission_earn', 'total_appointment'])
            ->toJson();
    }

    public function vendor_subscription(Request $request)
    {
        
        $filter = [
            'status' => $request->status,
        ];
        $userid = $request->user_id ?? auth()->id();
        $user = User::find($userid);
        $module_action = 'List';
        $module_name = 'Vendor Subscription Report';
        $subscription = new Subscription();
        $totalsubscription = $subscription->distinct('user_id')->count();
        $totalActivesubscription = $subscription->where('status', 'active')->count();
        $totalExpiredsubscription = $subscription->where('status', 'inactive')->count();
        $totalExpiredSoonsubscription = $subscription->whereBetween('end_date', [now(), now()->addDays(7)])->count();


        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ],
            [
                'value' => 'plan',
                'text' => __('messages.lbl_plan'),
            ],
            [
                'value' => 'duration',
                'text' => __('messages.lbl_duration'),
            ],
            [
                'value' => 'total_amount',
                'text' => __('messages.lbl_total_amount'),
            ],
            [
                'value' => 'start_date',
                'text' => __('messages.lbl_start_date'),
            ],
            [
                'value' => 'end_date',
                'text' => __('messages.lbl_end_date'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
            
        ];
        $export_url = route('backend.reports.subscription_report_export');
        $module_title = __('Vendor Subscription');
        return view('report::backend.vendor_subscription', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url', 'totalActivesubscription', 'totalsubscription', 'totalExpiredsubscription', 'totalExpiredSoonsubscription', 'module_title','module_name'));
    }
    public function vendor_subscription_data(Datatables $datatable, Request $request)
    {
        $query = Subscription::query()
            ->with('user', 'subscription_transaction', 'plan');

        return $datatable->eloquent($query)
            ->addColumn('action', function ($data) {

                return '-';
            })

            ->editColumn('user_id', function ($data) {
                if (!$data->user) {
                    return '<span class="text-danger">Deleted User</span>';
                }
                $data = $data->user;
                return view('user::backend.users.user_details', compact('data'));
            })
            ->filterColumn('user_id', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('user', function ($q) use ($keyword) {
                        $q->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })

            ->editColumn('plan', function ($data) {
                return $data->plan->name ?? '-';
            })
            ->filterColumn('plan', function ($query, $keyword) {
                $query->whereHas('plan', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('plan', function ($query, $direction) {
                $query->select('subscriptions.*')
                    ->leftJoin('plan', 'subscriptions.plan_id', '=', 'plan.id')
                    ->orderByRaw('COALESCE(plan.name, "") ' . $direction);
            })
            ->editColumn('duration', function ($data) {
                return $data->duration . ' ' . ucfirst($data->type);
            })
           
            ->filterColumn('duration', function ($query, $keyword) {
                $query->whereHas('plan', function ($q) use ($keyword) {
                    $q->where('duration', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('duration', function ($query, $direction) {
                $query->select('subscriptions.*')
                    ->orderByRaw("
                        CASE subscriptions.type 
                            WHEN 'week' THEN 1 
                            WHEN 'month' THEN 2 
                            WHEN 'year' THEN 3 
                            ELSE 4 
                        END {$direction}, 
                        subscriptions.duration {$direction}
                    ");
            })
            ->editColumn('total_amount', function ($data) {
                return \Currency::format($data->subscription_transaction->amount) ?? '-';
            })
            ->filterColumn('total_amount', function ($query, $keyword) {
                $query->whereHas('subscription_transaction', function ($q) use ($keyword) {
                    $q->where('amount', 'like', "%$keyword%");
                });
            })
            ->orderColumn('total_amount', function ($query, $direction) {
                $query->join('subscription_transactions', 'model_name.subscription_transaction_id', '=', 'subscription_transactions.id')
                    ->orderByRaw('subscription_transactions.amount * 1 ' . $direction);
            })
            ->editColumn('start_date', function ($data) {
                return Setting::formatDate($data->start_date) ?? '-';
            })
            ->editColumn('end_date', function ($data) {
                return Setting::formatDate($data->end_date) ?? '-';
            })
            
            // Filter and Order for start_date
            ->filterColumn('start_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(start_date, '%Y-%m-%d') LIKE ?", ["%{$keyword}%"]);
            })
            ->orderColumn('start_date', function ($query, $direction) {
                $query->orderBy('start_date', $direction);
            })

            // Filter and Order for end_date
            ->filterColumn('end_date', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(end_date, '%Y-%m-%d') LIKE ?", ["%{$keyword}%"]);
            })
            ->orderColumn('end_date', function ($query, $direction) {
                $query->orderBy('end_date', $direction);
            })
            ->editColumn('status', function ($data) {
                if (!$data->user || $data->user->deleted_at) {
                    return 'cancelled';
                }
                return $data->status == 1 || $data->status == 'active' ? 'active' : 'inactive';
            })
            // Filter and Order for status
            ->filterColumn('status', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    if (stripos('active', $keyword) !== false) {
                        $q->orWhere('status', 1);
                    }
                    if (stripos('inactive', $keyword) !== false) {
                        $q->orWhere('status', 0);
                    }
                });
            })
            ->orderColumn('status', function ($query, $direction) {
                $query->orderBy('status', $direction);
            })


            ->addIndexColumn()
            ->rawColumns(['action', 'user_id', 'status','duration','plan'])
            ->orderColumns(['total_amount'], ':column $1')
            ->toJson();
    }
    public function top_testcase_booked(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_title=__('messages.top_test_case');
        $export_import = true;
        $export_columns = [
            [
                'value' => 'test_case',
                'text' => __('messages.lbl_test_case'),
            ],
            [
                'value' => 'test_category',
                'text' => __('messages.lbl_test_category'),
            ],
            [
                'value' => 'booking_count',
                'text' => __('messages.lbl_booking_counts'),
            ],
            [
                'value' => 'booking_percentage',
                'text' => __('messages.lbl_booking_percentage'),
            ],
            [
                'value' => 'last_booking_date',
                'text' => __('messages.lbl_last_booking_date'),
            ],

        ];
        $export_url = route('backend.reports.top_testcase_report_export');

        return view('report::backend.top_test_case_booked', compact('filter', 'export_import', 'export_columns', 'export_url','module_title'));
    }
    public function top_testcase_booked_data(Datatables $datatable, Request $request)
{
    $user = auth()->user();

        
        if ($user->user_type == 'vendor') {
            $query = Appointment::selectRaw('appointments.test_id, appointments.test_type, COUNT(*) as booking_count, MAX(appointments.created_at) as last_booking_date')
            ->whereIn('appointments.test_type', ['test_case', 'test_package'])
            ->whereNull('appointments.deleted_at') 
            ->where('appointments.vendor_id', $user->id)  // Specify the table name
            ->where(function ($q) {
                $q->whereHas('catlog', function ($subQuery) {
                    $subQuery->whereNull('deleted_at'); 
                })->orWhereHas('package', function ($subQuery) {
                    $subQuery->whereNull('deleted_at'); 
                });
            })
            ->groupBy('appointments.test_id', 'appointments.test_type')  // Specify the table name
            ->with([
                'catlog' => function ($query) {
                    $query->whereNull('deleted_at')->with('category:id,name'); 
                },
                'package' => function ($query) {
                    $query->whereNull('deleted_at'); 
                }
            ]);
        }
        else{
            $query = Appointment::selectRaw('test_id, test_type, COUNT(*) as booking_count, MAX(appointments.created_at) as last_booking_date')
            ->whereIn('test_type', ['test_case', 'test_package'])
            ->whereNull('appointments.deleted_at') 
            ->where(function ($q) {
                $q->whereHas('catlog', function ($subQuery) {
                    $subQuery->whereNull('deleted_at'); 
                })->orWhereHas('package', function ($subQuery) {
                    $subQuery->whereNull('deleted_at'); 
                });
            })
            ->groupBy('test_id', 'test_type')
            ->with([
                'catlog' => function ($query) {
                    $query->whereNull('deleted_at')->with('category:id,name'); 
                },
                'package' => function ($query) {
                    $query->whereNull('deleted_at'); 
                }
            ]);
        }
  
    

    return $datatable->eloquent($query)
        ->addColumn('action', function ($data) {
            return '-';
        })
        ->editColumn('test_case', function ($data) {
            return $data->test_type == 'test_case' 
                ? optional($data->catlog)->name 
                : optional($data->package)->name ?? '-';
        })
        ->filterColumn('test_case', function ($query, $keyword) {
            $query->whereHas('catlog', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            })->orWhereHas('package', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            });
        })
        ->orderColumn('test_case', function ($query, $direction) {
            $query->leftJoin('catlogmanagements', function ($join) {
                $join->on('appointments.test_id', '=', 'catlogmanagements.id')
                    ->where('appointments.test_type', 'test_case');
            })->leftJoin('packagemanagements', function ($join) {
                $join->on('appointments.test_id', '=', 'packagemanagements.id')
                    ->where('appointments.test_type', 'test_package');
            })->orderByRaw("COALESCE(catlogmanagements.name, packagemanagements.name) {$direction}");
        })
        ->editColumn('test_category', function ($data) {
            return $data->test_type == 'test_case' 
                ? optional($data->catlog->category)->name ?? '-' 
                : '-';
        })
        ->filterColumn('test_category', function ($query, $keyword) {
            $query->whereHas('catlog.category', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            });
        })
        ->orderColumn('test_category', function ($query, $direction) {
            $query->leftJoin('categories', 'catlogmanagements.category_id', '=', 'categories.id')
                ->orderBy('categories.name', $direction);
        })
        ->addColumn('appointments_count', function ($data) {
            return $data->booking_count ?? 0;
        })
        ->orderColumn('appointments_count', function ($query, $direction) {
            $query->orderBy('booking_count', $direction);
        })
        ->editColumn('booking_percentage', function ($data) {
            $totalBookings = Appointment::count();
            $bookingPercentage = ($totalBookings > 0) ? ($data->booking_count / $totalBookings) * 100 : 0;
            return number_format($bookingPercentage, 2) . '%';
        })
        ->orderColumn('booking_percentage', function ($query, $direction) {
            $totalBookings = Appointment::count();
            if ($totalBookings > 0) {
                $query->orderByRaw('(COUNT(appointments.id) / ?) * 100 ' . $direction, [$totalBookings]);
            }
        })
        ->editColumn('last_booking_date', function ($data) {
            return $data->last_booking_date 
                ? Setting::formatDate($data->last_booking_date)
                : '-';
        })
        ->filterColumn('last_booking_date', function ($query, $keyword) {
            $query->whereRaw("MAX(appointments.created_at) LIKE ?", ["%{$keyword}%"]);
        })
        ->orderColumn('last_booking_date', function ($query, $direction) {
            $query->orderByRaw("MAX(appointments.created_at) {$direction}");
        })
        ->addIndexColumn()
        ->rawColumns(['action', 'appointments_count', 'booking_percentage'])
        ->toJson();
}

}
