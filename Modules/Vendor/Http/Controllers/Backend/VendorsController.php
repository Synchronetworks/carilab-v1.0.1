<?php

namespace Modules\Vendor\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Modules\Vendor\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Vendor\Http\Requests\VendorRequest;
use App\Trait\ModuleTrait;
use Modules\Tax\Models\Tax;
use Modules\World\Models\Country;
use Modules\World\Models\City;
use Modules\World\Models\State;
use  App\Models\User;
use App\Models\UserTaxMapping;
use App\Models\UserCommissionMapping;
use Illuminate\Support\Facades\Hash;
use Modules\Lab\Models\Lab;
use Modules\Collector\Models\Collector;
use Illuminate\Support\Facades\Route;
use Modules\Commision\Models\Commision;
use Currency;
use App\Trait\NotificationTrait;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\Review\Models\Review;
use Modules\Helpdesk\Models\Helpdesk;
use Modules\User\Http\Requests\PasswordRequest;
use Modules\Commision\Models\CommissionEarning;
use Modules\Subscriptions\Models\Subscription;
use Illuminate\Support\Facades\Crypt;
class VendorsController extends Controller
{
    protected string $exportClass = '\App\Exports\VendorExport';
    use NotificationTrait;
    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            __('messages.vendor'), // module title
            'vendors', // module name
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

        $module_action = 'List';

        $export_import = true;
        $export_columns = [
            [
                'value' => 'vendor',
                'text' => __('messages.vendor'),
            ],
            [
                'value' => 'mobile',
                'text' => __('messages.lbl_conatct_number'),
            ],
            [
                'value' => 'commission',
                'text' => __('messages.commissions'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
            [
                'value' => 'labs_count',
               'text' => __('messages.labs'),
            ],
            [
              'value' => 'collectors_count',        
              'text' => __('messages.collectors'),
            ],
            
        ];
        $export_url = route('backend.vendors.export');

        return view('vendor::backend.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url'));
    }
    public function index_list(Request $request,$approval_status)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';

        $export_import = true;
        $export_columns = [
            [
                'value' => 'vendor',
                'text' => __('messages.vendor'),
            ],
            [
                'value' => 'mobile',
                'text' => __('messages.lbl_conatct_number'),
            ],
            [
                'value' => 'commission',
                'text' => __('messages.commissions'),
            ],
            [
                'value' => 'labs_count',
                'text' => __('messages.lbl_lab_count'),
            ],
            [
                'value' => 'collectors_count',
                'text' => __('messages.total_collector_count'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
        ];
       
        $export_url = route('backend.vendors.export');

        return view('vendor::backend.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url','approval_status'));
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'Vendor'; // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(User::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
       
        


        $query = User::MyVendor()->where('user_type', 'vendor'); // Add relationships
        if($request->approval_status == null){
            $query = $query->where('status', 1);
        }
        if($request->approval_status == 'pending'){
            $query = $query->where('status', 0);
        }
        
        $query = $query->with(['userCommissionMapping', 'country', 'state', 'city']);
        $filter = $request->filter; 

        if (isset($filter['name'])) {
            $fullName = $filter['name'];

            $query->where(function ($query) use ($fullName) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$fullName%"]);
            });
        }

        if (isset($filter['email'])) {

            $query->where('email', $filter['email']);
        }

         // Commission Filter
            if (!empty($filter['commission_id'])) {
                $query->whereHas('userCommissionMapping', function($q) use ($filter) {
                    $q->where('commission_id', $filter['commission_id']);
                });
            }

            // Tax Filter
            if (!empty($filter['tax_id'])) {
                $query->whereHas('userTaxMapping', function($q) use ($filter) {
                    $q->where('tax_id', $filter['tax_id']);
                });
            }

            // Lab Filter
            if (!empty($filter['lab_id'])) {
                $query->whereHas('vendorLabs', function($q) use ($filter) {
                    $q->where('id', $filter['lab_id']);
                });
            }

          

              // Collector Filter
            if (!empty($filter['collector_id'])) {
                $query->whereHas('vendorCollectormapping', function($q) use ($filter) {
                    $q->where('collector_id', $filter['collector_id']);
                });
            }

            // Vendor Filter (for searching/filtering by vendor name)
            if (!empty($filter['vendor_id'])) {
                $query->where(function($q) use ($filter) {
                    $q->where('id', $filter['vendor_id']);
                });
            }
            // Gender Filter
            if (!empty($filter['gender'])) {
                $query->where('gender', $filter['gender']);
            }
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        return $datatable->eloquent($query)
            ->editColumn('name', function ($data) {
                return view('user::backend.users.user_details', compact('data'));
            })

            ->filterColumn('name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('first_name', 'like', '%' . $keyword . '%')->orWhere('last_name', 'like', '%' . $keyword . '%')->orWhere('email', 'like', '%' . $keyword . '%');
                }
            })

            ->orderColumn('name', function ($query, $order) {
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order");
            }, 1)

            ->addColumn('contact_number', function ($data) {
                return $data->mobile ?? '-';
            })
            ->filterColumn('contact_number', function ($query, $keyword) {
                $query->where('mobile', 'like', "%{$keyword}%");
            })
            ->addColumn('commission', function ($data) {
                if ($data->userCommissionMapping->isNotEmpty()) {
                    return $data->userCommissionMapping->map(function ($commission) {
                        return $commission->commission_type == 'Percentage' ? $commission->commission.'%' : Currency::format($commission->commission);
                    })->implode(', '); // Convert array to string
                }
                
                return '-';
            })            
            ->addColumn('labs_count', function ($data) {
                $count = Lab::where('vendor_id', $data->id)->count(); // Adjust model name if different
                return '<a href="' . route('backend.labs.index', ['vendor_id' => encrypt($data->id)]) . '">' . 
                    $count . ' Labs</a>';
            })
            ->addColumn('collectors_count', function ($data) {
                $count = User::whereHas('collectorVendorMapping', function($query) use ($data) {
                    $query->where('vendor_id', $data->id);
                })->count(); // Adjust model name if different
                return '<a href="' . route('backend.collectors.index', ['vendor_id' => encrypt($data->id)]) . '">' . $count . ' Collectors</a>';
            })
            ->addColumn('country_id', function ($data) {
                return $data->country->name ?? '-';
            })
            ->addColumn('state_id', function ($data) {
                return $data->state->name ?? '-';
            })
            ->addColumn('city_id', function ($data) {
                return $data->city->name ?? '-';
            })
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-'.$data->id.'" name="datatable_ids[]" value="'.$data->id.'" data-type="users" onclick="dataTableRowCheck('.$data->id.',this)">';
            })
            ->addColumn('action', function ($data) {
                return view('vendor::backend.action', compact('data'));
            })
            ->editColumn('status', function ($data) {
                return $data->getStatusLabelAttribute();
            })

            ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
            ->rawColumns(['action', 'status', 'check', 'labs_count', 'collectors_count','subject'])
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
         $taxes = Tax::where('status', 1)->get();
         $countries = Country::where('status', 1)->get(); // Fetch active countries
         $states = State::where('status', 1)->get(); // Fetch active states
         $cities = City::where('status', 1)->get(); // Fetch active cities
        $module_title=__('messages.new_vendor');
         return view('vendor::backend.create', compact('taxes', 'countries', 'states', 'cities','module_title'));
     }
     

     public function store(VendorRequest $request)
     {
        
         // Retrieve all request data
         $data = $request->all();
         if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
         // Add user_type for vendor
         $data['user_type'] = 'vendor'; // Set 'vendor' as the user type

         // Create vendor with data

         $vendor = User::create($data);
         $vendor->assignRole('vendor');
        if(Setting('vendor_commission_type') =='per_vendor'){

            if (isset($data['commission_type'], $data['commission'])) {
                $commissionMappingData = [
                    'user_id'         => $vendor->id,
                    'commission_type' => $data['commission_type'],
                    'commission'      => $data['commission'],
                    'created_by'      => auth()->id(),
                    'updated_by'      => auth()->id(),
                ];
                UserCommissionMapping::create($commissionMappingData);
            }
        }

        // Create the TaxMapping record for the vendor
        if (isset($data['tax_id'])) {
            foreach ($data['tax_id'] as $tax) {
                $taxMappingData = [
                    'user_id'      => $vendor->id,
                    'tax_id' => $tax,
                    'created_by'   => auth()->id(),
                    'updated_by'   => auth()->id(),
                ];
                UserTaxMapping::create($taxMappingData);
            }
        }
        $activity_data = [
            'notification_type'=>'vendor_registration',
            'vendor_id' => $vendor->id,
            'vendor' => $vendor,
        ];
     $this->sendNotification($activity_data);
        if ($request->hasFile('profile_image')) {
            storeMediaFile($vendor, $request->file('profile_image'), 'profile_image');
        }
         // Redirect with success message
         $messages=__('messages.record_add');
         return redirect()->route('backend.vendors.index')->with('success',$messages);
     }
     

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = User::with('userTaxMapping','userCommissionMapping')->where('user_type','vendor')->where('id',$id)->first();
        if($data == null){
            return redirect()->route('backend.vendors.index')->with('error',  __('messages.record_not_found'));
        }
        $commissionData = optional($data->userCommissionMapping->first());
        $data['tax'] = $data->userTaxMapping->pluck('tax_id')->toArray() ?? null;
        $data['commission_type'] = $data->userCommissionMapping && Setting('vendor_commission_type')=='per_vendor' ? $commissionData->commission_type : null;
        $data['commission'] = $data->userCommissionMapping && Setting('vendor_commission_type')=='per_vendor' ? $commissionData->commission : 0;
        $countries = Country::all(); // Get all countries
        $states = State::all(); // Get all states
        $cities = City::all(); // Get all cities
        $taxes = Tax::where('status', 1)->get();
        $module_title=__('messages.edit_vendor');
        return view('vendor::backend.edit', compact('data', 'countries', 'states', 'cities','taxes','module_title'));
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(VendorRequest $request, $id)
    {

        $data = $request->all();

        // Update vendor data
        $vendor = User::findOrFail($id);

        // Update vendor data
        $vendor->update($data);

        
        if(Setting('vendor_commission_type') =='per_vendor'){

            if (isset($data['commission_type'], $data['commission'])) {
                $commissionMappingData = [
                    'user_id'         => $vendor->id,
                    'commission_type' => $data['commission_type'],
                    'commission'      => $data['commission'],
                    'created_by'      => auth()->id(),
                    'updated_by'      => auth()->id(),
                ];
                UserCommissionMapping::create($commissionMappingData);
            }
        }

        // Update or create the Tax Mapping record
        if (isset($data['tax_id'])) {
            UserTaxMapping::where('user_id', $vendor->id)->delete();
            foreach ($data['tax_id'] as $tax) {
                UserTaxMapping::create([
                    'user_id' => $vendor->id,
                    'tax_id' => $tax,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }
        }
        if ($request->hasFile('profile_image')) {
            if ($vendor->getMedia('profile_image')->first()) {
                $vendor->clearMediaCollection('profile_image');
            }
            storeMediaFile($vendor, $request->file('profile_image'), 'profile_image');
        }
        $messages=__('messages.record_update');
        return redirect()->route('backend.vendors.index')->with('success', $messages);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = User::findOrFail($id);
        if($data == null){
            return redirect()->route('backend.vendors.index')->with('error',  __('messages.record_not_found'));
        }
        if($data->userTaxMapping()->exists()) {
            $data->userTaxMapping()->delete();
        }
        if($data->userCommissionMapping()->exists()) {
            $data->userCommissionMapping()->delete();
        }
       
        $data->delete();
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = User::withTrashed()->findOrFail($id);
        if($data == null){
            return redirect()->route('backend.vendors.index')->with('error',  __('messages.record_not_found'));
        }
        $data->restore();
        if($data->userTaxMapping()->exists()) {
            $data->userTaxMapping()->restore();
        }
        if($data->userCommissionMapping()->exists()) {
            $data->userCommissionMapping()->restore();
        }
        $messages=__('messages.restore_form');
        return response()->json(['message' => $messages]);
    }

    public function forceDelete($id)
    {
        $data = User::withTrashed()->findOrFail($id);
        if($data == null){
            return redirect()->route('backend.vendors.index')->with('error',  __('messages.record_not_found'));
        }
        if ($data->getMedia('profile_image')->first()) {
            $data->clearMediaCollection('profile_image');
        }
        $data->forceDelete();
        if($data->userTaxMapping()->exists()) {
            $data->userTaxMapping()->forceDelete();
        }
        if($data->userCommissionMapping()->exists()) {
            $data->userCommissionMapping()->forceDelete();
        }
        $message=__('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }

    public function details($id)
    {
        // Fetch collector details with relationships
        $data = User::MyVendor()->with([
           
            'collectorVendormapping.vendor',
            'lab.lab',
            'userCommissionMapping.commissions',
            'country',
            'state',
            'city',
            'collectorAppointmentmapping.appointment',
            'media',
            'subscriptiondata',
            'userTaxMapping.tax',
            'commission_earning',
        ])->where('user_type', 'vendor')
          ->where('id', $id)
          ->first();

        if ($data == null) {
            return redirect()->route('backend.vendors.index')->with('error',  __('messages.record_not_found'));
        }

        // Check if collector has appointment mappings
        $appointments = $data->collectorAppointmentmapping->pluck('appointment')->filter();
        $commissions = Commision::where('user_type','vendor')->get();
        // Calculaddte statistics
        $statistics = [
        'total_appointments' => $appointments->count(),
        'completed_appointments' => $appointments->where('status', 'completed')->count(),
        'pending_appointments' => $appointments->where('status', 'pending')->count(),
        'cancelled_appointments' => $appointments->where('status', 'cancelled')->count(),
        'totalPaidAmount' => $appointments->where('status', 'completed')->sum('amount'), // Total Earning
        'paidPayouts' => $data->commission_earning()->where('commission_status', 'paid')->sum('commission_amount'), // Total Paid Payout
        'pendingPayouts' => (new CommissionEarning)->rolewiseCommission($data,'unpaid'),
        'totalvendor' => $data->collectorVendormapping()->count(), // Total Vendor count
        'total_lab'=> Lab::where('vendor_id', $data->id)->count() ?? 0,
        'total_test' => CatlogManagement::where('vendor_id',$data->id)->count() ?? 0,
      

        ];
        $reviews = Review::whereHas('lab', function($qry) use($data){
            $qry->whereHas('vendor', function($q) use($data){
                $q->where('vendor_id', $data->id);
            });
        })->get();
        $totalReviews = $reviews->whereNotNull('review')->count();
        $averageRating = $reviews->avg('rating') ?? 0;

        // Get rating counts for each star level
        $ratingCounts = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];
        // Get recent activity (Last 5 Appointments)
        $recent_activity = $appointments->sortByDesc('created_at')->take(5);
        $module_title = $data->full_name ?? __('messages.vendor_detail');
        return view('vendor::backend.details', compact('commissions','totalReviews','averageRating','ratingCounts','data', 'statistics', 'recent_activity','module_title'));
    }

    public function changepassword($id)
    {
        $user = User::MyVendor()->where('id', $id)->first();
        
        if ($user == null) {
            $message = __('messages.user_not_found');
            return redirect()->route('backend.home')->with('error', $message);
        }
        return view('vendor::backend.changepassword', compact('id'));
        
    }
    public function updatePassword(PasswordRequest $request, $id)
    {
        
        $user = User::where('id', $id)->first();

        if ($user == "") {
            $message = __('messages.user_not_found');
            return redirect()->route('backend.vendors.changepassword', ['id' => $id])->with('error', $message);
        }

        $hashedPassword = $user->password;

        $match = Hash::check($request->old_password, $hashedPassword);

        $same_exits = Hash::check($request->password, $hashedPassword);
        if ($match) {
            if ($same_exits) {
                $message = __('messages.same_pass');
                return redirect()->route('backend.vendors.changepassword', ['id' => $user->id])->with('error', $message);
            }

            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();
            $message = __('messages.pass_successfull');
            if(auth()->user()->hasRole('vendor')){
                return redirect()->route('backend.vendors.details', ['id' => auth()->id()])->with('success', $message);
            }
            return redirect()->route('backend.vendors.index')->with('success', $message);
        } else {
            $message = __('messages.check_old_password');
            return redirect()->route('backend.vendors.changepassword', ['id' => $user->id])->with('error', $message);
        }
    }
    public function subscriptionHistory(Request $request)
    {
        $vendorId  = null;
        if($request->vendor_id !== null){
            try {
                $vendorId = Crypt::decryptString($request->vendor_id);
            } catch (DecryptException $e) {
                return redirect()->back()->with('error', 'Invalid request.');
            }
        }
        
        $userId = $vendorId ?? auth()->id();
        $subscriptions = Subscription::where('user_id', $userId)
        ->with('subscription_transaction')
        ->get();

        $activeSubscriptions =  Subscription::where('user_id', $userId)
        ->where('status', 'active')
        ->where('end_date', '>', now())
        ->orderBy('id', 'desc')
        ->first();

        $module_title=__('messages.subscription_history');
        return view('vendor::backend.subscriptionHistory', compact('activeSubscriptions','module_title', 'subscriptions'));

    }
    public function cancelSubscription(Request $request)
    {
        try {
            $planId = $request->input('plan_id');
            Subscription::where('user_id', auth()->id())
                ->where('id', $request->id)
                ->where('status', 'active')
                ->update(['status' => 'cancel']);

            $otherSubscription=Subscription::where('user_id', auth()->id())
                ->where('status', 'active')->get();

            if($otherSubscription->isEmpty()){

                $user=User::where('id',auth()->id() )->first();

                $user->update(['is_subscribe'=>0]);

            }



            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
