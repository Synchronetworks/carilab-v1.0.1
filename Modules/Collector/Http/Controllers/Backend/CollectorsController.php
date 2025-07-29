<?php

namespace Modules\Collector\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Collector\Models\Collector;
use Modules\Collector\Http\Requests\CollectorRequest;
use App\Models\User;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use App\Trait\ModuleTrait;
use Modules\Lab\Models\Lab;
use App\Models\UserCommissionMapping;
use Modules\Collector\Models\CollectorVendorMapping;
use Modules\Collector\Models\CollectorLabMapping;
use Illuminate\Support\Facades\Crypt;
use Modules\Commision\Models\Commision;
use App\Trait\NotificationTrait;
use Modules\Review\Models\Review;
use Modules\User\Http\Requests\PasswordRequest;
class CollectorsController extends Controller
{
    use NotificationTrait;
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\CollectorExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.collectors', // module title
            'collectors', // module name
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
        $user_type = $request->route('user_type');
        $module_action = __('messages.list');
        $filter = [
            'status' => $request->status,
        ];
        $vendor_id = null;
        
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
                'value' => 'details',
                'text' => __('messages.collector'),
            ],
            [
                'value' => 'vendor',
                'text' => __('messages.vendor'),
            ],
            [
                'value' => 'lab',
                'text' => __('messages.lbl_lab'),
            ],
            [
                'value' => 'mobile',
                'text' => __('messages.lbl_contact_number'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
        ];
        $export_url = route('backend.collectors.export');

        return view('collector::backend.index', compact('vendor_id','module_action', 'filter', 'export_import', 'export_columns', 'export_url','user_type'));
    }
    public function index_list(Request $request,$approval_status)
    {
        $module_action = __('messages.list');
        $filter = [
            'status' => $request->status,
        ];
        $vendor_id = null;
        
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
                'value' => 'details',
                'text' => __('messages.collector'),
            ],
            [
                'value' => 'vendor',
                'text' => __('messages.vendor'),
            ],
            [
                'value' => 'lab',
                'text' => __('messages.lbl_lab'),
            ],
            [
                'value' => 'mobile',
                'text' => __('messages.lbl_conatct_number'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
        ];
        $export_url = route('backend.collectors.export');

        return view('collector::backend.index', compact('vendor_id','module_action', 'filter', 'export_import', 'export_columns', 'export_url','approval_status'));
    }

    public function index_data(DataTables $datatable, Request $request)
    {
        $query = User::myCollector();
                
        if($request->approval_status == null){
            $query = $query->where('status', 1);
        }
        if($request->approval_status == 'pending'){
            $query = $query->where('status', 0);
        }
        if($request->approval_status == 'unassigned'){
            $query = $query->whereDoesntHave('collectorVendormapping');
        }
        $query = $query->with(['country', 'state', 'city', 'lab', 'collectorVendormapping.vendor']);

        $filter = $request->filter;

        if (isset($filter['collector_name']) && !empty($filter['collector_name'])) {
            $query->where('id', $filter['collector_name']); 
        }

        if (isset($filter['lab_name']) && !empty($filter['lab_name'])) {
            $query->whereHas('lab', function($query) use ($filter) {
                $query->where('lab_id', $filter['lab_name']); 
            });
        }

        if (isset($filter['vendor_name']) && !empty($filter['vendor_name'])) {
            $query->whereHas('collectorVendormapping', function($query) use ($filter) {
                $query->where('vendor_id', $filter['vendor_name']);
            });
        }

        if (!empty($filter['country_id'])) {
            $query->where('country_id', $filter['country_id']);
        }
    
        
        if (!empty($filter['state_id'])) {
            $query->where('state_id', $filter['state_id']);
        }
    
        
        if (!empty($filter['city_id'])) {
            $query->where('city_id', $filter['city_id']);
        }

        if (isset($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        
        if (isset($filter['vendor_id'])) {
            $query->whereHas('collectorVendormapping', function($query) use ($filter) {
                $query->where('vendor_id', $filter['vendor_id']);
            });
        }
        if (!empty($filter['gender'])) {
            $query->where('gender', $filter['gender']);
        }
        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }
        return $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-'.$data->id.'" name="datatable_ids[]" value="'.$data->id.'" data-type="users" onclick="dataTableRowCheck('.$data->id.',this)">';
            })
            ->addColumn('name', function ($data) {
                return view('user::backend.users.user_details', compact('data'));
            })
            ->filterColumn('name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where(function ($subQuery) use ($keyword) {
                        $subQuery->where('first_name', 'like', "%{$keyword}%")
                                 ->orWhere('last_name', 'like', "%{$keyword}%")
                                 ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"])
                                 ->orWhere('email', 'like', "%{$keyword}%");
                    });
                }
            })
            ->orderColumn('name', function ($query, $order) {
                
                $query->orderByRaw("CONCAT(first_name, ' ', last_name) $order")
                      ->orderBy('email', $order); 
            })
            ->addColumn('vendor_name', function ($data) {
                if($data->collectorVendormapping){
                    $data = optional($data->collectorVendormapping)->vendor;
                    return view('user::backend.users.user_details', compact('data'));
                }
                return '-';
            })
            ->filterColumn('vendor_name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('collectorVendormapping.vendor', function ($subQuery) use ($keyword) {
                        $subQuery->where('first_name', 'like', "%{$keyword}%")
                                 ->orWhere('last_name', 'like', "%{$keyword}%")
                                 ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$keyword}%"])
                                 ->orWhere('email', 'like', "%{$keyword}%");
                    });
                }
            })
            
            ->addColumn('lab_name', function ($query) {

                if ($query->lab && $query->lab->lab) {
                    $data = $query->lab->lab;
                    return view('lab::backend.lab_details', compact('data'));
                } else {
                    return '-';
                }
            })
            ->filterColumn('lab_name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('lab.lab', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%' . $keyword . '%')
                          ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })
            ->orderColumn('lab_name', function ($query, $order) {
                $query->orderBy(
                    \DB::raw("(SELECT name FROM labs WHERE labs.id = (SELECT lab_id FROM collector_lab_mapping WHERE collector_lab_mapping.collector_id = users.id LIMIT 1))"), 
                    $order
                );
            })
            
            
            
            ->addColumn('contact_number', function ($data) {
                return $data->mobile ?? '-';
            })
            ->filterColumn('contact_number', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('mobile', 'like', '%' . $keyword . '%');
                }
            })
            ->orderColumn('contact_number', function ($query, $order) {
                $query->orderBy('mobile', $order);  
            })
            ->editColumn('is_available', function ($data) {
                return $data->is_available ? '<span class="badge bg-success">Online</span>' : '<span class="badge bg-danger">Offline</span>';
            })
            ->filterColumn('is_available', function ($query, $keyword) {
                if (strtolower($keyword) == 'online') {
                    $query->where('is_available', 1);
                } elseif (strtolower($keyword) == 'offline') {
                    $query->where('is_available', 0);
                }
            })
            ->editColumn('status', function ($data) {
                return $data->status_label;
            })
            ->addColumn('action', function ($data) {
                return view('collector::backend.action', compact('data'));
            })
            ->rawColumns(['action', 'status', 'check','name','lab_name','is_available'])
            ->orderColumns([''], ':column $1')
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
        if(auth()->user()->hasRole('vendor')){
            $auth_user = User::where('id', auth()->id())
            ->where('user_type', 'vendor')
            ->first();
            
           
            if (!$auth_user || $auth_user->user_type !== 'vendor') {
                return redirect()
                    ->route('backend.collectors.index')
                    ->with('error', __('messages.vendor_not_found'));
            }

            if ($auth_user->collectorLimitReach()) {
                return redirect()
                    ->route('backend.collectors.index')
                    ->with('error', __('messages.you_cannot_add_collector'));
            }
            
        }
         $vendors = User::where('user_type', 'vendor')
             ->where('status', 1)
             ->get();
         $labs = Lab::where('status', 1)->get();
         $countries = Country::where('status', 1)->get(); 
        $module_title=__('messages.new_collector');
         return view('collector::backend.create', compact(
             'vendors',
             'countries',
             'labs',
             'module_title'
         ));
     }

    public function store(CollectorRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $data['user_type'] = 'collector';
        if(auth()->user()->hasRole('vendor')){
            $auth_user = User::where('id', $data['vendor_id'])
            ->where('user_type', 'vendor')
            ->first();
            
            if (!$auth_user || $auth_user->user_type !== 'vendor') {
                return redirect()
                    ->route('backend.collectors.index')
                    ->with('error', __('messages.vendor_not_found'));
            }

          
            if ($auth_user->collectorLimitReach()) {
                return redirect()
                    ->route('backend.collectors.index')
                    ->with('error', __('messages.you_cannot_add_collector'));
            }
            
        }
        $collector = User::create($data);
        $collector->assignRole('collector');
        $this->logActivity('create',$collector,'collector_create');
        if(isset($data['education'],$data['degree'],$data['bio'],$data['experience']))
        {
            Collector::updateOrCreate(
                ['user_id' => $collector->id], 
                [
                    'education' => $data['education'],
                    'degree' => $data['degree'],
                    'bio' => $data['bio'],
                    'experience' => $data['experience'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            );
        }
        if(Setting('collector_commission_type') =='per_collector'){
        
            if (isset($data['commission_type'], $data['commission'])) {
            
                $commissionMappingData = [
                    'user_id'         => $collector->id,
                    'commission_type' => $data['commission_type'],
                    'commission'      => $data['commission'],
                    'created_by'      => auth()->id(),
                    'updated_by'      => auth()->id(),
                ];
                UserCommissionMapping::create($commissionMappingData);
            }
    
        }
        
        if (isset($data['vendor_id'])) {
            $collectorvendormapping = [
                'collector_id' => $collector->id,    
                'vendor_id' => $data['vendor_id'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];
            $dataCollectorVendorMapping = CollectorVendorMapping::create($collectorvendormapping);
            $this->logActivity('create',$dataCollectorVendorMapping,'collector_vendor_mapping_create');

        }
        if (!empty($data['lab_id'])) {
            $collectorlabmapping = [
                'collector_id' => $collector->id,
                'lab_id' => $data['lab_id'],
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ];
            $data = CollectorLabMapping::create($collectorlabmapping);
            $this->logActivity('create',$data,'collector_lab_mapping_create');
        }
        

        if ($request->hasFile('profile_image')) {
            storeMediaFile($collector, $request->file('profile_image'), 'profile_image');
        }

        $activity_data = [
            'notification_type'=>'collector_registration',
            'collector_id' => $collector->id,
            'collector' => $collector,
        ];
        $this->sendNotification($activity_data);

        return redirect()
            ->route('backend.collectors.index')
            ->with('success', __('messages.created_successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = User::myCollector()->with('userCommissionMapping','collector','collectorVendormapping','lab')->where('user_type','collector')->where('id',$id)->first();
        if($data == null){
            return redirect()->route('backend.collectors.index')->with('error', __('record_not_found'));
        }

        $commissionData = optional($data->userCommissionMapping->first());
        
        $data['commission_type'] = $data->userCommissionMapping && Setting('collector_commission_type')=='per_collector' ? $commissionData->commission_type : null;
        $data['commission'] = $data->userCommissionMapping && Setting('collector_commission_type')=='per_collector' ? $commissionData->commission : 0;
        $data['education'] = $data->collector->education ?? '-';
        $data['degree'] = $data->collector->degree ?? '-';
        $data['bio'] = $data->collector->bio ?? '-';
        $data['experience'] = $data->collector->experience ?? '-';
        $data['vendor_id'] = $data->collectorVendormapping->vendor_id ?? '-';
        $data['lab_id'] = $data->lab->lab_id ?? '-';
        $countries = Country::all(); 
        $states = State::all(); 
        $cities = City::all(); 
        $vendors = User::where('user_type', 'vendor')
            ->where('status', 1)
            ->get();
        $labs = Lab::where('vendor_id', $data->vendor_id)->where('status', 1)->get();
        $module_title=__('messages.edit_collector');
        return view('collector::backend.edit', compact('data','vendors','labs','countries','states','cities','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CollectorRequest $request, $id)
    {
        $data = $request->all();

        $collector = User::where('id',$id)->first();
        $collector->update($data);
        
        $this->logActivity('update',$collector,'collector_update');
        if(isset($data['education'],$data['degree'],$data['bio'],$data['experience']))
        {
            
            Collector::updateOrCreate(
                ['user_id' => $collector->id], 
                [
                    'education' => $data['education'],
                    'degree' => $data['degree'],
                    'bio' => $data['bio'],
                    'experience' => $data['experience'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            );
        }
        UserCommissionMapping::where('user_id',$collector->id)->forceDelete();
        if(Setting('collector_commission_type') =='per_collector'){
       
            if (isset($data['commission_type'], $data['commission'])) {
            
                $commissionMappingData = [
                    'user_id'         => $collector->id,
                    'commission_type' => $data['commission_type'],
                    'commission'      => $data['commission'],
                    'created_by'      => auth()->id(),
                    'updated_by'      => auth()->id(),
                ];
                UserCommissionMapping::create($commissionMappingData);
            }
    
        }

        if (isset($data['vendor_id'])) {
            CollectorVendorMapping::updateOrCreate(
                ['collector_id' => $collector->id],  
                [
                    'vendor_id' => $data['vendor_id'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            );
        }
        
        if (isset($data['lab_id'])) {
            CollectorLabMapping::updateOrCreate(
                ['collector_id' => $collector->id],  
                [
                    'lab_id' => $data['lab_id'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]
            );
        }        

        if ($request->hasFile('profile_image')) {
            if ($collector->getMedia('profile_image')->first()) {
                $collector->clearMediaCollection('profile_image');
            }
            storeMediaFile($collector, $request->file('profile_image'), 'profile_image');
        }

        return redirect()
            ->route('backend.collectors.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy($id)
{

    $data = User::with('collectorVendormapping', 'lab', 'userCommissionMapping', 'collector')
                ->where('id', $id)
                ->first();

    if ($data->collectorVendormapping) {
        if ($data->collectorVendormapping instanceof \Illuminate\Database\Eloquent\Collection) {
            foreach ($data->collectorVendormapping as $item) {
                $item->delete();
                $this->logActivity('delete', $item, 'collector_vendor_mapping_delete');
            }
        } else {
            $data->collectorVendormapping->delete();
            $this->logActivity('delete', $data->collectorVendormapping, 'collector_vendor_mapping_delete');
        }
    }

    if ($data->lab) {
        if ($data->lab instanceof \Illuminate\Database\Eloquent\Collection) {
            foreach ($data->lab as $item) {
                $item->delete();
                $this->logActivity('delete', $item, 'collector_lab_mapping_delete');
            }
        } else {
            $data->lab->delete();
        }
    }

    if ($data->userCommissionMapping) {
        if ($data->userCommissionMapping instanceof \Illuminate\Database\Eloquent\Collection) {
            foreach ($data->userCommissionMapping as $item) {
                $item->delete();
            }
        } else {
            $data->userCommissionMapping->delete();
        }
    }

    if ($data->collector) {
        if ($data->collector instanceof \Illuminate\Database\Eloquent\Collection) {
            foreach ($data->collector as $item) {
                $item->delete();
            }
        } else {
            $data->collector->delete();
        }
    }

    $data->delete();
    $this->logActivity('delete', $data, 'collector_delete');

    $message = __('messages.delete_form');
    return response()->json(['message' => $message, 'type' => 'DELETE_FORM','status' => true],200);
}


    public function restore($id)
    {
        $data = User::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'restore_restore');
        if($data->collectorVendormapping){
            $data->collectorVendormapping->restore();
            $this->logActivity('restore',$data->collectorVendormapping,'collector_vendor_mapping_restore');
        }
        if($data->lab){
            $data->lab->restore();
            $this->logActivity('restore',$data->lab,'collector_lab_mapping_restore');
        }
        if($data->userCommissionMapping){
            $data->userCommissionMapping->each(function ($commissionMapping) {
                $commissionMapping->restore();
            });
        }
        if($data->collector){
            $data->collector->restore();
        }
        $message=__('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = User::withTrashed()->findOrFail($id);
        if($data->collectorVendormapping){
            $data->collectorVendormapping->forceDelete();
            $this->logActivity('force_delete',$data->collectorVendormapping,'collector_vendor_mapping_force_delete');
        }
        if($data->lab){
            $data->lab->forceDelete();
            $this->logActivity('force_delete',$data->lab,'collector_lab_mapping_force_delete');
        }
        if($data->userCommissionMapping){
            $data->userCommissionMapping->each(function ($commissionMapping) {
                $commissionMapping->forceDelete();
            });
        }
        if($data->collector){
            $data->collector->forceDelete();
        }
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'collector_force_delete');
        if ($data->getMedia('profile_image')->first()) {
            $data->clearMediaCollection('profile_image');
        }
        $message=__('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        if (auth()->user()->user_type === 'vendor') {
            $collectors = Collector::whereIn('id', $ids)
                ->where('vendor_id', auth()->id())
                ->get();
            $ids = $collectors->pluck('id')->toArray();
        }

        return $this->performBulkAction(
            User::class,
            $ids,
            $actionType,
            __('messages.collector_status_update'),
            'Collector'
        );
    }


    public function details($id)
    {
        // Fetch collector details with relationships
        $data = User::with([
            'collector',
            'collectorVendormapping.vendor',
            'lab.lab',
            'userCommissionMapping',
            'country',
            'state',
            'city',
            'collectorAppointmentmapping.appointment',
            'media',
            'reviews', // Profile Image
        ])->myCollector()->where('user_type', 'collector')
          ->where('id', $id)
          ->first();

        if ($data == null) {
            return redirect()->route('backend.collectors.index')->with('error', __('messages.record_not_found'));
        }
        $commissions = Commision::where('user_type','collector')->get();
        
        $appointments = $data->collectorAppointmentmapping->pluck('appointment')->filter();
        $reviews = Review::where('collector_id', $id)->get();
        $totalReviews = $reviews->whereNotNull('review')->count();
        $averageRating = $reviews->avg('rating') ?? 0;
        $ratingCounts = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];
        
        // Calculate statistics
        $statistics = [
        'total_appointments' => $appointments->count(),
        'completed_appointments' => $appointments->where('status', 'completed')->count(),
        'pending_appointments' => $appointments->where('status', 'pending')->count(),
        'cancelled_appointments' => $appointments->where('status', 'cancelled')->count(),
        'totalPaidAmount' => $appointments->where('status', 'completed')->sum('amount'), // Total Earning
        'paidPayouts' => $data->commission_earning()->where('commission_status', 'paid')->sum('commission_amount') ?? 0,
        'pendingPayouts' => $data->commission_earning()->where('commission_status', 'unpaid')->sum('commission_amount') ?? 0, // Total Pending Payouts
        'totalvendor' => $data->collectorVendormapping()->count(), // Total Vendor count
        ];
        $totlearning = $statistics['paidPayouts'] + $statistics['pendingPayouts'];
        // Get recent activity (Last 5 Appointments)
        $recent_activity = $appointments->sortByDesc('created_at')->take(5);
        $module_title = $data->full_name ?? __('messages.collector_detail');
        return view('collector::backend.details', compact('commissions','totlearning','totalReviews','averageRating','ratingCounts','data', 'statistics', 'recent_activity','module_title'));
    }

    public function changepassword($id)
    {
        $user = User::myCollector()->where('id', $id)->first();
        if ($user == null) {
            $message = __('messages.user_not_found');
            return redirect()->route('backend.collectors.index')->with('error', $message);
        }
        return view('collector::backend.changepassword', compact('id'));

    }

    public function updatePassword(PasswordRequest $request, $id)
    {
        
        $user = User::where('id', $id)->first();

        if ($user == "") {
            $message = __('messages.user_not_found');
            return redirect()->route('backend.collectors.changepassword', ['id' => $id])->with('error', $message);
        }

        $hashedPassword = $user->password;

        $match = Hash::check($request->old_password, $hashedPassword);

        $same_exits = Hash::check($request->password, $hashedPassword);
        if ($match) {
            if ($same_exits) {
                $message = __('messages.same_pass');
                return redirect()->route('backend.collectors.changepassword', ['id' => $user->id])->with('error', $message);
            }

            $user->fill([
                'password' => Hash::make($request->password)
            ])->save();
            $message = __('messages.pass_successfull');
            return redirect()->route('backend.collectors.index')->with('success', $message);
        } else {
            $message = __('messages.check_old_password');
            return redirect()->route('backend.collectors.changepassword', ['id' => $user->id])->with('error', $message);
        }


    }
    
}
