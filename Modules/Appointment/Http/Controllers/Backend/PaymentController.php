<?php

namespace Modules\Appointment\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Appointment\Models\AppointmentTransaction;
use App\Trait\ModuleTrait;
use Currency;
use App\Models\Setting;
use Modules\Appointment\Models\CashPaymentHistories;
use Modules\Appointment\Models\Appointment;
use App\Models\User;
use Modules\Commision\Models\CommissionEarning;
use Modules\Payout\Models\Payout;

class PaymentController extends Controller
{
    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'payment.title', // module title
            'payments', // module name
            'fa-solid fa-clipboard-list' // module icon
        );
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
       
        $module_action = __('messages.list');
        $module_title=__('messages.payment_list');
      
        $export_import = true;
        $export_columns = [
            [
                'value' => 'id',
                'text' => __('messages.id'),
            ],
            [
                'value'=>'test',
                'text'=>__('messages.lbl_test_case'),
            ],
            [
                'value' => 'customer',
                'text' => __('messages.customer'),
            ],
            [
                'value' => 'datetime',
                'text' => __('messages.datetime'),
            ],
            [
                'value' => 'payment_status',
                'text' => __('messages.lbl_payment_status'),
            ],
            [
                'value' => 'payment_type',
                'text' => __('messages.payment_type'),
            ],
            [
                'value'=>'total_amount',
                'text'=>__('messages.lbl_total_amount'),
            ],
        ];
        $export_url = route('backend.appointments.export');
        return view('appointment::backend.payment.payment_list',compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url','module_title'));
    }

    public function cash_payment_list(Request $request,$payment_type)
    {
   
        $filter = [
            'status' => $request->status,
        ];

        if ($payment_type=='cash') {
            $module_title=__('messages.cash_payment_list');
        }else{
            $module_title=__('messages.payment_list');
        }
     
        $module_action = 'List';

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ]
        ];
        $export_url = route('backend.appointments.export');
        return view('appointment::backend.payment.payment_list',compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url','payment_type','module_title'));
    }
    public function index_data(Datatables $datatable, Request $request){
        $query = AppointmentTransaction::query()->myPayment(); 

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('payment_status', $filter['column_status']);
            }
        }
        if($request->payment_type == 'cash'){
            $query = $query->where('payment_type', $request->payment_type);
        }else{
            $query = $query->where(function ($q) {
                $q->whereNotIn('payment_type', ['cash'])
                  ->orWhereNull('payment_type');
            });
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" onclick="dataTableRowCheck('.$row->id.')">';
            })
        ->editColumn('id', function($query) {

            return "<a class='btn-link btn-link-hover' href=" .route('backend.appointments.details', $query->appointment_id).">#".$query->appointment_id."</a>"; 
        })
        
        ->orderColumn('id', function($query, $order) {
            $query->orderBy('appointment_transaction.appointment_id', $order);  
        })
        ->editColumn('appointment_id', function ($query) {
            if (!empty($query->appointment) && $query->appointment->test_type == 'test_package') {
                $test_case = optional($query->appointment->package)->name ?? '-';
            } elseif (!empty($query->appointment) && $query->appointment->test_type == 'test_case') {
                $test_case = optional($query->appointment->catlog)->name ?? '-';
            }
        
            return $test_case ?? '-';
        })        
        ->filterColumn('appointment_id', function ($query, $keyword) {
            $query->whereHas('appointment', function ($subQuery) use ($keyword) {
                $subQuery->whereHas('package', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                })->orWhereHas('catlog', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            });
        })
        ->orderColumn('appointment_id', function ($query, $order) {
            $query->join('appointments', 'appointments.id', '=', 'appointment_transaction.appointment_id')
                  ->leftJoin('packagemanagements', 'packagemanagements.id', '=', 'appointments.test_id')
                  ->leftJoin('catlogmanagements', 'catlogmanagements.id', '=', 'appointments.test_id')
                  ->orderByRaw("
                      CASE 
                          WHEN appointments.test_type = 'test_package' THEN packagemanagements.name 
                          ELSE catlogmanagements.name 
                      END $order
                  ");
        })
        ->editColumn('customer_id', function ($data) {
            $data = $data->appointment->customer ?? null;
            if ($data !== null){
                return view('user::backend.users.user_details', compact('data'));
            }else {
                return '-';
            }
           
        })
        ->filterColumn('customer_id',function($query,$keyword){
            $query->whereHas('appointment',function ($qry) use($keyword){
                $qry->whereHas('customer',function ($q) use($keyword){
                    $q->where('first_name','like','%'.$keyword.'%')->orwhere('last_name','like','%'.$keyword.'%');
                });
            });
        })
        ->orderColumn('customer_id', function ($query, $order) {
            $query->join('appointments', 'appointments.id', '=', 'appointment_transaction.appointment_id')
                  ->join('users as customers', 'customers.id', '=', 'appointments.customer_id')
                  ->orderByRaw("CONCAT(customers.first_name, ' ', customers.last_name) $order");
        })
        
        ->editColumn('datetime' , function ($query){
            $dateTime = Setting::timeZone($query->updated_at);
            $date = Setting::formatDate($dateTime) . ' ' . Setting::formatTime($dateTime);
        
            return $date;
        })
        ->editColumn('payment_type', function($query) {
            $paymentType = empty($query->payment_type) ? '-' : ucfirst($query->payment_type);
            return '<div class="text-center">' . $paymentType . '</div>';
        })            
        ->editColumn('payment_status', function($query) {
            $payment = $query->payment_status;
            if($payment !== null){
                $payment_status = '<span class="text-center text-white badge bg-primary">'.str_replace('_'," ",ucfirst($payment)).'</span>';
            }else{
                $payment_status = '<span class="text-center d-block">-</span>';
            }
            return $payment_status;
        })
        ->editColumn('cash_history', function($payment) {
            $action = '<a class="btn-link btn-link-hover" href="'.route('backend.payments.cash_history', $payment->id).'">'.__('messages.view').'</a>';
            return $action;
        })

        ->editColumn('total_amount', function($query) {
            return '<div class="text-end">' . Currency::format($query->total_amount) . '</div>';
        })
        ->addColumn('action', function($data){
            return view('appointment::backend.payment.action',compact('data'))->render();
        })
        ->addIndexColumn()
        ->rawColumns(['action','check','payment_status','payment_type','id','cash_history','total_amount'])
        ->toJson();
    }


    public function cashHistory($id, Request $request)
    {
        
        $filter = [
            'status' => $request->status,
        ];
       
        $module_action = __('messages.list');

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ]
        ];
        $export_url = route('backend.appointments.export');
        $module_title = __('messages.cash_payment_history');
        return view('appointment::backend.payment.cash_payment_history', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url','id','module_title'));
    }

     public function cash_index_data(DataTables $datatable, $id)
    {
        $query = CashPaymentHistories::where('transaction_id', $id);

        
        if (auth()->user()->hasRole('vendor')) {
            $query->whereHas('appointment', function ($q) {
                $q->where('vendor_id', auth()->id());
            });
        }
        return $datatable->eloquent($query)
            ->editColumn('sender_id', function($payment) {
                return ($payment->sender != null && isset($payment->sender)) 
                    ? $payment->sender->first_name . ' ' . $payment->sender->last_name 
                    : '-';
            })
            ->filterColumn('sender_id', function ($query, $keyword) {
                $query->whereHas('sender', function ($q) use ($keyword) {
                    $q->where(function($subQuery) use ($keyword) {
                        $subQuery->where('first_name', 'like', '%' . $keyword . '%')
                                ->orWhere('last_name', 'like', '%' . $keyword . '%')
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $keyword . '%']);
                    });
                });
            })
            ->editColumn('receiver_id', function($payment) {
                return ($payment->receiver != null && isset($payment->receiver)) 
                    ? $payment->receiver->first_name . ' ' . $payment->receiver->last_name 
                    : '-';
            })
            ->filterColumn('receiver_id', function ($query, $keyword) {
                $query->whereHas('receiver', function ($q) use ($keyword) {
                    $q->where(function($subQuery) use ($keyword) {
                        $subQuery->where('first_name', 'like', '%' . $keyword . '%')
                                ->orWhere('last_name', 'like', '%' . $keyword . '%')
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) like ?", ['%' . $keyword . '%']);
                    });
                });
            })
            ->editColumn('datetime', function ($query) {
                $dateTime = Setting::timeZone($query->datetime);
                $date = Setting::formatDate($dateTime).' '.Setting::formatTime($dateTime);
                return $date;
            })
            ->addIndexColumn()
            ->toJson();
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('appointment::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('appointment::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('appointment::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = AppointmentTransaction::findOrFail($id);
        $data->delete();
        $this->logActivity('delete',$data,'appointment_transaction_delete');
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = AppointmentTransaction::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'appointment_transaction_restore');
        $message = __('messages.restore_form');
        return response()->json(['message' => $message]);
        
    }

    public function forceDelete($id)
    {
        $data = AppointmentTransaction::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'appointment_transaction_force_delete');
        $message = __('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }

    public function cashApprove($id,Request $request)
    {

        $paymentdata = AppointmentTransaction::where('id',$id)->first();
        $parent_payment_history = CashPaymentHistories::where('transaction_id', $id)->orderBy('id', 'desc')->first();
        $appointment = Appointment::where('id', $paymentdata['appointment_id'])->first();
        $vendor_id = $appointment->vendor_id;
        $user = User::find($vendor_id);
        Setting::getSettings('default_time_zone') ?? 'UTC';
        $payment_history['datetime'] = date('Y-m-d H:i:s');
        if($parent_payment_history->status == 'send_to_vendor' && $appointment->vendor_id == auth()->id() && $user->user_type == 'vendor'){
            $total_amount = $parent_payment_history->total_amount;
            
            $payment_history = [
                'transaction_id' => $id,
                'appointment_id' => $paymentdata->appointment_id,
                'action' => config('constant.PAYMENT_HISTORY_ACTION.VENDOR_APPROVED_CASH'),
                'type' => $parent_payment_history->type,
                'sender_id' => $parent_payment_history->sender_id,
                'receiver_id' => auth()->id() ?? '',
                'total_amount' => $total_amount ?? 0,
                'text' =>  __('messages.cash_approved',['amount' => Currency::format((float)$total_amount),'name' => get_user_name(auth()->id())]),
                'status' => config('constant.PAYMENT_HISTORY_STATUS.APPROVED_VENDOR'),
                'parent_id' => $parent_payment_history->parent_id
            ];
            
        }elseif($parent_payment_history->status == 'approved_by_vendor' && $appointment->vendor_id == auth()->id() && $user->user_type == 'vendor'){
            $commission_earning = CommissionEarning::where('commissionable_id',$paymentdata->appointment_id)->where('commission_status','pending')->where('employee_id',auth()->id())->first();
            $commissionamount = $commission_earning->commission_amount ?? 0;
            $total_amount = $parent_payment_history->total_amount - $commissionamount;

           
            if ($commission_earning && $commission_earning->commission_status == "pending") {
                         
                $payouts = [
                    'user_id' =>  $commission_earning->employee_id ?? null,
                    'user_type' => $commission_earning->user_type ?? null,
                    'payment_method' => 'cash',
                    'bank' => null,
                    'description' => __('messages.payment_transfer',['from' => get_user_name(auth()->id()),'to' => get_user_name(admin_id()),'amount' => Currency::format((float)$total_amount) ]) ?? null,
                    'paid_date' => $payment_history['datetime'] ?? now(),
                    'amount' => $commission_earning->commission_amount ?? 0, // Now it works
                ];
            
                $payout = Payout::create($payouts);
                $commission_earning->update(['commission_status' => 'paid']); // Update status
                                
            }
            
            $payment_history = [
                'transaction_id' => $id,
                'appointment_id' => $paymentdata->appointment_id,
                'action' => config('constant.PAYMENT_HISTORY_ACTION.VENDOR_SEND_ADMIN'),
                'type' => $parent_payment_history->type,
                'sender_id' => auth()->id() ?? '',
                'receiver_id' => admin_id(),
                'total_amount' => $total_amount ?? 0,
                'text' =>   __('messages.payment_transfer',['from' => get_user_name(auth()->id()),'to' => get_user_name(admin_id()),'amount' => Currency::format((float)$total_amount) ]),
                'status' => config('constant.PAYMENT_HISTORY_STATUS.SEND_ADMIN'),
                'parent_id' => $parent_payment_history->parent_id
            ];
            
        }elseif($parent_payment_history->status == 'send_to_admin' && auth()->user()->hasRole ('admin')){
            $payment_history = [
                'transaction_id' => $id,
                'appointment_id' => $paymentdata->appointment_id,
                'action' => config('constant.PAYMENT_HISTORY_ACTION.ADMIN_APPROVED_CASH'),
                'type' => $parent_payment_history->type,
                'sender_id' => $parent_payment_history->sender_id,
                'receiver_id' => auth()->id(),
                'total_amount' => $parent_payment_history->total_amount,
                'text' =>  __('messages.cash_approved',['amount' => Currency::format((float)$parent_payment_history->total_amount),'name' => get_user_name(admin_id())]),
                'status' => config('constant.PAYMENT_HISTORY_STATUS.APPROVED_ADMIN'),
                'parent_id' => $parent_payment_history->parent_id
            ];
        }
        

     
        
        
        $res = CashPaymentHistories::updateOrCreate(
            [
                'appointment_id' => $payment_history['appointment_id'], 
                'action' => $payment_history['action'], 
                'status' => $payment_history['status']
            ],
            [
                'transaction_id' => $payment_history['transaction_id'],
                'type' => $payment_history['type'],
                'status' => $payment_history['status'],
                'sender_id' => $payment_history['sender_id'],
                'receiver_id' => $payment_history['receiver_id'],
                'datetime' => $payment_history['datetime'] ?? now(),
                'total_amount' => $payment_history['total_amount'] ?? 0,
                'type' => $payment_history['payment_type'] ?? 'cash',
                'parent_id' => $payment_history['parent_id'] ?? CashPaymentHistories::where('appointment_id', $payment_history['appointment_id'])->value('id'),
                'text' => $payment_history['text'] ?? null,

            ]
        );
        
        $paymentdata->payment_status = $payment_history['status'];
        $paymentdata->update();
        
        if($payment_history['action'] == config('constant.PAYMENT_HISTORY_ACTION.ADMIN_APPROVED_CASH')){
            $paymentdata->payment_status = 'paid';
            $paymentdata->update();
        }
        if($appointment->status == 'completed' && $paymentdata->payment_status == "paid"){
            $commission_earning = CommissionEarning::where('commissionable_id',$paymentdata->appointment_id)->where('employee_id',auth()->id())->first();
            if($commission_earning){
                $payouts = [
                    'user_id' =>  $commission_earning->employee_id ?? null,
                    'user_type' => $commission_earning->user_type ?? null,
                    'payment_method' => 'cash',
                    'bank' => null,
                    'description' => $payment_history['text'] ?? null,
                    'paid_date' => $payment_history['datetime'] ?? now(),
                    'amount' => $commission_earning->commission_amount ?? 0, 
                ];
            
                $payout = Payout::create($payouts);
                $commission_earning->update(['commission_status' => 'paid']); 
            }
        }

        $msg = __('messages.approve_successfully');
        return redirect()->back()->withSuccess($msg);
    }
}
