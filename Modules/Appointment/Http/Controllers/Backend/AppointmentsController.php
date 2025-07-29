<?php

namespace Modules\Appointment\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Appointment\Models\Appointment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Appointment\Http\Requests\AppointmentRequest;
use App\Trait\ModuleTrait;
use App\Models\User;
use Currency;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Appointment\Trait\AppointmentTrait;
use Modules\Appointment\Models\AppointmentCollectorMapping;
use Modules\Appointment\Models\AppointmentOtpMapping;
use Illuminate\Support\Facades\Crypt;
use App\Trait\NotificationTrait;
use Modules\Appointment\Models\AppointmentActivity;
use Modules\Appointment\Transformers\AppointmentDetailResource;
use Modules\Prescription\Models\Prescription;
use Modules\Appointment\Models\AppointmentStatus;
use Modules\Lab\Models\Lab;
use App\Models\Setting;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceEmail;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Modules\Appointment\Models\AppointmentPackageMapping;
use Modules\PackageManagement\Models\PackageManagement;
use DB;

class AppointmentsController extends Controller
{
    protected string $exportClass = '\App\Exports\AppointmentExport';
    use NotificationTrait;
    use AppointmentTrait;
    use ActivityLogger;
    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.appointments', 
            'appointments', 
            'fa-solid fa-clipboard-list' 
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
        $collector_id = null;
        $lab_id = null;
        $vendor_id = null;
        $paymentGateways = Setting::where('datatype', 'payment_gateways')->where('val', 1)->get();

        if ($request->has('collector_id')) {
            try {
                $collector_id = Crypt::decrypt($request->collector_id);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                abort(404);
            }
        }
        if ($request->has('lab_id')) {
            try {
                $lab_id = Crypt::decrypt($request->lab_id);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                abort(404);
            }
        }
        $type = __('messages.lbl_appointment');
        $module_title = __('messages.appointment');
        if ($request->has('vendor_id')) {
            try {
                
                $vendor_id = Crypt::decrypt($request->vendor_id);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                abort(404);
            }
            
        }
        $module_action = __('messages.list');
        $export_import = true;
        $export_columns = [
            [
                'value' => 'id',
                'text' => __('messages.appointment_id'),
            ],
            [
                'value' => 'datetime',
                'text' => __('messages.datetime'),
            ],
            [
                'value' => 'customer',
                'text' => __('messages.lbl_customer'),
            ],
            [
                'value' => 'lab',
                'text' => __('messages.lbl_lab'),
            ],
            [
                'value' => 'collector',
                'text' => __('messages.lbl_collector'),
            ],
            [
                'value'=>'vendor',
                'text'=>__('messages.lbl_vendor'),
            ],
            [
                'value'=>'test',
                'text'=>__('messages.test'),
            ],
            [
                'value' => 'payment_status',
                'text' => __('messages.lbl_payment_status'),
            ],
            [
                'value'=>'total_amount',
                'text'=>__('messages.lbl_total_amount'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ]
        ];
        $export_url = route('backend.appointments.export');

        return view('appointment::backend.index', compact('lab_id','collector_id','module_action', 'filter', 'export_import', 'export_columns', 'export_url','paymentGateways','vendor_id'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.appointment'); 
        $messageKey = __('messages.bulk_action'); 

        return $this->performBulkAction(Appointment::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        
        $query = Appointment::MyAppointment()
                ->with(['appointmentCollectorMapping','transactions','vendor','catlog','package','customer'])
                ->where('submission_status','!=','reject');
            $user = auth()->user();
               
            $user_id = $request->query('user_id');
         
            if($user_id){
                $query=  $query->where('customer_id', $user_id);
            }
            $lab_id = $request->query('lab_id');
            if($lab_id && $lab_id !== null){
                    $query->where('lab_id', $lab_id);
            }
            $payment_status = $request->query('payment_status');
            if($payment_status){
                $query->whereHas('transactions', function($q) use ($payment_status) {
                    $q->where('payment_status', $payment_status);
                });
            }
            $collectorId = !empty($request->query('collectorId')) ? $request->query('collectorId') : $request->collector_id;
            if ($collectorId && $collectorId !== null) {
                $query->whereHas('commissionsdata', function ($q) {
                    $q->whereIn('commission_status', ['unpaid', 'paid']);
                })->whereHas('appointmentCollectorMapping', function($q) use ($collectorId) {
                    $q->where('collector_id', $collectorId);
                });
            }

            $vendorId = !empty($request->query('vendorId')) ? $request->query('vendorId') :  $request->vendor_id;
           
            if ($vendorId) {
                 $query->whereHas('commissionsdata', function ($q) {
                    $q->whereIn('commission_status', ['unpaid', 'paid']);
                })->where('vendor_id', $vendorId);
            }

        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }

        if (isset($filter['vendor'])) {
            $vendorName = $filter['vendor'];
            $query->whereHas('vendor', function ($subQuery) use ($vendorName) {
                $subQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$vendorName%"])
                         ->orWhere('email', 'like', '%' . $vendorName . '%');
            });
        }

            
        if (!empty($filter['collector_id']) && $filter['collector_id'] !== null) {
            $query->whereHas('appointmentCollectorMapping', function($q) use ($filter) {
                $q->where('collector_id', $filter['collector_id']);
            });
        }

      
        if (!empty($filter['lab_id'])) {
            $query->where('lab_id', $filter['lab_id']);
        }

       
        if (!empty($filter['vendor_id'])) {
            $query->where('vendor_id', $filter['vendor_id']);
        }

       
        if (!empty($filter['test_id'])) {
            $query->where(function($q) use ($filter) {
                $q->where('test_id', $filter['test_id']);
            });
        }

       
        if (!empty($filter['user_name'])) {
            $query->whereHas('customer', function($q) use ($filter) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $filter['user_name'] . '%')
                ->orWhere('email', 'like', '%' . $filter['user_name'] . '%');
            });
        }

       
        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }

      
        if (!empty($filter['payment_status'])) {
            $query->whereHas('transactions', function($q) use ($filter) {
                $q->where('payment_status', $filter['payment_status']);
            });
        }

        
        if (!empty($filter['booking_status'])) {
            $query->where('status', $filter['booking_status']);
        }

        
        if (!empty($filter['submission_status'])) {
            $query->where('submission_status', $filter['submission_status']);
        }

      
        if (!empty($filter['payment_type'])) {
            $query->whereHas('transactions', function($q) use ($filter) {
                $q->where('payment_type', $filter['payment_type']);
            });
        }

        if ($user->user_type == 'vendor') {
           
            $query = $query->where('vendor_id', $user->id);
        }
          
        return $datatable->eloquent($query)
            ->editColumn('id', function($data) {
                
                return '#'.$data->id;
            })
            ->editColumn('appointment_date', function($data) {
                if ($data->appointment_date && $data->appointment_time) {
                    $date = Setting::formatDate($data->appointment_date).'  '.Setting::formatTime($data->appointment_time);
            return $date;
                  
                }
                return '-';
            })
            ->addColumn('appointment_id', function ($data) {
                return $data->id; 
            })
           
            ->addColumn('transaction_id', function ($data) {
                return $data->transactions ? $data->transactions->txn_id : '-';
            })
            ->addColumn('payment_method', function ($data) {
                return $data->transactions ? ucfirst($data->transactions->payment_type) : '-';
            })
            ->filterColumn('payment_method', function ($query, $keyword) {
                $query->whereHas('transactions', function ($q) use ($keyword) {
                    $q->where('payment_type', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('check', function ($data) {
             
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"   name="datatable_ids[]" value="'.$data->id.'" data-type="appointment" onclick="dataTableRowCheck('.$data->id.',this)">';            })
            ->addColumn('customer', function($data) {
                $data = User::find($data->customer_id);
                return view('user::backend.users.user_details', compact('data'));
            })
            ->filterColumn('customer', function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $keyword . '%')
                      ->orWhere('email', 'like', '%' . $keyword . '%');
                });
            })
            ->orderColumn('customer', function ($query, $order) {
                $query->select('appointments.*')
                      ->leftJoin('users', 'appointments.customer_id', '=', 'users.id')
                      ->orderByRaw("CONCAT(users.first_name, ' ', users.last_name) $order");
            })
            ->addColumn('lab', function($data) {
                $data = $data->lab ? $data->lab : null;
                if($data){
                    return view('lab::backend.lab_details', compact('data'));
                }
                return '-';
            })
            ->filterColumn('lab', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('lab', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%')
                          ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('lab', function ($query, $order) {
                $query->select('appointments.*')
                      ->leftJoin('labs', 'appointments.lab_id', '=', 'labs.id')
                      ->orderBy('labs.name', $order);
            })
            ->addColumn('collector', function($data) {
                if ($data->status === 'cancelled') {
                    return '-';
                }
             
                if($data->appointmentCollectorMapping == null){
                    $collectors = User::where('user_type', 'collector')->where('status', 1)->where('is_available',1)
                        ->whereHas('collectorVendormapping', function ($query) use ($data) {
                            $query->where('vendor_id', $data->vendor_id);
                        })
                        ->whereHas('lab', function ($query) use ($data) {
                            $query->where('lab_id', $data->lab_id);
                        })->get();

                             return view('appointment::backend.collector_select', compact('data', 'collectors'));
                }else{
                    $data = User::find($data->appointmentCollectorMapping->collector_id);
                    return view('user::backend.users.user_details', compact('data'));
                }
            })
            ->filterColumn('collector', function ($query, $keyword) {
                $query->whereHas('appointmentCollectorMapping.collector', function ($q) use ($keyword) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $keyword . '%')
                      ->orWhere('email', 'like', '%' . $keyword . '%');
                });
            })
            ->orderColumn('collector', function ($query, $order) {
                $query->select('appointments.*')
                      ->leftJoin('appointment_collector_mapping', 'appointments.id', '=', 'appointment_collector_mapping.appointment_id')
                      ->leftJoin('users as collectors', 'appointment_collector_mapping.collector_id', '=', 'collectors.id')
                      ->orderByRaw("CONCAT(COALESCE(collectors.first_name, ''), ' ', COALESCE(collectors.last_name, '')) {$order}")
                      ->select('appointments.*'); 
            })
            ->addColumn('vendor', function($data) {
                $data = User::find($data->vendor_id);
                return view('user::backend.users.user_details', compact('data'));
            })
            ->filterColumn('vendor', function ($query, $keyword) {
                $query->whereHas('vendor', function ($q) use ($keyword) {
                    $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', '%' . $keyword . '%')
                      ->orWhere('email', 'like', '%' . $keyword . '%');
                });
            })
            ->orderColumn('vendor', function ($query, $order) {
                $query->select('appointments.*')
                      ->leftJoin('users as vendors', 'appointments.vendor_id', '=', 'vendors.id')
                      ->orderByRaw("CONCAT(COALESCE(vendors.first_name, ''), ' ', COALESCE(vendors.last_name, '')) {$order}");
            })
            ->addColumn('test_id', function ($data) {
                $test = $data->getTestAttribute();
                return $test ? $test->name : '-';
            }) 
            ->filterColumn('test_id', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                   
                    $q->where(function ($subQ) use ($keyword) {
                        $subQ->where('test_type', 'test_package')
                             ->whereHas('package', function ($packageQuery) use ($keyword) {
                                 $packageQuery->where('name', 'like', "%{$keyword}%");
                             });
                    })
                    
                    ->orWhere(function ($subQ) use ($keyword) {
                        $subQ->where('test_type', 'test_case')
                             ->whereHas('catlog', function ($catlogQuery) use ($keyword) {
                                 $catlogQuery->where('name', 'like', "%{$keyword}%");
                             });
                    });
                });
            })
            
            ->addColumn('total_amount', function($data) {
                return \Currency::format($data->total_amount) ?? 0;

            }) 
            ->filterColumn('total_amount', function ($query, $keyword) {
               
                $cleanedKeyword = preg_replace('/[^0-9.]/', '', $keyword);
            
               
                if ($cleanedKeyword !== '') {
                   
                    $query->whereRaw("CAST(REGEXP_REPLACE(total_amount, '[^0-9.]', '') AS DECIMAL(10, 2)) LIKE ?", ["%{$cleanedKeyword}%"]);
                }
            })
            ->addColumn('payment_status',function($data){
                $collectorId = optional($data->appointmentCollectorMapping)->collector_id;
                $payment_status = $data->transactions  ? $data->transactions->payment_status : '-';
               
                if ($data->status == 'completed' && $payment_status == "pending" && ($collectorId == auth()->id() || auth()->user()->hasrole('admin')) ) {
                    return view('appointment::backend.status_select', compact('data'));
                }
                if ($payment_status != '-') {
                    
                    $payment_status = getPaymentStatusAttribute($payment_status);
                }
                
                return $payment_status;          
              })     
            ->filterColumn('payment_status', function ($query, $keyword) {
               
                $query->whereHas('transactions', function ($q) use ($keyword) {
                    
                    $q->where('payment_status', 'like', "%{$keyword}%");
                });
            })
            ->orderColumn('payment_status', function ($query, $direction) {
                $query->join('appointment_transaction as at', 'appointments.id', '=', 'at.appointment_id')
                      ->orderBy('at.payment_status', $direction);
            })
            ->addColumn('action', function ($data) {
                return view('appointment::backend.action', compact('data'));
            })
            ->editColumn('status', function ($data) {
                $collectorId = optional($data->appointmentCollectorMapping)->collector_id;
            
               
                if ($data->status !== 'completed' && $data->status !== 'cancelled' && ($collectorId === auth()->id() || auth()->user()->hasrole('admin'))) {
                    $status = AppointmentStatus::where('status', 1)->get();
                    return view('appointment::backend.status_select', compact('data', 'status'));
                }
                $payment_status = $data->transactions  ? $data->transactions->payment_status : '-';
                if ($data->status == 'completed' && $payment_status == 'paid') {
                    if ($data->test_case_status == 'report_generated') {
                        return '<span class="badge bg-success-subtle">Report Generated</span>';
                    }                    
                    return view('appointment::backend.submission_status', compact('data'));
                }
                return $data->getAppointmentStatusAttribute();
            })                      
           
            ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
            ->rawColumns(['action', 'status', 'check','test_id','total_amount','payment_status','collector','submission_status','amount','customer','lab','vendor'])
            ->orderColumns(['id','test_id','total_amount'], ':column $1')
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */

      public function create()
    {
        $customers = User::where('user_type', 'user')->where('status', 1)->get();
        $vendors = User::where('user_type', 'vendor')->where('status', 1)->get();
        $assets = ['textarea'];
        $module_title=__('messages.new_appointment');
      return view('appointment::backend.create', compact('customers', 'vendors','assets','module_title'));
    }

    public function store(AppointmentRequest $request)
    {
       
        $data = $request->all();
        $data['other_member_id'] = $data['other_member_id'] ?? null;
        
        $couponId = $data['coupon_id'] ?? null;
        $adminId = User::where('user_type','admin')->value('id');
        $vendor_id = Lab::where('id',$data['lab_id'])->value('vendor_id');
        
        $testcasedata = $this->getTestAmount($data['test_id'], $data['test_type'], $data['lab_id'], $couponId);
        $data['vendor_id'] =  $vendor_id ?? $adminId;
        $data['amount'] = $testcasedata['amount'];
        $data['test_discount_amount'] = $testcasedata['test_discount_amount'];
        $data['total_amount'] = $testcasedata['total_amount'];
        $data['duration'] = $testcasedata['duration'];
        $data['discount_type'] = $testcasedata['discount_type'];
        $data['discount_value'] = $testcasedata['discount_value'];
        $data['discount_amount'] = $testcasedata['discount_amount'];
        $data['tax_amount'] = $testcasedata['tax_amount'];
        $data['tax_list'] = $testcasedata['tax_list'];
        $data['status'] = 'pending';
        
        $data['coupon_amount'] = $testcasedata['coupon_amount'] ?? 0;
        $data['coupon'] = $testcasedata['coupon'] ?? null;
        $data['coupon_id'] = $testcasedata['coupon_id'] ?? null;
        $data['by_suggestion'] = $data['by_suggestion'] ?? 0;
        $data['symptoms'] = $data['symptoms'] ?? null;
        $appointment = Appointment::create($data);
        if (!empty($appointment) && $appointment->by_suggestion == 1) {
            Prescription::where('id', $data['prescription_id'])
                ->update([
                    'is_notify' => 0,
                    'status' => 0
                ]);
        }        
        $this->logActivity('create',$appointment,'appointment_create');
        if ($appointment->test_type == 'test_package') {
            
            $packageMapping = PackageManagement::where('id', $appointment->test_id)
                ->whereHas('packageCatlogMapping', function ($q) {
                    $q->whereNotNull('catalog_id'); 
                })
                ->with('packageCatlogMapping') 
                ->first(); 
        
            
            if ($packageMapping && $packageMapping->packageCatlogMapping->isNotEmpty()) {
                $appointmentPackageMapping = [];
        
                
                foreach ($packageMapping->packageCatlogMapping as $mapping) {
                    $appointmentPackageMapping[] = [
                        'appointment_id' => $appointment->id,
                        'package_id' => $appointment->test_id,
                        'test_id' => $mapping->catalog_id, 
                        'price' => $mapping->catalog->price, 
                        'status' => 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
        
               
                AppointmentPackageMapping::insert($appointmentPackageMapping);
            }
        }        
        $appointmenttransaction = AppointmentTransaction::create([
            'appointment_id' => $appointment->id,
            'discount_amount' => $data['discount_amount'],
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'coupon_amount' => $data['coupon_amount'],
            'coupon' => $data['coupon'],
            'coupon_id' => $data['coupon_id'],
            'total_tax_amount' => $data['tax_amount'],
            'tax' => $data['tax_list'] ? json_encode($data['tax_list']) : null,
            'total_amount' => $data['total_amount'],
            'payment_status' => 'pending',
            'payment_type' => $data['payment_type'] ?? null,
        ]);
        $this->logActivity('create',$appointmenttransaction,'appointment_transaction_create');
        if ($request->hasFile('medical_report')) {
            storeMediaFile($appointment, $request->file('medical_report'), 'medical_report');
        }
        $activity_data = [
            'activity_type' => 'add_appointment',
            'notification_type'=>'new_appointment',
            'appointment_id' => $appointment->id,
            'customer' => $appointment->customer->full_name ?? '-',
            'test_name' => $appointment->getTestAttribute()->name ?? '-',
        ];
        AppointmentActivity::create([
            'appointment_id' => $appointment->id,
            'activity_type' => 'add_appointment',
            'activity_message' => __('messages.new_Appointment_created'),
            'activity_data' => json_encode($activity_data),
            'activity_date' => now()
        ]);
     $this->sendNotification($activity_data);
        $messages = __('messages.create_form');
        if ($request->is('api/*')) {
            $responseData = new AppointmentDetailResource($appointment);
            $response = [
                'message' => $messages,
                'appointment_id' => $appointment->id,
                'data' => $responseData
            ];
            return comman_custom_response($response);
        }
       
        return redirect()->route('backend.appointments.index')->with('success', $messages);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Appointment::findOrFail($id);
        $module_title=__('messages.edit_appointment');
    return view('appointment::backend.edit', compact('data','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request)
    {
        $data = $request->all();
        
        $id = $data['id'];
        $appointment = Appointment::with('appointmentCollectorMapping','transactions')->findOrFail($id);
        if(!$appointment){
            $message = __('messages.not_found');
            return response()->json([
                'success' => false,
                'message' => $message
            ], 404);
        }
        if($appointment->status != $data['status']) {
            $activity_type = 'update_appointment_status';
        }
        if($data['status'] == 'cancelled'){
            $appointment->status = 'cancelled';
            $appointment->cancellation_reason = $data['cancellation_reason'] ?? null;
            $activity_type = 'cancel_appointment' ?? $activity_type;
            $activity_message = __('messages.cancle_appointment_successfully');
        }
        if($data['status'] == 'reschedule'){
            
            $appointment->reschedule_reason = $data['reschedule_reason'] ?? null;
        }
        if($data['status'] == 'accept'){
           
            $activity_type = 'accept_appointment' ?? $activity_type;
            $activity_message = __('messages.accept_appointment_successfully');
        }

        if($data['status'] == 'on_going'){
 
            $activity_type = 'on_going_appointment' ?? $activity_type;
            $activity_message = __('messages.on_going_appointment_successfully');
        }

        if($data['status'] == 'rejected'){
            if($appointment->appointmentCollectorMapping()->count() > 0){
                $appointment->appointmentCollectorMapping()->delete();
                $data['status'] = 'accept';
            }
            $activity_type = 'reject_appointment' ?? $activity_type;
            $activity_message = __('messages.reject_appointment_successfully');
        }
        if($data['status'] == 'in_progress') {
          
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
          
            $appointmentOtpMapping = AppointmentOtpMapping::updateOrCreate([
                'appointment_id' => $appointment->id,
            ],[
                'collector_id' => $appointment->appointmentCollectorMapping->collector_id,
                'otp' => $otp,
                'otp_generated_at' => now(),
            ]);
            $this->logActivity('create',$appointmentOtpMapping,'appointment_otp_mapping_create');
          
            $activity_data = [
                'activity_type' => 'otp_generated',
                'notification_type'=>'otp_generated',
                'appointment_id' => $appointment->id,
                'appointment' => $appointment,
                'otp' => $otp,
            ];
            AppointmentActivity::create([
                'appointment_id' => $appointment->id,
                'activity_type' => 'otp_generated',
                'activity_message' => __('messages.otp_generated_appointment_successfully'),
                'activity_data' => json_encode($activity_data),
                'activity_date' => now()
            ]);
            $this->sendNotification($activity_data);
            $activity_type = 'in_progress_appointment' ?? $activity_type;
            $activity_message = __('messages.in_progress_appointment_successfully');
          
          
        }
        if($data['status'] == 'completed'){
            $activity_type = 'completed_appointment' ?? $activity_type;
            $activity_message =  __('messages.complete_appointment_successfully');
        }
        
        $old_status = $appointment->status;
        $appointment->update($data);
        $this->logActivity('update',$appointment,$activity_type ?? 'update_appointment_status');
        if($old_status != $data['status'] ){
            $appointment->old_status = $old_status;
            $activity_data = [
                'activity_type' => $activity_type,
                'activity_message' => $activity_message ?? __('messages.appointment_status_update'),
                'notification_type'=> $activity_type,
                'appointment_id' => $appointment->id,
            ];
          
          $this->sendNotification($activity_data);
        }
        AppointmentActivity::create([
            'appointment_id' => $appointment->id,
            'activity_type' => $activity_type,
            'activity_message' => $activity_message ?? __('messages.appointment_status_update'),
            'activity_data' => isset($activity_data) ? json_encode($activity_data) : null,
            'activity_date' => now()
        ]);
        $message = __('messages.update_form');

        if($request->is('api/*')) {
            $responseData = new AppointmentDetailResource($appointment);
            $response = [
                'message' => $message,
                'appointment_id' => $appointment->id,
                'data' => $responseData
            ];
            return comman_custom_response($response);
		}
        return redirect()->route('backend.appointments.index', $appointment->id)->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Appointment::findOrFail($id);
        $data->delete();
        $this->logActivity('delete',$data,'appoinment_delete');
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = Appointment::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'appointment_restore');
        $message = __('messages.restore_form');
        return response()->json(['message' => $message]);
        
    }

    public function forceDelete($id)
    {
        $data = Appointment::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'appointment_force_delete');
        $message = __('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }

    public function assignCollector(Request $request)
    {
        
        try {
            $request->validate([
                'collector_id' => 'required|exists:users,id'
            ]);            
            $appointment = Appointment::find($request->id);
            if(!$appointment){
                $message = __('messages.not_found');
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 404);
            }

            if (!empty($appointment->rejected_id)) {
                // Get commission data related to the appointment
                $commission = $appointment->commission()->where('commissionable_id', $appointment->id)->first();
    
                if ($commission) {
                    // Check if the collector ID is different from the employee ID in the commission record
                    if ($commission->employee_id != $request->collector_id) {
                        // Update commission with new collector ID
                        $commission->update(['employee_id' => $request->collector_id]);
                    }
                }
            }

            if ($request->collector_id != null) {

                $AppointmentCollectorMapping = AppointmentCollectorMapping::where('appointment_id',$appointment->id)->first();
                $assign_to_collector = [
                    'appointment_id' => $appointment->id,
                    'collector_id' => $request->collector_id,
                ];
            if ($AppointmentCollectorMapping){
                $AppointmentCollectorMapping->update($assign_to_collector);
                $this->logActivity('update',$AppointmentCollectorMapping,'appointment_collector_mapping_update');
            }
            else{
                $data = AppointmentCollectorMapping::Create( $assign_to_collector ); 
                $this->logActivity('create',$data,'appointment_collector_mapping_create');
            }  
                
            
                $activity_type = 'collector_assigned';
                $activity_message = __('messages.assigned_to_collector');
            }            
            
            $appointment->status = __('messages.lbl_accept');
            $appointment->save();
          
           
            $collector = User::find($request->collector_id);
            $activity_data = [
                'activity_type' => $activity_type,
                'notification_type'=> $activity_type,
                'appointment_id' => $appointment->id,
                'activity_message' => $activity_message,
                'collector' => $collector->first_name . ' ' . $collector->last_name,
                'collector_id'=>$request->collector_id,

            ];
            AppointmentActivity::create([
                'appointment_id' => $appointment->id,
                'activity_type' => $activity_type,
                'activity_message' => $activity_message,
                'activity_data' => json_encode($activity_data),
                'activity_date' => now()
            ]);
            $this->sendNotification($activity_data);
            $message = __('messages.assigned_to_collector');
            if ($request->is('api/*')) {
                $responseData = new AppointmentDetailResource($appointment);
                $response = [
                    'message' => $message,
                    'appointment_id' => $appointment->id,
                    'data' => $responseData
                ];
                return comman_custom_response($response);
            }
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function appointmentDetails($id)
    {
        $data = Appointment::with([
            'lab',                    
            'catlog', 
            'package',                 
            'customer',               
            'vendor',                 
            'appointmentCollectorMapping.collector', 
            'transactions',          
        ])->MyAppointment()->where('id',$id)->first();
        if ($data == null) {
            return redirect()->route('backend.appointments.index')->with('error', __('messages.record_not_found'));
        }
        
        $data->status_color = match ($data->status) {
            'pending' => 'warning',
            'accept' => 'info',
            'in_progress' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'rejected' => 'danger',
            default => 'secondary'
        };

      
        $data->payment_status_color = match ($data->payment_status) {
            'paid' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary'
        };
      
        $module_title=__('messages.appointment_details');
        return view('appointment::backend.details', compact('data','module_title'));
    }

    public function otpVerification(Request $request)
    {
        $data = $request->all();
        $appointment = Appointment::with('appointmentCollectorMapping','otpMapping')->find($data['id']);
        if (!$appointment){
            $message = __('messages.not_found');
            return response()->json([
                'success' => false,
                'message' => $message
            ], 404);
        }
            $otp = $data['otp'];
            $collector_id = $data['collector_id'];
            $otpMapping = $appointment->otpMapping;
            if ($otpMapping->otp == $otp && $otpMapping->collector_id == $collector_id) {
                
                $message = __('messages.otp_verified');
                $activity_data = [
                    'activity_type' => 'otp_verified',
                    'notification_type'=> 'otp_verified',
                    'appointment_id' => $appointment->id,
                    'collector' => $appointment->appointmentCollectorMapping()->collector->full_name ?? '-',
                ];
                AppointmentActivity::create([
                    'appointment_id' => $appointment->id,
                    'activity_type' => 'otp_verified',
                    'activity_message' => __('messages.otp_verified_successfully'),
                    'activity_data' => json_encode($activity_data),
                    'activity_date' => now()
                ]);
           $this->sendNotification($activity_data);
                if ($request->is('api/*')) {
                    $responseData = new AppointmentDetailResource($appointment);
                    $response = [
                        'message' => $message,
                        'appointment_id' => $appointment->id,
                        'data' => $responseData
                    ];
                    return comman_custom_response($response);
                }
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                $message = __('messages.otp_invalid');
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
        
    }
    public function acceptRejectAppointment(Request $request)
    {
        
        $appointment = Appointment::with('appointmentCollectorMapping','transactions')->find($request->appointment_id);
        
        if (!$appointment) {
            return response()->json(['message' => __('messages.appointment_not_found')], 404);
        }
    
        if ($request->submission_status == 'accept') {
           
            $appointment->submission_status = $request->submission_status ?? 'accept';
            $appointment->save();
            $notification_type='test_case_received';
            $activity_data = [
                'activity_type' => $notification_type,
                'notification_type'=>$notification_type,
                'appointment_id' => $appointment->id,
                'test_name' => $appointment->getTestAttribute()->name ?? '-',
                ];
                AppointmentActivity::create([
                    'appointment_id' => $appointment->id,
                    'activity_type' => $notification_type,
                    'activity_message' => __('messages.test_case_received'),
                    'activity_data' => json_encode($activity_data),
                    'activity_date' => now()
                ]);
            $this->sendNotification($activity_data);
            return response()->json(['message' => __('messages.appointment_accept_status_update')]);
        }
    
       
        if ($request->submission_status == 'reject') {
            $appointment->submission_status = $request->submission_status ?? 'reject';
            $appointment->save();
            
            $newAppointment = $appointment->replicate();
            $newAppointment->status = 'pending';
            $newAppointment->rejected_id = $appointment->id;
            $newAppointment->submission_status = 'pending';
            $newAppointment->save();

        
            $transaction = $appointment->transactions; 

            if ($transaction) { 
                $newTransaction = $transaction->replicate();
                $newTransaction->appointment_id = $newAppointment->id;
                $newTransaction->save();
            }

            if ($appointment->commissionsdata) {
                foreach ($appointment->commissionsdata as $commission) {
                    $newCommission = $commission->replicate();
                    $newCommission->commissionable_id = $newAppointment->id; // Update to new appointment ID
                    $newCommission->save();
                }
            }

           $notification_type='test_case_not_received';
           $activity_data = [
            'activity_type' => $notification_type,
            'notification_type'=>$notification_type,
            'appointment_id' => $appointment->id,
            'appointment' => $appointment,
            ];
            AppointmentActivity::create([
                'appointment_id' => $appointment->id,
                'activity_type' => $notification_type,
                'activity_message' => __('messages.test_case_declined_successfully'),
                'activity_data' => json_encode($activity_data),
                'activity_date' => now()
            ]);
            $this->sendNotification($activity_data);
            $appointment->transactions()->delete();
            $appointment->appointmentCollectorMapping()->delete();
            $appointment->otpMapping()->delete();
            $appointment->commissionsdata()->delete();
            $appointment->delete();
        
            return response()->json(['message' => __('messages.new_appointment_create_after_reject')]);
        }

          
        return response()->json(['message' => __('messages.invalid_action')], 400);  
    }    
    public function updateTestCaseStatus(Request $request)
    {
        $appointment = Appointment::find($request->appointment_id);

        if (!$appointment) {
            return response()->json(['message' => __('messages.appointment_not_found')], 404);
        }

        $appointment->test_case_status = $request->test_case_status ?? null;
        $appointment->save();

        $notificationTypes = [
            'in_progress' => 'test_in_progress',
            'awaiting_validation' => 'test_awaiting_validation',
            'validated' => 'test_validated',
            'report_generated'=> 'report_generated',
        ];
        $notification_type = $notificationTypes[$appointment->test_case_status] ??'' ;
        if ($notification_type) {
            $activity_data = [
                'activity_type' => $notification_type,
                'notification_type' => $notification_type,
                'appointment_id' => $appointment->id,
                'appointment' => $appointment,
            ];
            AppointmentActivity::create([
                'appointment_id' => $appointment->id,
                'activity_type' => $notification_type,
                'activity_message' => $notification_type,
                'activity_data' => json_encode($activity_data),
                'activity_date' => now()
            ]);
            $this->sendNotification($activity_data);
        
            
            if ($notification_type === 'report_generated') {
                $activity_data['notification_type'] = 'report_sent';
                $this->sendNotification($activity_data);
            }
        }

        return response()->json(['message' => __('messages.test_case_status_update')]);
    }

    public function createInvoice(Request $request,$id)
    {
        $data = Appointment::with([
            'lab',                    
            'catlog', 
            'package',                  
            'customer',               
            'vendor',                 
            'appointmentCollectorMapping.collector', 
            'transactions',           
        ])->findOrFail($id);
        if ($request->is('api/*')) {
            $pdf = PDF::loadHTML(view("appointment::backend.invoice", ['data' => $data])->render())
                ->setOptions(['defaultFont' => 'sans-serif']);

            $baseDirectory = storage_path('app/public');
            $highestDirectory = collect(File::directories($baseDirectory))->map(function ($directory) {
                return basename($directory);
            })->max() ?? 0;
            $nextDirectory = intval($highestDirectory) + 1;
            while (File::exists($baseDirectory . '/' . $nextDirectory)) {
                $nextDirectory++;
            }
            $newDirectory = $baseDirectory . '/' . $nextDirectory;
            File::makeDirectory($newDirectory, 0777, true);

            $filename = 'invoice_' . $id . '.pdf';
            $filePath = $newDirectory . '/' . $filename;

            $pdf->save($filePath);


            $url = url('storage/' . $nextDirectory . '/' . $filename);

            if (!isset($data) || !$data->first()->customer_id) {
                return response()->json(['error' => __('messages.userid_not_found')], 404);
            }
            $customer_id = $data->first()->customer_id;
            $user = User::findOrFail($customer_id);
            $email = $user->email;
            $subject = __('messages.your_invoice');
            $details = __('appointment.invoice_find') . $url;

            Mail::to($email)->send(new InvoiceEmail($data, $subject, $details, $filePath, $filename));
            if (!empty($url)) {
                return response()->json(['status' => true, 'link' => $url], 200);
            } else {
                return response()->json(['status' => false, 'message' => __('messages.url_not_found')], 404);
            }
        } else {
        $pdf = Pdf::loadView('appointment::backend.invoice', ['data' => $data]);
        return $pdf->download('invoice_' . $data->id . '.pdf');
        }
    }

    public function uploadImages(Request $request)
    {
           
        $data = $request->all();
        
        $appointment = Appointment::where('id', $data['id'])->first();
        $uploadedFiles = [];
        if ($request->hasFile('report_generate')) {
            storeMediaFile($appointment, $request->file('report_generate'), 'report_generate');
        }

        return response()->json(['success' => true, 'files' => $uploadedFiles]);
    }
    public function deleteReport(Request $request)
    {
        $media = Media::find($request->id);
    
        if ($media) {
            $media->delete();
            return response()->json(['success' => true]);
        }
    
        return response()->json(['success' => false, 'message' => __('messages.file_not_found')]);
    }
    

}
