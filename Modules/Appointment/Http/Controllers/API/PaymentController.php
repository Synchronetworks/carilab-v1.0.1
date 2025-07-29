<?php

namespace Modules\Appointment\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Trait\NotificationTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Appointment\Models\Appointment;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Commision\Models\CommissionEarning;
use App\Models\User;
use Currency;
use Modules\Appointment\Models\CashPaymentHistories;
use Modules\Appointment\Transformers\PaymentResource;
use Modules\Appointment\Transformers\CashPaymentHistoryResource;
use DB;
use Modules\Appointment\Transformers\AppointmentDetailResource;
use App\Models\Setting;
use Modules\Payout\Models\Payout;

class PaymentController extends Controller
{
    use AppointmentTrait;
    use NotificationTrait;
    public function savePayment(Request $request)
    {
        $data = $request->all();
        $appointment = Appointment::with('appointmentCollectorMapping','transactions')->where('id',$data['id'])->first();
        if($appointment == null){
            $messages = __('messages.appointment_not_found');
            $status_code = 404;
            $response = [
                'message' => $messages,
                'appointment_id' => $data['id'],
            ];
            return comman_custom_response($response,$status_code);
        }
        $transactions = AppointmentTransaction::where('appointment_id',$data['id'])->first();
        if($transactions){
            $transaction = $transactions->update($data);
            $transactions = AppointmentTransaction::where('appointment_id', $data['id'])->first();

            $collector_id = optional($appointment->appointmentCollectorMapping)->collector_id;
            $assignedUserData = User::find($collector_id);
            Setting::getSettings('default_time_zone') ?? 'UTC';
            $appointment['datetime'] = date('Y-m-d H:i:s');
            if ($collector_id !== null && in_array($assignedUserData->user_type, ['admin', 'vendor', 'demo_admin'])) {
                if(in_array($assignedUserData->user_type, ['admin','demo_admin'])){
                   
                    $payment_history = [
                        'transaction_id' => $transactions->id,
                        'appointment_id' => $transactions->appointment_id,
                        'parent_id' => $request->parent_id ?? null,
                        'action' => config('constant.PAYMENT_HISTORY_ACTION.ADMIN_APPROVED_CASH'),
                        'status' => config('constant.PAYMENT_HISTORY_STATUS.APPROVED_ADMIN'),
                        'sender_id' => $appointment->customer_id,
                        'receiver_id' => $collector_id,
                        'datetime' => $appointment->datetime ?? now(),
                        'total_amount' => $appointment->total_amount,
                        'type' => 'cash',
                        'text'     =>   __('messages.cash_approved',['amount' => Currency::format((float)$appointment->total_amount),'name' => get_user_name($collector_id)]),
                    ];
                    $res =  CashPaymentHistories::create($payment_history);

                    if($payment_history['action'] == config('constant.PAYMENT_HISTORY_ACTION.ADMIN_APPROVED_CASH')){
                        $transactions->payment_status = 'paid';
                        $transactions->payment_type = 'cash';
                        $transactions->txn_id = '#'.$transactions->appointment_id;
                        $transactions->update();
                    }
                }elseif(in_array($assignedUserData->user_type, ['vendor'])){
                   
                    $payment_history = [
                        'transaction_id' => $transactions->id,
                        'appointment_id' => $transactions->appointment_id,
                        'parent_id' => $request->parent_id ?? null,
                        'action' => config('constant.PAYMENT_HISTORY_ACTION.VENDOR_APPROVED_CASH'),
                        'status' => config('constant.PAYMENT_HISTORY_STATUS.APPROVED_VENDOR'),
                        'sender_id' => $appointment->customer_id,
                        'receiver_id' => $collector_id,
                        'datetime' => $appointment->datetime ?? now(),
                        'total_amount' => $appointment->total_amount,
                        'type' => 'cash',
                        'text'     =>   __('messages.cash_approved',['amount' => Currency::format((float)$appointment->total_amount),'name' => get_user_name($collector_id)]),
                    ];
                    $res =  CashPaymentHistories::create($payment_history);

                    if($payment_history['action'] == config('constant.PAYMENT_HISTORY_ACTION.VENDOR_APPROVED_CASH')){
                        $transactions->payment_status = $payment_history['status'];
                        $transactions->payment_type = 'cash';
                        $transactions->txn_id = '#'.$transactions->appointment_id;
                        $transactions->update();
                    }
                    if($appointment->status == 'completed' && $transactions->payment_status==$payment_history['status'])
                    {
                        $vendor_id = $appointment['vendor_id'] ?? null;
                        if($vendor_id){
                            $vendor = User::find($vendor_id);
                            $this->commissionDistribute($vendor, $appointment, $transactions);
                        }
                    }
                }
            }            
            if($collector_id != null && $assignedUserData->user_type == 'collector' && $request->payment_type == 'cash'){
                $payment_history = [
                    'transaction_id' => $transactions->id,
                    'appointment_id' => $transactions->appointment_id,
                    'parent_id' => $request->parent_id ?? null,
                    'action' => config('constant.PAYMENT_HISTORY_ACTION.CUSTOMER_SEND_COLLECTOR'),
                    'status' => config('constant.PAYMENT_HISTORY_STATUS.PENDING_COLLECTOR'),
                    'sender_id' => $request->customer_id ?? $appointment->customer_id,
                    'receiver_id' => $collector_id,
                    'datetime' => $request->datetime ?? $appointment->datetime,
                    'total_amount' => $request->total_amount,
                    'type' => $request->payment_type,
                    'text'     =>    __('messages.payment_transfer',['from' => get_user_name( $request->customer_id),'to' => get_user_name($collector_id),'amount' => Currency::format((float)$request->total_amount) ]),
                ];
                $res =  CashPaymentHistories::create($payment_history);
                

                

                if($appointment->status == 'completed' && $transactions->payment_status=='pending')
                {
                    $vendor_id = $appointment['vendor_id'] ?? null;
                    if($vendor_id){
                        $vendor = User::find($vendor_id);
                        $this->commissionDistribute($vendor, $appointment, $transactions);
                    }
                }
            }
            if($transactions->payment_status == 'failed')
            {
                $message = __('messages.payment_failed');
                $status_code = 400;
                return comman_custom_response($message,$status_code);
            }
            
            if($appointment->status == 'completed' && $transactions->payment_status=='paid')
            {
            
                $vendor_id = $appointment['vendor_id'] ?? null;
                    if($vendor_id){
                        $vendor = User::find($vendor_id);
                       $this->commissionDistribute($vendor, $appointment, $transactions);
                    }
            }
        }else {
            
            $message = __('messages.transaction_not_found');
            $status_code = 404;
            return comman_custom_response($message, $status_code);
        }
       
        $status_code = 200;
        $message = __('messages.payment_completed');
        $activity_data = [
            'activity_type' => 'payment_message_status',
            'notification_type' => 'payment_completed',
            'payment_status'=>  $data['payment_status'] ?? $transactions->payment_status,
            'appointment_id' => $appointment->id,
            'appointment' => $appointment,
            'appointment_amount' => $appointment->total_amount,
        ];
        
        $responseData = new AppointmentDetailResource($appointment);
        $response = [
            'message' => $message,
            'appointment_id' => $appointment->id,
            'data' => $responseData,
        ];
        return comman_custom_response($response);
    }
    public function transferPayment(Request $request){
       
        $data = $request->all();
        $auth_user = authSession();
        $user_id = $auth_user->id;
        $appointment = Appointment::where('id',$data['appointment_id'])->first();
        $transactions = AppointmentTransaction::where('appointment_id',$data['appointment_id'])->first();
       
        if($appointment->status == 'completed')
        {
            Setting::getSettings('default_time_zone') ?? 'UTC';
            $data['datetime'] = date('Y-m-d H:i:s');
            if($data['action'] == config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_APPROVED_CASH')){
                $data['text'] = __('messages.cash_approved',['amount' => Currency::format((float)$data['total_amount']),'name' => get_user_name($data['receiver_id']) ]);
                $paymentdata = $transactions->update(['payment_status' => $data['status']]);
            }
                $vendor_id = $appointment['vendor_id'] ?? null;
                    if($vendor_id){
                        $vendor = User::find($vendor_id);

                        if ($vendor->user_type == 'vendor' && multivendor() == 1) {
                            if($data['action'] == config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_SEND_VENDOR')){
                                $data['text'] = __('messages.payment_transfer',
                                ['from' => get_user_name($data['sender_id']),'to' => get_user_name($data['receiver_id']),'amount' => Currency::format((float)$data['total_amount']) ]);
                                $CommissionEarning = CommissionEarning::where('commissionable_id', $appointment->id)
                                ->where('user_type', 'collector')
                                ->first(); 
                            
                                if ($CommissionEarning) {
                                   
                                    $payouts = [
                                        'user_id' =>  $CommissionEarning->employee_id ?? null,
                                        'user_type' => $CommissionEarning->user_type ?? null,
                                        'payment_method' => 'cash',
                                        'bank' => null,
                                        'description' => $data['text'],
                                        'paid_date' => $data['datetime'] ?? now(),
                                        'amount' => $CommissionEarning->commission_amount ?? 0, 
                                    ];
                                
                                    $payout = Payout::create($payouts);
                                    $CommissionEarning->update(['commission_status' => 'paid']); 
                                }
                                $paymentdata = $transactions->update(['payment_status' => $data['status']]);
                            }
                            
                            if($data['action'] == config('constant.PAYMENT_HISTORY_ACTION.VENDOR_APPROVED_CASH')){
                                $data['text'] = __('messages.cash_approved',['amount' => Currency::format((float)$data['total_amount']),'name' => get_user_name($data['receiver_id']) ]);
                                $paymentdata = $transactions->update(['payment_status' => $data['status']]);
                            }
                            if($data['action'] == config('constant.PAYMENT_HISTORY_ACTION.VENDOR_SEND_ADMIN')){
                                $data['text'] =  __('messages.payment_transfer',['from' => get_user_name($data['sender_id']),'to' => get_user_name(admin_id()),
                                'amount' => Currency::format((float)$data['total_amount']) ]);
                                
                                $CommissionEarning = CommissionEarning::where('commissionable_id', $appointment->id)
                                ->where('user_type', 'vendor')
                                ->first(); 
                            
                                if ($CommissionEarning) {
                                   
                                    $payouts = [
                                        'user_id' =>  $CommissionEarning->employee_id ?? null,
                                        'user_type' => $CommissionEarning->user_type ?? null,
                                        'payment_method' => 'cash',
                                        'bank' => null,
                                        'description' => $data['text'],
                                        'paid_date' => $data['datetime'] ?? now(),
                                        'amount' => $CommissionEarning->commission_amount ?? 0, // Now it works
                                    ];
                                
                                    $payout = Payout::create($payouts);
                                    $CommissionEarning->update(['commission_status' => 'paid']); // Update status
                                }
                                $paymentdata = $transactions->update(['payment_status' => $data['status']]);
                            }
                            
                           
                        }else{
                          
                            if($data['action'] == config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_SEND_ADMIN')){
                                $data['text'] =  __('messages.payment_transfer',['from' => get_user_name($data['sender_id']),'to' => get_user_name(admin_id()),
                                'amount' => Currency::format((float)$data['total_amount']) ]);
                                $CommissionEarning = CommissionEarning::where('commissionable_id', $appointment->id)
                                ->where('user_type', 'collector')
                                ->first(); 
                            
                                if ($CommissionEarning) {
                                    
                                    $payouts = [
                                        'user_id' => $CommissionEarning->employee_id ?? null,
                                        'user_type' => $CommissionEarning->user_type ?? null,
                                        'payment_method' => 'cash',
                                        'bank' => null,
                                        'description' => $data['text'],
                                        'paid_date' => $data['datetime'] ?? now(),
                                        'amount' => $CommissionEarning->commission_amount ?? 0, // Now it works
                                    ];
                                
                                    $payout = Payout::create($payouts);
                                    $CommissionEarning->update(['commission_status' => 'paid']); // Update status
                                }
                               
                                $paymentdata = $transactions->update(['payment_status' => $data['status']]);
                            }
                            
                           
                        }
                       
                      
                    }
        }
       
        $result = CashPaymentHistories::updateOrCreate(
            [
                'appointment_id' => $data['appointment_id'], 
                'action' => $data['action'] 
            ],
            [
                'transaction_id' => $data['transaction_id'],
                'type' => $data['type'],
                'status' => $data['status'],
                'sender_id' => $data['sender_id'],
                'receiver_id' => $data['receiver_id'],
                'datetime' => $data['datetime'] ?? now(),
                'total_amount' => $data['total_amount'] ?? 0,
                'type' => $data['payment_type'] ?? 'cash',
                'parent_id' => $data['parent_id'] ?? CashPaymentHistories::where('appointment_id', $data['appointment_id'])->value('id'),
                'text' => $data['text'] ?? null,

            ]
        );
        
        
        $message = trans('messages.cash_transfer');
       
        $response = [
            'message' => $message,
            'appointment' => new AppointmentDetailResource($appointment),
        ];
        return comman_custom_response($response);
    }
    public function paymentList(Request $request)
    {
        $payment = AppointmentTransaction::myPayment()->with('appointment');
        if($request->has('appointment_id') && !empty($request->appointment_id)){
            $payment->where('appointment_id',$request->appointment_id);
        }
        if($request->has('payment_type') && !empty($request->payment_type)){
                $payment->where('payment_type',$request->payment_type);
        }
        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page)){
                $per_page = $request->per_page;
            }
            if($request->per_page === 'all' ){
                $per_page = $payment->count();
            }
        }
        $payment = $payment->orderBy('id','desc')->paginate($per_page);
        $items = PaymentResource::collection($payment);

        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
        ];

        return comman_custom_response($response);
    }
    public function cashpaymentHistory(Request $request){
        $transaction_id = $request->transaction_id;
        $payment = CashPaymentHistories::with('transaction','appointment')->where('transaction_id',$transaction_id);

        $per_page = config('constant.PER_PAGE_LIMIT');
        if( $request->has('per_page') && !empty($request->per_page)){
            if(is_numeric($request->per_page)){
                $per_page = $request->per_page;
            }
            if($request->per_page === 'all' ){
                $per_page = $payment->count();
            }
        }

        $payment = $payment->orderBy('id','desc')->paginate($per_page);
        $items = CashPaymentHistoryResource::collection($payment);

        $response = [
            'pagination' => [
                'total_items' => $items->total(),
                'per_page' => $items->perPage(),
                'currentPage' => $items->currentPage(),
                'totalPages' => $items->lastPage(),
                'from' => $items->firstItem(),
                'to' => $items->lastItem(),
                'next_page' => $items->nextPageUrl(),
                'previous_page' => $items->previousPageUrl(),
            ],
            'data' => $items,
        ];

        return comman_custom_response($response);

    }
    public function paymentDetail(Request $request)
    {
        $auth_user = authSession();
        $user_id = $auth_user->id;

        $payments = CashPaymentHistories::query();

        if (!empty($request->status)) {
            $payments = $payments->where('status', $request->status);
        }

        $user = auth()->user();
        $role = $user->hasAnyRole(['collector', 'vendor']) ? $user->getRoleNames()->first() : null;
        $status = $request->status ?? null;

        $roleActionMap = [
            'collector' => [
                config('constant.PAYMENT_HISTORY_STATUS.APPROVED_COLLECTOR') => ['action' => config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_APPROVED_CASH'), 'column' => 'receiver_id'],
                config('constant.PAYMENT_HISTORY_STATUS.PENDING_ADMIN') => ['action' => config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_SEND_ADMIN'), 'column' => 'sender_id'],
                config('constant.PAYMENT_HISTORY_STATUS.APPROVED_ADMIN') => ['action' => config('constant.PAYMENT_HISTORY_ACTION.ADMIN_APPROVED_CASH'), 'column' => 'sender_id'],
                config('constant.PAYMENT_HISTORY_STATUS.PENDING_VENDOR') => ['action' => config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_SEND_VENDOR'), 'column' => 'sender_id'],
                config('constant.PAYMENT_HISTORY_STATUS.APPROVED_VENDOR') => ['action' => config('constant.PAYMENT_HISTORY_ACTION.VENDOR_APPROVED_CASH'), 'column' => 'sender_id'],
                'default' => [
                    'actions' => [
                        config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_APPROVED_CASH'),
                        config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_SEND_ADMIN'),
                        config('constant.PAYMENT_HISTORY_ACTION.ADMIN_APPROVED_CASH'),
                        config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_SEND_VENDOR'),
                        config('constant.PAYMENT_HISTORY_ACTION.VENDOR_APPROVED_CASH'),
                        config('constant.PAYMENT_HISTORY_ACTION.VENDOR_SEND_ADMIN')
                    ]
                ],
            ],
            'vendor' => [
                config('constant.PAYMENT_HISTORY_STATUS.PENDING_ADMIN') => ['action' => config('constant.PAYMENT_HISTORY_ACTION.VENDOR_SEND_ADMIN'), 'column' => 'sender_id'],
                config('constant.PAYMENT_HISTORY_STATUS.APPROVED_VENDOR') => ['action' => config('constant.PAYMENT_HISTORY_ACTION.VENDOR_APPROVED_CASH'), 'column' => 'receiver_id'],
                config('constant.PAYMENT_HISTORY_STATUS.PENDING_VENDOR') => ['action' => config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_SEND_VENDOR'), 'column' => 'receiver_id'],
                config('constant.PAYMENT_HISTORY_STATUS.APPROVED_ADMIN') => ['action' => config('constant.PAYMENT_HISTORY_ACTION.ADMIN_APPROVED_CASH'), 'column' => 'sender_id'],
                'default' => [
                    'actions' => [
                        config('constant.PAYMENT_HISTORY_ACTION.COLLECTOR_SEND_VENDOR'),
                        config('constant.PAYMENT_HISTORY_ACTION.VENDOR_SEND_ADMIN'),
                        config('constant.PAYMENT_HISTORY_ACTION.VENDOR_APPROVED_CASH'),
                        config('constant.PAYMENT_HISTORY_ACTION.ADMIN_APPROVED_CASH')
                    ]
                ],
            ],
        ];

        if ($role && isset($roleActionMap[$role])) {
            if (!empty($status) && isset($roleActionMap[$role][$status])) {
                $actionData = $roleActionMap[$role][$status];
                $payments = $payments->where('action', $actionData['action'])
                                    ->where($actionData['column'], $user_id)
                                    ->orderBy('id', 'desc');
            } else {
                $payments = $payments->whereIn('action', $roleActionMap[$role]['default']['actions'])
                    ->where(function ($query) use ($user_id) {
                        $query->where('receiver_id', $user_id)
                            ->orWhere('sender_id', $user_id);
                    })
                    ->orderBy('id', 'desc');
            }
        }

        
       $payments->whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')  
                ->from('cash_payment_histories')
                ->groupBy('appointment_id');
        });

        
        if (!empty($request->from) && !empty($request->to)) {
            $payments = $payments->whereDate('datetime', '>=', $request->from)
                                ->whereDate('datetime', '<=', $request->to);
        }

        
        $per_page = config('constant.PER_PAGE_LIMIT');
        if ($request->has('per_page') && !empty($request->per_page)) {
            $per_page = ($request->per_page === 'all') ? $payments->count() : (int) $request->per_page;
        }
        $payments = $payments->paginate($per_page);

        
        $items = CashPaymentHistoryResource::collection($payments);
        $cashpayment = new CashPaymentHistories;
        $total_cash_in_hand = $cashpayment->total_cash_in_hand($user_id);

        $response = [
            'today_cash' => $payments->sum('total_amount') ?? 0,
            'total_cash_in_hand' => $total_cash_in_hand ?? 0,
            'cash_detail' => $items,
        ];

        return comman_custom_response($response);
    }

}
