<?php

namespace Modules\Payout\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Modules\Payout\Models\Payout;
use Illuminate\Http\Request;
use Modules\Wallet\Trait\PayoutTrait;
use Yajra\DataTables\DataTables;
use Modules\Payout\Http\Requests\PayoutRequest;
use App\Trait\ModuleTrait;
use App\Models\User;
use Modules\Commision\Models\CommissionEarning;
use Currency;
use App\Trait\NotificationTrait;
use App\Trait\PaymentTrait;
use Illuminate\Support\Facades\Http;
use Stripe\StripeClient;
use GuzzleHttp\Client;
use Modules\Wallet\Models\Wallet;
use Modules\Wallet\Models\WalletHistory;
use Modules\Bank\Models\Bank;

class PayoutsController extends Controller
{
    use PaymentTrait;
    use PayoutTrait;
    use NotificationTrait;
    protected string $exportClass = '\App\Exports\PayoutExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.collector_payouts', // module title
            'payouts', // module name
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
        $module_title=__('messages.collector_payouts');
        $module_action = __('messages.list');
        $user_type = 'collector';
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ],
            [
                'value' => 'payment_method',
                'text' => __('messages.payment_method'),
            ],
            [
                'value' => 'amount',
                'text' => __('messages.amount'),
            ],
            [
                'value' => 'paid_date',
                'text' => __('messages.paid_date'),
            ]

        ];
        $export_url = route('backend.payouts.export',['user_type' => $user_type]);

        return view('payout::backend.index', compact('module_action','module_title', 'filter', 'export_import', 'export_columns', 'export_url'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'Payout'; // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Payout::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $user = auth()->user();

        $query = Payout::query()->where('user_type','collector')->withTrashed();
        if ($user->user_type == 'vendor') {
            $collectorIds = $user->MyCollector()->pluck('id')->toArray();
   
            $query->whereIn('user_id', $collectorIds);
        }
        $filter = $request->filter;

        if (isset($filter['collector_name']) && !empty($filter['collector_name'])) {
            $query->where('user_id', $filter['collector_name']); 
        }
        if (isset($request->collector_id) && $request->collector_id !== null) {
            $query->where('user_id', $request->collector_id);
        }
        if (isset($filter['payment_method']) && !empty($filter['payment_method'])) {
            $query->where('payment_method', $filter['payment_method']);
        }
        return $datatable->eloquent($query)
            ->editColumn('user_id', function ($data) {
                $data = $data->user ?? null;
                if (!$data){
                    return '-';
                }
                return view('user::backend.users.user_details', compact('data'));
            })
          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" onclick="dataTableRowCheck('.$data->id.')">';
          })
          ->editColumn('payment_method', function ($data) {
            return ucfirst($data->payment_method) ?? '-';
        })
          ->editColumn('amount', function ($data) {
            return Currency::format($data->amount) ?? '-';
            })
          ->addColumn('paid_date', function ($data) {
              return Setting::formatDate($data->paid_date) ?? '-';
          })
          ->editColumn('status', function ($data) {
              return $data->getStatusLabelAttribute();
          })
          ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
          ->rawColumns(['action', 'status', 'check'])
          ->orderColumns(['id'], '-:column $1')
          ->make(true);
    }

    public function vendor_index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];
        $module_title=__('messages.vendor_payouts');
        $module_action = __('messages.list');
        $user_type = 'vendor';
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ],
            [
                'value' => 'payment_method',
                'text' => __('messages.payment_method'),
            ],
            [
                'value' => 'amount',
                'text' => __('messages.amount'),
            ],
            [
                'value' => 'paid_date',
                'text' => __('messages.paid_date'),
            ]
        ];
        $export_url = route('backend.payouts.export',['user_type' => $user_type]);

        return view('payout::backend.vendor_index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url','module_title'));
    }
    public function vendor_index_data(Datatables $datatable, Request $request)
    {
        $query = Payout::query()->where('user_type','vendor')->where('amount', '>',0)->withTrashed();

        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }
        if (isset($request->vendor_id) && $request->vendor_id !== null) {
            $query->where('user_id', $request->vendor_id);
        }
        if (isset($filter['vendor_name']) && !empty($filter['vendor_name'])) {
            $query->where('user_id', $filter['vendor_name']); 
        }
        if (isset($filter['payment_method']) && !empty($filter['payment_method'])) {
            $query->where('payment_method', $filter['payment_method']);
        }
        return $datatable->eloquent($query)
            ->editColumn('user_id', function ($data) {
                $data = $data->user ?? null;
                if (!$data){
                    return '-';
                }
                return view('user::backend.users.user_details', compact('data'));
            })
          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" onclick="dataTableRowCheck('.$data->id.')">';
          })
          ->editColumn('payment_method', function ($data) {
            return ucfirst($data->payment_method) ?? '-';
        })
          ->editColumn('amount', function ($data) {
            return Currency::format($data->amount) ?? '-';
            })
          ->addColumn('paid_date', function ($data) {
              return Setting::formatDate($data->paid_date) ?? '-';
          })
          ->editColumn('status', function ($data) {
              return $data->getStatusLabelAttribute();
          })
          ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
          ->rawColumns(['action', 'status', 'check'])
          ->orderColumns(['id'], '-:column $1')
          ->make(true);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */

      public function create(Request $request)
    {
        $id = $request->id ?? null;
        if (!$id) {
            return redirect()->back()->with('error', __('messages.invalid_request_id'));
        }
        $assets = ['textarea'];
        $collector = User::MyCollector()->where('user_type','collector')->with('commission_earning')->with('banks')->find($id);
        if(!$collector){
            return redirect()->back()->with('error', __('messages.invalid_request_user'));
        }

        
        $commissionData = $collector->commission_earning()
            ->where('deleted_at', null)
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'completed');
            })
            ->where('commission_status', 'unpaid')
            ->where('user_type', 'collector')
            ->get(); // Use get() to retrieve the results as a collection

        $collectorearning = 0;

        if ($commissionData) {
            foreach ($commissionData as $commission) {
                $commission_data = CommissionEarning::where('commissionable_id', $commission->commissionable_id) // Use $commission->booking_id
                    ->whereIn('user_type', ['collector'])
                    ->where('commission_status', 'unpaid')
                    ->where('deleted_at', null)
                    ->get(); 

                foreach ($commission_data as $data) {
                    if (isset($data->commission_amount)) {
                        $collectorearning += $data->commission_amount;
                    }
                }
            }
        }
      return view('payout::backend.create',compact('collector', 'collectorearning','assets'));
    }

    public function store(PayoutRequest $request)
    {
      
        $data = $request->all();
        $data['paid_date'] = now();
        $collectordata = User::with('collectorVendormapping')->where('id',$data['user_id'])->where('user_type',$data['user_type'])->first();
        $data['account_holder_name'] = $collectordata->first_name.' '.$collectordata->last_name ?? 'Unknown';
        if(!$collectordata){
            return redirect()->back()->with('error', __('messages.invalid_request_user'));
        }
        $adminIds = User::whereIn('user_type', ['admin', 'demo_Admin'])->pluck('id')->toArray();
        if($data['payment_method'] == 'bank' && !empty($data['payment_gateway'])){
        switch ($data['payment_gateway']) {
            case 'stripe':
               $stripePaid =$this->payout_stripe($data);
               break;
            case 'razorpayx':
                
                $rozarPaid= $this->handleRazorpayPayout($data);
                break;
            default:
                return [
                    'status' => __('messages.error'),
                    'message' => __('invalid_payment_gateway_select')
                ];
        }
        if($rozarPaid['success']!='true'){
            return redirect()->back()->with('error', __('messages.error_payout_transfer'));

        }
      }


      if (multivendor() == 1 && $collectordata->collectorVendormapping && !in_array($collectordata->collectorVendormapping->vendor_id, $adminIds)) {        
    
        $vendorId = $collectordata->collectorVendormapping->vendor_id;
        $collectorId = $collectordata->id;
    
        // Calculate vendor and collector earnings
        $vendorEarning = Payout::where('user_id', $vendorId)->where('user_type', 'vendor')->sum('amount');
        $collectorEarning = Payout::where('user_id', $collectorId)->where('user_type', 'collector')->sum('amount');

        $remainingVendorEarning = $vendorEarning - $collectorEarning;
    
        // Check if the vendor has enough earnings
     
        if ($remainingVendorEarning < $data['amount']) {
            $message = __('messages.less_vendor_earning');
    
            if (request()->is('api*')) {
                return response()->json([
                    'message' => $message,
                    'status' => false,
                ], 400); // 400 Bad Request
            }
    
            return redirect()->back()->with('error', $message);
        }
    
        // Retrieve the latest vendor payout record
        $vendorPayoutUpdate = Payout::where('user_id', $vendorId)
            ->where('user_type', 'vendor')
            ->latest('id') // Order by latest ID
            ->first();
    
        if ($vendorPayoutUpdate) {
            $newPayoutAmount = -abs($data['amount']);
   
            // Prepare new payout data
            $vendorPayoutData = [
                'user_id'        => $vendorId,
                'user_type'      => 'vendor',
                'amount'         => $newPayoutAmount ?? 0, // Ensure non-negative payout
                'payment_method' => $vendorPayoutUpdate->payment_method ?? null,
                'bank'           => null,
                'paid_date'      => now(),
            ];
            // Create a new payout entry
            Payout::create($vendorPayoutData);
        }
    }
        if($data['payment_method']=='wallet'){
            $walletResult = $this->handleWalletPayment($data, $collectordata,$vendorId);

        }
        $payout = Payout::create($data);
        
        CommissionEarning::where('employee_id', $data['user_id'])->where('commission_status','unpaid')->update(['commission_status' => 'paid']);
        $activity_data = [
            'type' => 'collector_payout',
            'activity_type' => 'collector_payout',
            'notification_type' => 'collector_payout',
            'id' => $payout->id,
            'pay_date' => $payout->paid_date,
            'user_id' => $payout->collector_id,
            'amount' => $payout->amount,
            'collector_id' => $payout->collector_id,
            'collector_name' => $collectordata->first_name . $collectordata->last_name,
        ];
       
        $this->sendNotification($activity_data);
        $module_title=__('New Payout');
        $message=__('message.record_add');
        return redirect()->route('backend.payouts.index',compact('module_title'))->with('success',$message);

    }


    public function vendor_create(Request $request)
    {
        if (auth()->user()->hasRole('vendor')) {
            return redirect()->back()->with('error', __('messages.do_not_permission'));
        }
        $id = $request->id ?? null;
        if (!$id) {
            return redirect()->back()->with('error', __('messages.invalid_request_id'));
        }
        $assets = ['textarea'];
        $module_title=__('messages.vendor_payouts');
        $vendor = User::where('user_type','vendor')->with('commission_earning')->with('banks')->find($id);
        if(!$vendor){
            return redirect()->back()->with('error', __('messages.invalid_request_user'));
        }
       
        $commissionData = $vendor->commission_earning()
        ->where('deleted_at', null)
            ->whereHas('getAppointment', function ($query) {
                $query->where('status', 'completed');
            })
            ->where('commission_status', 'unpaid')
            ->where('user_type', 'vendor')
            ->get(); // Use get() to retrieve the results as a collection
           
        $vendorearning = 0;

        if ($commissionData) {
            foreach ($commissionData as $commission) {
                $commission_data = CommissionEarning::where('commissionable_id', $commission->commissionable_id) // Use $commission->booking_id
                    ->whereIn('user_type', ['collector','vendor'])
                    ->where('commission_status', 'unpaid')
                    ->get(); 

                foreach ($commission_data as $data) {
                    if (isset($data->commission_amount)) {
                        $vendorearning += $data->commission_amount;
                    }
                }
            }
        }
      return view('payout::backend.vendor_create',compact('vendor', 'vendorearning','assets','module_title'));
    }

    public function vendor_store(PayoutRequest $request)
    {
        $data = $request->all();

        $data['paid_date'] = now();
        $vendor = User::where('id',$data['user_id'])->where('user_type',$data['user_type'])->first();
        $data['account_holder_name'] = $vendor->first_name.' '.$vendor->last_name ?? __('messages.Unknown');
        if(!$vendor){
            return redirect()->back()->with('error', __('messages.invalid_request_user'));
        }
        if($data['payment_method'] == 'bank' && !empty($data['payment_gateway'])){
            switch ($data['payment_gateway']) {
                case 'stripe':
                   $stripePaid =$this->payout_stripe($data);
                   break;
                case 'razorpayx':
                    
                    $rozarPaid= $this->handleRazorpayPayout($data);
                    break;
                default:
                    return [
                        'status' => __('messages.error'),
                        'message' => __('messages.invalid_payment_gateway_select')
                    ];
            }
            if (!$rozarPaid['success']) {
                return redirect()->back()->with('error', $rozarPaid['error']);
            }
          }
      
        if($data['payment_method']=='wallet'){
            $walletResult = $this->handleWalletPayment($data, $vendor,null);
          }

        $payout = Payout::create($data);
        CommissionEarning::where('employee_id', $data['user_id'])->where('commission_status','unpaid')->update(['commission_status' => 'paid']);
        $activity_data = [
            'type' => 'vendor_payout',
            'activity_type' => 'vendor_payout',
            'notification_type' => 'vendor_payout',
            'id' => $payout->id,
            'pay_date' => $payout->paid_date,
            'user_id' => $payout->vendor_id,
            'vendor_id' => $payout->vendor_id,
            'amount' => $payout->amount,
            'vendor_name' => $vendor->first_name . $vendor->last_name,
        ];
     
        $this->sendNotification($activity_data);
        $message=__('messages.record_add');
        return redirect()->route('backend.payouts.vendor_index')->with('success', $message);
    }

 


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Payout::findOrFail($id);
        $module_title = __('Edit Payout');
    return view('payout::backend.payout.edit', compact('data','Module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(PayoutRequest $request, Payout $payout)
    {
        $requestData = $request->all();
        $payout->update($requestData);
        $message=__('messages.update_form');
        return redirect()->route('backend.payouts.index', $payout->id)->with('success',$message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Payout::findOrFail($id);
        $data->delete();
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = Payout::withTrashed()->findOrFail($id);
        $data->restore();
        $message = __('messages.restore_form');
        return response()->json(['message' =>  $message]);
    }

    public function forceDelete($id)
    {
        $data = Payout::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $message = __('messages.permanent_delete_form');
        return response()->json(['message' =>  $message]);
    }

    //wallet
    protected function handleWalletPayment($data, $user, $vendorId = null)
    {
        $wallets = Wallet::whereIn('user_id', [$user->id, $vendorId])->get();

        // Get or create wallet for the user
        $wallet = $wallets->where('user_id', $user->id)->first();
        if (!$wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'title' => $user->full_name,
                'amount' => 0,
                'status' => 1
            ]);
        }
    
        // Handle vendor wallet deduction
        if ($vendorId !== null) {
            $vendorWallet = $wallets->where('user_id', $vendorId)->first();
            if (!$vendorWallet) {
                $vendorWallet = Wallet::create([
                    'user_id' => $vendorId,
                    'title' => 'Vendor Wallet',
                    'amount' => 0,
                    'status' => 1
                ]);
            }
    
            // Ensure the vendor has sufficient balance before deducting
            if ($vendorWallet->amount < $data['amount']) {
                return [
                    'success' => false,
                    'message' => __('messages.insufficient_vendor_wallet_balance')
                ];
            }
    
            $vendorWallet->amount -= $data['amount'];
            $vendorWallet->save();
            WalletHistory::create([
                'datetime' => now(),
                'user_id' => $vendorId,
                'activity_type' => 'commission_debit',
                'activity_message' => __('messages.commission_earnings_debit_to_wallet'),
                'activity_data' => json_encode([
                    'amount' => $data['amount'],
                    'previous_balance' => ($vendorWallet->amount - $data['amount']),
                    'new_balance' => $vendorWallet->amount,
                    'payout_id' => null
                ])
            ]);
        }

            // Add commission amount to wallet
            $wallet->amount += $data['amount'];
            $wallet->save();

            // Create wallet history
            WalletHistory::create([
                'datetime' => now(),
                'user_id' => $user->id,
                'activity_type' => 'wallet_credit',
                'activity_message' => $user->first_name .' '. $user->last_name . __('messages.has_credited_to_wallet'),
                'activity_data' => json_encode([
                    'amount' => $data['amount'],
                    'previous_balance' => ($wallet->amount - $data['amount']),
                    'new_balance' => $wallet->amount,
                    'payout_id' => null
                ])
            ]);
            return [
                'success' => true,
                'message' => __('messages.amount_add_wallet'),
                'wallet_balance' => $wallet->amount
            ];
        
    
    }

}






