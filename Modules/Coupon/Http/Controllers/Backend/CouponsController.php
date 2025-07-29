<?php

namespace Modules\Coupon\Http\Controllers\Backend;
use App\Models\User;
use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Appointment\Models\AppointmentTransaction;
use Modules\Coupon\Models\Coupon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Coupon\Http\Requests\CouponRequest;
use App\Trait\ModuleTrait;
use Modules\Vendor\Models\Vendor;
use Modules\Coupon\Models\CouponTestMapping;
use Modules\Coupon\Models\CouponPackageMapping;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\Lab\Models\Lab;
use App\Models\Setting;
class CouponsController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\CouponExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            __('messages.coupons'), // module title
            'coupons', // module name
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
         // Filter values
         $filter = [
             'status' => $request->status,
             'vendor_id' => $request->vendor_id, // To filter by vendor
         ];

         $selectedVendor = $request->vendor_id;
         $type = 'coupons';
         $module_action = __('messages.list');
     $module_title=  __('messages.coupons');
         $export_import = true;
         $export_columns = [
             [
                 'value' => 'coupon_code',
                 'text' => __('messages.lbl_coupon_code'),
             ],
             [
                'value' => 'lab',
                'text' => __('messages.lbl_lab'),
             ],
             [
                'value' => 'vendor',
                'text' => __('messages.lbl_vendor'),
             ],
             [
                'value' => 'discount_value',
                'text' => __('messages.lbl_discount_value'),
             ],
             
             [
                'value' => 'start_at',
                'text' => __('messages.lbl_start_at'),
             ],
             [
                'value' => 'end_at',
                'text' => __('messages.lbl_end_at'),
             ],
             [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
             ],
         ];
         $export_url = route('backend.coupons.export');
     
         return view('coupon::backend.coupon.index', compact(
             'module_action',
             'filter',
             'export_import',
             'export_columns',
             'export_url',
             'selectedVendor',
             'module_title',
             'type'
         ));
     }
     public function index_list(Request $request)
     {
        $testType = $request->test_type;
        $testId = $request->test_id;
        $labId = $request->lab_id;
        $customerId = $request->customer_id ?? auth()->id();
        $searchTerm = $request->input('search', null);
    
        // Get active and non-expired coupons
        $coupons = Coupon::where('status', 1)
            ->where('end_at', '>', now());
    
        // Filter by lab_id if provided
        if (!empty($labId)) {
            $coupons->where('lab_id', $labId);
        }
    
        // Filter by test type and test ID
        $coupons->where(function ($query) use ($testType, $testId) {
            $query->whereJsonContains('applicability', 'all');
    
            if ($testType === 'test_case') {
                $query->orWhereHas('tests', function ($q) use ($testId) {
                    $q->where('test_id', $testId);
                })->whereJsonContains('applicability', 'specific_tests');
            }
    
            if ($testType === 'test_package') {
                $query->orWhereHas('packages', function ($q) use ($testId) {
                    $q->where('package_id', $testId);
                })->whereJsonContains('applicability', 'specific_packages');
            }
        });
    
        if (!empty($searchTerm)) {
            $coupons->where('coupon_code', 'LIKE', "%{$searchTerm}%");
        }
    
        $coupons = $coupons->get();
    
        // Get appointment transactions with related appointments
        $appointmentTransactions = AppointmentTransaction::with('appointment')
            ->whereIn('coupon_id', $coupons->pluck('id'))
            ->get();
    
        $filteredCoupons = $coupons->filter(function ($coupon) use ($appointmentTransactions, $customerId) {
            // Count total usage
            $usageCount = $appointmentTransactions->where('coupon_id', $coupon->id)->count();
            
            // Count customer usage
            $customerUsageCount = $appointmentTransactions->where('coupon_id', $coupon->id)
                ->where('appointment.customer_id', $customerId)
                ->count();
    
            // Check if usage limits are reached
            $totalLimitReached = $usageCount >= $coupon->total_usage_limit;
            $customerLimitReached = $customerUsageCount >= $coupon->per_customer_usage_limit;
    
            // Update coupon status if total limit is reached
            if ($totalLimitReached && $coupon->status) {
                $coupon->update(['status' => 0]);
            }
    
            // Allow only if usage is within both limits
            return !$totalLimitReached && !$customerLimitReached;
        });
    
        if ($request->is('api/*')) {
            return response()->json([
                'status' => true,
                'data' => $filteredCoupons->values(),
                'message' => __('messages.coupon_list'),
            ], 200);
        }
    
        return response()->json([
            'coupons' => $filteredCoupons->values(),
            'status' => true
        ]);
     }
    


    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.coupons'); // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Coupon::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Coupon::MyCoupon();
        
        $user = auth()->user();

        // If the logged-in user is a vendor, filter by vendor_id
        if ($user->user_type == 'vendor') {  // Adjust based on your role management
            $query->where('vendor_id', $user->id);
        }

        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }

        if (!empty($filter['vendor_id'])) {
            $query->where('vendor_id', $filter['vendor_id']);
        }

        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }   

        return $datatable->eloquent($query)
          ->editColumn('name', fn($data) => $data->name)
          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" data-type="coupons" onclick="dataTableRowCheck('.$data->id.',this)">';
          })
          ->addColumn('action', function ($data) {
              return view('coupon::backend.coupon.action', compact('data'));
          })
          ->editColumn('status', function ($row) {
            $checked = $row->status ? 'checked="checked"' : ''; // Check if the status is true
            $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
        
            return '
                <div class="form-check form-switch ">
                    <input type="checkbox" data-url="' . route('backend.coupons.update_status', $row->id) . '" 
                           data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                           id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                           ' . $checked . ' ' . $disabled . '>
                </div>
            ';
        })  
        ->addColumn('vendor', function($data) {
            $data = User::find($data->vendor_id);
            if (!$data) {
                return ' '; // Return dash if no vendor found
            }
            return view('user::backend.users.user_details', compact('data'));
        })
        ->editColumn('discount_value', function($data) {
            if ($data->discount_type === 'percentage') {
                return $data->discount_value . ' %';
            }
           
            return \Currency::format($data->discount_value);
        })
        ->editColumn('start_at', function ($data) {
            return Setting::formatDate($data->start_at);
        })
        ->editColumn('end_at', function ($data) {
            return Setting::formatDate($data->end_at);
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
            $query->leftJoin('labs as l', 'coupons.lab_id', '=', 'l.id')
                  ->orderBy("l.name", $order);
        })
          ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
          ->rawColumns(['action', 'status','vendor', 'check'])
          ->orderColumns(['id'], '-:column $1')
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
        
        $vendors = User::where('user_type', 'vendor')->get();
        $module_title=__('messages.new_coupon');
        // Pass the vendors, tests, and packages to the view
        return view('coupon::backend.coupon.create', compact('vendors','module_title'));
    }

    public function store(CouponRequest $request)
    {
        $data = $request->all();
        $applicability = $request->input('applicability');
        if(empty($applicability))
        {
            $data['applicability']='all';
        }
        
        $coupon = Coupon::create($data);
        $this->logActivity('create',$coupon,'coupon_create');



       // Insert into coupon_test_mappings if 'specific_tests' is selected
        if (!empty($applicability)) {
            if (in_array('specific_tests', $applicability)) {
                $tests = (array) $request->input('test_id', []);  // Ensure it's an array
                foreach ($tests as $test) {
                    CouponTestMapping::create([
                        'coupon_id' => $coupon->id,
                        'test_id' => $test,
                    ]);
                }
            }

     // Insert into coupon_package_mappings if 'specific_packages' is selected
        if (in_array('specific_packages', $applicability)) {
            $packages = (array) $request->input('package_id', []);  // Ensure it's an array
            foreach ($packages as $package) {
                CouponPackageMapping::create([
                    'coupon_id' => $coupon->id,
                    'package_id' => $package,
                ]);
            }
        }
    }

    
        $message=__('messages.record_add');
        return redirect()->route('backend.coupons.index')->with('success',$message);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $coupon = Coupon::MyCoupon()->with(['packages.package', 'tests.test'])->where('id',$id)->first(); // Load related packages & tests
        if ($coupon == null) {
            return redirect()->route('backend.coupons.index')->with('error', __('messages.record_not_found'));
        }
        $vendors = User::where('user_type', 'vendor')->get();
        $labs=Lab::where('status', 1)->get();
        $tests = CatlogManagement::where('lab_id',$coupon->lab_id)->select('id', 'name','price')->get();
        $packages = PackageManagement::where('lab_id',$coupon->lab_id)->select('id', 'name','price')->get();
        $module_title = __('messages.edit_coupon');
    return view('coupon::backend.coupon.edit', compact('coupon','vendors','labs','tests','packages','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CouponRequest $request, Coupon $coupon)
    {
        try {
            $requestData = $request->all();
            $applicability = $request->input('applicability');
            
            if(empty($applicability)) {
                $requestData['applicability'] = 'all';
            }
    
            // Check for unique coupon code excluding current coupon
            $existingCoupon = Coupon::where('coupon_code', $requestData['coupon_code'])
                ->where('id', '!=', $coupon->id)
                ->first();
    
            if ($existingCoupon) {
                return redirect()->back()
                    ->withInput()
                    ->withError(__('messages.coupon_code_exists'));
            }
    
            $coupon->update($requestData);
            $this->logActivity('update', $coupon, 'coupon_update');
    
            // Handle test mappings
            if (!empty($applicability)) {
                CouponTestMapping::where('coupon_id', $coupon->id)->delete();
                
                if (in_array('specific_tests', $applicability) && !empty($request->input('test_id'))) {
                    foreach ($request->input('test_id') as $test) {
                        CouponTestMapping::create([
                            'coupon_id' => $coupon->id,
                            'test_id' => $test,
                        ]);
                    }
                }
    
                // Handle package mappings
                CouponPackageMapping::where('coupon_id', $coupon->id)->delete();
                
                if (in_array('specific_packages', $applicability) && !empty($request->input('package_id'))) {
                    foreach ($request->input('package_id') as $package) {
                        CouponPackageMapping::create([
                            'coupon_id' => $coupon->id,
                            'package_id' => $package,
                        ]);
                    }
                }
            }
    
            return redirect()->route('backend.coupons.index')
                ->with('success', __('messages.update_form'));
    
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withError(__('messages.error_updating_coupon'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Coupon::findOrFail($id);
        $data->delete();
        $this->logActivity('delete',$data,'coupon_delete');
        $message = __('messages.delete_form', ['form' => __('tax.title')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function update_status(Request $request, Coupon $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }
    public function restore($id)
    {
        $data = Coupon::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'coupon_restore');
        $message=__('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = Coupon::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'coupon_force_delete');
        $message=__('messages.permanent_delete_form');
        return response()->json(['message' => $message, 'status' => true], 200);
    }
    public function getTestsAndPackages($vendorId)
{
  
    $tests = CatlogManagement::where('vendor_id', $vendorId)->pluck('name', 'id');
    $packages = PackageManagement::where('vendor_id', $vendorId)->pluck('name', 'id');



    return response()->json([
        'success' => true, 
        'tests' => $tests, 
        'packages' => $packages
    ]);
}
}
