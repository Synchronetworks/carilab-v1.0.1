<?php

namespace Modules\Lab\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Lab\Models\Lab;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Lab\Http\Requests\LabRequest;
use App\Trait\ModuleTrait;
use Modules\World\Models\Country;
use Modules\World\Models\State;
use Modules\World\Models\City;
use Modules\Tax\Models\Tax;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use Modules\Lab\Models\LabTaxMapping;
use Modules\Lab\Models\LabLocationMapping;
use Modules\World\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Modules\Lab\Models\LabSession;
use Modules\Appointment\Models\Appointment;
use Modules\CatlogManagement\Models\CatlogManagement;
use Modules\PackageManagement\Models\PackageManagement;
use Modules\Review\Models\Review;
use Modules\PackageManagement\Models\PackageCatlogMapping;
use Modules\Commision\Models\CommissionEarning;
class LabsController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\LabExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.lab', // module title
            'labs', // module name
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
        $vendor_id = null;

        if ($request->has('vendor_id')) {
            try {
                $vendor_id = Crypt::decrypt($request->vendor_id);
            } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                abort(404);
            }
        }
        $type = 'lab';
        $module_action = __('messages.list');
        $module_title = trans('messages.labs');
        $export_import = true;
        $export_columns = [
            ['value' => 'name', 'text' => __('messages.lbl_name')],
            ['value' => 'lab_code', 'text' => __('messages.lbl_code')],
            ['value' => 'vendor', 'text' => __('messages.lbl_vendor')],
            ['value' => 'status', 'text' => __('messages.lbl_status')],
            ['value' => 'booking_count', 'text' => __('messages.lbl_booking_counts')],
            ['value' => 'collectors_count', 'text' => __('messages.collectors_count')],
            ['value' => 'test_case_counter', 'text' => __('messages.test_case_count')],

        ];
       
        $export_url = route('backend.labs.export');
        
        return view('lab::backend.index', compact('vendor_id', 'module_action','module_title','filter', 'export_import', 'export_columns', 'export_url','type'));
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Lab::myLabs()
            // ->query()
            ->with(['vendor', 'country', 'state', 'city']);
            

            $filter = $request->filter ?? [];

            // Apply filters dynamically if values exist
            $filterableFields = [
                'lab_name'    => 'name',
                'vendor_name' => 'vendor_id',
                'country_id'  => 'country_id',
                'state_id'    => 'state_id',
                'city_id'     => 'city_id'
            ];
            
            foreach ($filterableFields as $filterKey => $dbColumn) {
                if (!empty($filter[$filterKey])) {
                    $query->where($dbColumn, $filter[$filterKey]);
                }
            }
            
            // Search by name or email
            if (!empty($filter['name'])) {
                $query->where(function ($subQuery) use ($filter) {
                    $subQuery->where('name', 'like', "%{$filter['name']}%")
                        ->orWhere('email', 'like', "%{$filter['name']}%");
                });
            }  
             // Lab Name Filter
            if (!empty($filter['lab_name'])) {
                $query->where('name', 'like', '%' . $filter['lab_name'] . '%');
            }
            if (!empty($filter['lab_id'])) {
                $query->where('id', $filter['lab_id']); // Changed 'lab_id' to 'id'
            }
            // Vendor Filter
            if (!empty($filter['vendor_id'])) {
                $query->where('vendor_id', $filter['vendor_id']);
            }

            // Collector Filter
            if (!empty($filter['collector_id'])) {
                $query->whereHas('collectors', function($q) use ($filter) {
                    $q->where('collector_lab_mapping.collector_id', $filter['collector_id']);
                });
            }

            // Tax Filter
            if (!empty($filter['tax_id'])) {
                $query->whereHas('labTaxMapping', function($q) use ($filter) {
                    $q->where('tax_id', $filter['tax_id']);
                });
            }

            // Accreditation Type Filter
            if (!empty($filter['accreditation_type'])) {
                $query->where('accreditation_type', $filter['accreditation_type']);
            }

                     

            if (!empty($filter['payment_mode'])) {
                $query->whereRaw("JSON_CONTAINS(payment_modes, ?)", [json_encode($filter['payment_mode'])]);
            }          

            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        return $datatable->eloquent($query)
            ->editColumn('name', function ($data) {
                return view('lab::backend.lab_details', compact('data'));
            })

            ->filterColumn('name', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where(function ($subQuery) use ($keyword) {
                        $subQuery->where('name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })
          
            ->addColumn('vendor', function ($data) {
                $data = User::find($data->vendor_id);
                return view('user::backend.users.user_details', compact('data'));
            })
            ->filterColumn('vendor', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->whereHas('vendor', function ($subQuery) use ($keyword) {
                        $subQuery->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%')
                            ->orWhere('email', 'like', '%' . $keyword . '%');
                    });
                }
            })

            ->addColumn('booking_count', function ($data) {
                $count = Appointment::where('lab_id', $data->id)->count(); // Adjust model name if different
                return '<a href="' . route('backend.appointments.index', ['lab_id' => encrypt($data->id)]) . '">' .
                    $count . ' Bookings</a>';
            })
            ->addColumn('collectors_count', function ($data) {
                $count = User::where('user_type', 'collector')
                    ->whereHas('lab', function ($query) use ($data) {
                        $query->where('lab_id', $data->id);
                    })->count();
                return '<a href="' . route('backend.collectors.index', ['lab_id' => encrypt($data->id)]) . '">' . $count . ' Collectors</a>';
            })
            ->addColumn('test_case_counter', function ($data) {
                $count = CatlogManagement::where('lab_id', $data->id)->count();
                return '<a href="' . route('backend.catlogmanagements.index', ['lab_id' => $data->id]) . '">' . $count . ' Test Cases</a>';
            })
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $data->id . '" name="datatable_ids[]" value="' . $data->id . '" data-type="lab" onclick="dataTableRowCheck(' . $data->id . ',this)">';
            })
            ->addColumn('action', function ($data) {
                return view('lab::backend.action', compact('data'));
            })
            ->editColumn('status', function ($data) {
                return $data->getStatusLabelAttribute();
            })
            ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
            ->rawColumns(['name', 'action', 'status', 'check', 'contact', 'booking_count', 'collectors_count','code','test_case_counter'])
            ->orderColumns(['id'], ':column $1')
            ->make(true);
    }

    public function lab_index(Request $request)
    {
        
        $labIds = $request->lab_ids ?? null;
        $testcaseid = $request->testcase_id ?? null;
        $testpackageid = $request->testpackage_id ?? null;
        return view('lab::backend.test_case_lab.lab_index', compact('labIds', 'testcaseid', 'testpackageid'));
    }    
    
    public function lab_index_data(Datatables $datatable, Request $request)
    {
        $query = Lab::myLabs();

        // Decode filter array
        $filter = $request->filter;

        // Handle lab_ids filter
        if (!empty($filter['lab_ids'])) {
            $labIds = explode(',', $filter['lab_ids']);
            $labIds = array_map('intval', $labIds);
            $query->whereIn('id', $labIds);
        }

        // Handle test_case_id filter (only apply if test_case_id is present)
        if (!empty($filter['test_case_id'])) {
            $query->with(['testcase' => function ($q) use ($filter) {
                $q->where('id', $filter['test_case_id'])->orWhere('parent_id', $filter['test_case_id']);
            }]);
        } 

        if (!empty($filter['test_package_id'])) {
            $query->with(['testpackage' => function ($q) use ($filter) {
                $q->where('id', $filter['test_package_id'])->orWhere('parent_id', $filter['test_package_id']);
            }]);
        } 


        return $datatable->eloquent($query)
            ->editColumn('name', function ($data) {
                return view('lab::backend.lab_details', compact('data'));
            })
            ->editColumn('price', function ($data) use ($filter) {
                if (!empty($filter['test_case_id'])) {
                    $testcase = $data->testcase->first();
                    return $testcase ? \Currency::format($testcase->price) : '-';
                } elseif (!empty($filter['test_package_id'])) {
                    $testpackage = $data->testpackage->first();
                    return $testpackage ? \Currency::format($testpackage->price) : '-';
                } else {
                    return '-';
                }
            })
            ->filterColumn('name', function($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->filterColumn('price', function($query, $keyword) use ($filter) {
                if (!empty($filter['test_case_id'])) {
                    $query->whereHas('testcase', function($q) use ($keyword) {
                        $q->where('price', 'like', "%{$keyword}%");
                    });
                } elseif (!empty($filter['test_package_id'])) {
                    $query->whereHas('testpackage', function($q) use ($keyword) {
                        $q->where('price', 'like', "%{$keyword}%");
                    });
                }
            })
            ->orderColumn('price', function ($query, $direction) {
                $query->orderByRaw("
                    LEAST(
                        COALESCE((SELECT MIN(price) FROM catlogmanagements WHERE catlogmanagements.lab_id = labs.id), 9999999),
                        COALESCE((SELECT MIN(price) FROM packagemanagements WHERE packagemanagements.lab_id = labs.id), 9999999)
                    ) $direction
                ");
            })            
                    
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row" id="datatable-row-' . $data->id . '" name="datatable_ids[]" value="' . $data->id . '"  onclick="dataTableRowCheck(' . $data->id . ')">';
            })
            ->addColumn('action', function ($data) use ($filter) {
                $testcaseid = null;
                $testpackageid = null;
                if (!empty($filter['test_case_id'])) {
                    $testcaseid = optional($data->testcase->first())->id ?? null; 
                }elseif (!empty($filter['test_package_id'])) {  
                    $testpackageid = optional($data->testpackage->first())->id ?? null;
                }
                 
                

                return view('lab::backend.test_case_lab.action', compact('data','testcaseid','testpackageid'));
            })
            ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
            ->rawColumns(['name', 'action', 'status', 'check', 'contact', 'booking_count', 'collectors_count', 'code', 'test_case_counter'])
            ->orderColumns(['id'], ':column $1')
            ->make(true);
    }
    
    public function index_list(Request $request)
    {
        $vendor_id = $request->vendor_id ?? null;
        $labId = $request->lab_id ?? null;
        $lab = Lab::with(['labTaxMapping.tax'])->where('status',1);
        if ($vendor_id) {
            $lab->where('vendor_id', $vendor_id);
        } 
        
        if ($labId && $labId != null)  {
            $lab->where('id', '!=', $labId);
        }
        $labs = $lab->get();
        return response()->json($labs);
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
            
            // Check if the user exists and has the user_type 'vendor'
            if (!$auth_user || $auth_user->user_type !== 'vendor') {
                return redirect()
                    ->route('backend.labs.index')
                    ->with('error', __('messages.vendor_not_found'));
            }

            // Check if the vendor has reached their lab creation limit
            if ($auth_user->labLimitReach()) {
                return redirect()
                    ->route('backend.labs.index')
                    ->with('error', __('messages.you_cannot_add_laboratory'));
            }
            
        }
        $vendors = User::where('user_type', 'vendor')->where('status', 1)->get();

        $countries = Country::pluck('name', 'id');
        $taxes = Tax::select('id', 'title', 'value')->get();
        $locations = collect([]);
        $paymentGateways = Setting::where('datatype', 'payment_gateways')->where('val', 1)->get();
        $assets = ['textarea'];
        $module_title=__('messages.new_lab');
        return view('lab::backend.create', compact('vendors', 'countries', 'taxes', 'paymentGateways', 'locations', 'assets','module_title'));
    }

    public function store(LabRequest $request)
    {

        try {
            $data = $request->all();
            // $data['slug'] = setNameAttribute($data['name']);
            if(auth()->user()->hasRole('vendor')){
                $auth_user = User::where('id', $data['vendor_id'])
                ->where('user_type', 'vendor')
                ->first();
                
                // Check if the user exists and has the user_type 'vendor'
                if (!$auth_user || $auth_user->user_type !== 'vendor') {
                    return redirect()
                        ->route('backend.labs.index')
                        ->with('error', __('messages.vendor_not_found'));
                }

                // Check if the vendor has reached their lab creation limit
                if ($auth_user->labLimitReach()) {
                    return redirect()
                        ->route('backend.labs.index')
                        ->with('error', __('messages.you_cannot_add_laboratory'));
                }
                
            }
           
            
            $lab = Lab::create($data);
            $this->logActivity('create',$lab,'lab_create');
            $days = [
                ['day' => 'monday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'tuesday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'wednesday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'thursday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'friday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'saturday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => false, 'breaks' => []],
                ['day' => 'sunday', 'start_time' => '09:00', 'end_time' => '18:00', 'is_holiday' => true, 'breaks' => []],
            ];            
            foreach ($days as $key => $val) {

                $val['lab_id'] = $lab->id;
                LabSession::create($val);
            }
            // Sync taxes if selected
            if (!empty($request->tax_id)) {
                $taxMappingData = array_map(fn($taxId) => [
                    'lab_id' => $lab->id,
                    'tax_id' => $taxId,
                ], (array) $request->tax_id);

                LabTaxMapping::insert($taxMappingData); // Use insert for bulk data
            }

            // Sync location if selected

            if (!empty($request->location_id)) {
                // Handle multiple location IDs
                $locationMappingData = [];
                foreach ((array) $request->location_id as $locationId) {
                    $locationMappingData[] = [
                        'lab_id' => $lab->id,
                        'location_id' => $locationId,
                    ];
                }
                LabLocationMapping::insert($locationMappingData);
            }

            if ($request->hasFile('logo')) {
                storeMediaFile($lab, $request->file('logo'), 'logo');
            }

            if ($request->hasFile('license_document')) {
                storeMediaFile($lab, $request->file('license_document'), 'license_document');
            }

            if ($request->hasFile('accreditation_certificate')) {
                storeMediaFile($lab, $request->file('accreditation_certificate'), 'accreditation_certificate');
            }
            $message=__('messages.lab_create');
            return redirect()
                ->route('backend.labs.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.error_creating_lab') . $e->getMessage());
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */

    public function edit($id)
    {
        $data = Lab::myLabs()->with(['labTaxMapping', 'labLocationMapping'])
            ->where('id', $id)
            ->first();

        if ($data == null) {
            return redirect()->route('backend.labs.index')->with('error', __('messages.record_not_found'));
        }
        $vendors = User::where('user_type', 'vendor')->get();
        $data['tax'] = $data->labTaxMapping->pluck('tax_id')->toArray() ?? null;
        $data['location'] = $data->labLocationMapping->pluck('location_id')->toArray() ?? null;
        $countries = Country::pluck('name', 'id');
        if (multivendor() == 1 && !empty($data->vendor_id)) {
            // Query for vendor-specific taxes
            $taxes = Tax::whereHas('user', function ($query) use ($data) {
                $query->where('user_id', $data->vendor_id);
            })->where('status', 1)->get();
        } else {
            // Query for general taxes
            $taxes = Tax::where('status', 1)->get();
        }
        $states = State::where('country_id', $data->country_id)->pluck('name', 'id');
        $cities = City::where('state_id', $data->state_id)->pluck('name', 'id');
        // $taxes = Tax::all();
        $locations = collect([]); // If you have locations
        $paymentGateways = Setting::where('datatype', 'payment_gateways')->where('val', 1)->get();
        $assets = ['textarea'];
        $module_title=__('messages.edit_lab');
        return view('lab::backend.edit', compact(
            'data',
            'vendors',
            'countries',
            'states',
            'cities',
            'taxes',
            'locations',
            'paymentGateways',
            'assets',
            'module_title'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(LabRequest $request, Lab $lab)
    {
       
        try {
            $data = $request->all();

            // Handle file uploads

            $lab->update($data);
            $this->logActivity('update',$lab,'lab_update');
            LabTaxMapping::where('lab_id', $lab->id)->forceDelete();
            LabLocationMapping::where('lab_id', $lab->id)->forceDelete();
            // Sync taxes if selected
            if (!empty($request->tax_id)) {
                // Handle multiple tax IDs
                $taxMappingData = [];
                foreach ((array) $request->tax_id as $taxId) {
                    $taxMappingData[] = [
                        'lab_id' => $lab->id,
                        'tax_id' => $taxId,
                    ];
                }

                LabTaxMapping::insert($taxMappingData);
            }

            // Sync location if selected

            if (!empty($request->location_id)) {
                // Handle multiple location IDs
                $locationMappingData = [];
                foreach ((array) $request->location_id as $locationId) {
                    $locationMappingData[] = [
                        'lab_id' => $lab->id,
                        'location_id' => $locationId,
                    ];
                }
                LabLocationMapping::insert($locationMappingData);
            }

            if ($request->hasFile('logo')) {
                if ($lab->getMedia('logo')->first()) {
                    $lab->clearMediaCollection('logo');
                }
                storeMediaFile($lab, $request->file('logo'), 'logo');
            }

            if ($request->hasFile('license_document')) {
                if ($lab->getMedia('license_document')->first()) {
                    $lab->clearMediaCollection('license_document');
                }
                storeMediaFile($lab, $request->file('license_document'), 'license_document');
            }

            if ($request->hasFile('accreditation_certificate')) {
                if ($lab->getMedia('accreditation_certificate')->first()) {
                    $lab->clearMediaCollection('accreditation_certificate');
                }
                storeMediaFile($lab, $request->file('accreditation_certificate'), 'accreditation_certificate');
            }
            $message=__('messages.lab_update');
            return redirect()
                ->route('backend.labs.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', __('messages.error_updating_lab') . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $lab = Lab::findOrFail($id);
        $lab->delete();
    
        $this->logActivity('delete', $lab, 'lab_delete');
        return response()->json(['message' => trans('messages.delete_lab', ['form' => 'lab']), 'status' => true], 200);
    }
    
    public function restore($id)
    {
        $lab = Lab::withTrashed()->findOrFail($id);
        $lab->restore();
    
        $this->logActivity('restore', $lab, 'lab_restore');
        return response()->json(['message' => __('messages.restore_form')]);
    }
    
    public function forceDelete($id)
    {
        $lab = Lab::withTrashed()->findOrFail($id);
        $lab->forceDelete();
    
        $this->logActivity('force_delete', $lab, 'lab_force_delete');
        return response()->json(['message' => __('messages.permanent_delete_form')]);
    }
    

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;

        return $this->performBulkAction(
            Lab::class,
            $ids,
            $actionType,
            __('lab.status_updated'),
            'Lab'
        );
    }

    public function details($id)
    {
        $data = Lab::with([
            'vendor.userTaxMapping.tax',
            'country',
            'state',
            'city', 
            'labTaxMapping.tax',
            'labLocationMapping.location',
            'review',
        ])->myLabs()->where('id',$id)->first();
        if ($data == null) {
            return redirect()->route('backend.labs.index')->with('error', __('messages.record_not_found'));
        }
        // Get lab statistics

        $totalAppointments = Appointment::where('lab_id', $id)->count();
        $cancelledAppointments = Appointment::where('lab_id', $id)
            ->whereIn('status', ['cancelled','rejected'])
            ->count();
        $completedAppointments = Appointment::where('lab_id', $id)
            ->where('status', 'completed')
            ->count();
        $upcomingAppointments = Appointment::where('lab_id', $id)
            ->whereIn('status', ['pending', 'accept'])
            ->count();

        // Get payment statistics
        $totaltescase = CatlogManagement::where('lab_id', $id)->count();
        $commissionable_id = CommissionEarning::whereHas('getAppointment', function($qry) use($data){
            $qry->where('lab_id',$data->id)->where('vendor_id',$data->vendor_id);
        })->where('employee_id' , $data->vendor_id)->where('commission_status','unpaid')->value('commissionable_id');
        
        $totalpendingpayouts = CommissionEarning::where('commissionable_id' , $commissionable_id)->where('commission_status', 'unpaid')->whereIn('user_type', ['vendor','collector'])
        ->sum('commission_amount');   
        $totalRevenue =  CommissionEarning::whereHas('getAppointment', function($qry) use($data){
            $qry->where('lab_id',$data->id)->where('vendor_id',$data->vendor_id);
        })->where('user_type','vendor')->where('commission_status','paid')->sum('commission_amount');

        // Get review statistics
        $reviews = Review::where('lab_id', $id)->get();
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
        $module_title = $data->name ?? __('messages.lab_detail');

        return view('lab::backend.details', compact(
            'data',
            'totalAppointments',
            'cancelledAppointments',
            'completedAppointments',
            'upcomingAppointments',
            'totaltescase',
            'totalpendingpayouts',
            'totalRevenue',
            'totalReviews',
            'averageRating',
            'ratingCounts',
            'module_title'

        ));
    }
    public function import(Request $request)
    {
        $labId = $request->has('lab_id') ? Crypt::decrypt($request->lab_id) : null;
        $labs = Lab::where('id', '!=', $labId)->get();
        $module_title = trans('messages.import_lab');
        return view('lab::backend.import', compact('labs','labId','module_title'));
    }

    public function importLab(Request $request)
    {

        $importLabIds = (array) $request->import_lab_id;
        $labId = $request->lab_id;  // Source Lab ID
        // $importlab = Lab::where('id',$importLabId)->first();
        // Fetch Test Cases
        $testCases = $request->test_case 
            ? CatlogManagement::where('lab_id', $labId)->whereIn('id', $request->test_case)->get() 
            : collect();
    
        // Fetch Test Packages
        $testPackages = $request->test_package 
            ? $testPackages = PackageManagement::with('packageCatlogMapping.catalog')->where('lab_id', $labId)->whereIn('id', $request->test_package)->get() 
            : collect();
        foreach ($importLabIds as $importLabId) {
                $importlab = Lab::where('id', $importLabId)->first();
                if (!$importlab) {
                    continue; // Skip if lab not found
                }
        // Prepare Test Cases Data for Upsert
        if ($testCases->isNotEmpty()) {
            foreach ($testCases as $testCase) {
                CatlogManagement::updateOrCreate(
                    ['parent_id' => $testCase->parent_id ?? $testCase->id, 'lab_id' => $importLabId], // Unique constraint
                    [
                        'name' => $testCase->name,
                        'code' => $testCase->code,
                        'type' => $testCase->type,
                        'equipment' => $testCase->equipment,
                        'description' => $testCase->description,
                        'category_id' => $testCase->category_id,
                        'vendor_id' => $importlab->vendor_id,
                        'price' => $testCase->price,
                        'duration' => $testCase->duration,
                        'instructions' => $testCase->instructions,
                        'status' => $testCase->status,
                        'is_home_collection_available' => $testCase->is_home_collection_available,
                        'additional_notes' => $testCase->additional_notes,
                        'restrictions' => $testCase->restrictions,
                        'is_featured' => $testCase->is_featured,
                        'updated_by' => auth()->id(),
                        'updated_at' => now(),
                    ]
                );
            }            
        }
    
        // Prepare Test Packages Data for Upsert
        if ($testPackages->isNotEmpty()) {
            // ✅ Filter test packages that have catalogs linked to $importLabId
            $matchtestcase = $testPackages->filter(function ($testPackage) use ($importLabId) {
                return $testPackage->packageCatlogMapping->contains(function ($mapping) use ($importLabId) {
                    return CatlogManagement::where(function ($query) use ($mapping, $importLabId) {
                        $query->where('id', $mapping->catalog_id)
                              ->orWhere('parent_id', $mapping->catalog_id);
                    })->where('lab_id', $importLabId)->exists();
                });
            });
            
            if ($matchtestcase->isNotEmpty()) {
                
                foreach ($matchtestcase as $testPackage) {
                    PackageManagement::updateOrCreate(
                        [
                            'parent_id' => $testPackage->id,
                            'lab_id' => $importLabId
                        ], // 🔹 Unique keys (matching condition)
                        [
                            'name' => $testPackage->name,
                            'description' => $testPackage->description,
                            'price' => $testPackage->price,
                            'start_at' => $testPackage->start_at,
                            'end_at' => $testPackage->end_at,
                            'is_discount' => $testPackage->is_discount,
                            'discount_type' => $testPackage->discount_type,
                            'discount_price' => $testPackage->discount_price,
                            'status' => $testPackage->status,
                            'is_featured' => $testPackage->is_featured,
                            'vendor_id' => $importlab->vendor_id,
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                            'updated_at' => now(),
                        ]
                    );
                }
                
                // ✅ Now insert only valid catalog mappings
                foreach ($matchtestcase as $testPackage) {
                    $newPackage = PackageManagement::where([
                        'parent_id' => $testPackage->id,
                        'lab_id' => $importLabId
                    ])->first(); // Fetch the newly inserted package
               
                    if ($newPackage) {
                        // 🔹 Remove old mappings to prevent duplicates
                        PackageCatlogMapping::where('package_id', $newPackage->id)->delete();
                
                        // ✅ Insert only the matching catalog mappings
                        $catlogMappings = $testPackage->packageCatlogMapping
                            ->map(function ($mapping) use ($importLabId, $newPackage) {
                                // 🔹 Find the correct catalog_id that matches the importLabId
                                $validCatalog = CatlogManagement::where(function ($query) use ($mapping) {
                                        $query->where('id', $mapping->catalog_id)
                                            ->orWhere('parent_id', $mapping->catalog_id);
                                    })
                                    ->where('lab_id', $importLabId)
                                    ->first(); // Fetch the matching catalog

                                if ($validCatalog) {
                                    return [
                                        'package_id' => $newPackage->id,
                                        'catalog_id' => $validCatalog->id, // ✅ Store the correct catalog_id
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];
                                }

                                return null; // Ignore invalid mappings
                            })
                            ->filter() // Remove null values (invalid mappings)
                            ->toArray(); // Convert to array for bulk insert
 
                        if (!empty($catlogMappings)) {
                            PackageCatlogMapping::insert($catlogMappings);
                        }
                    }
                }
                
            }
            
        }
        
        }
        return redirect()->route('backend.labs.index')->with('success', __('messages.lab_test_case_package_import'));
    }

    public function checkUnique(Request $request)
{
    $field = $request->field;
    $value = $request->value;
    $id = $request->id;

    $query = Lab::where($field, $value);
    
    if ($id) {
        $query->where('id', '!=', $id); // Ignore current record
    }

    $exists = $query->exists();

    return response()->json([
        'unique' => !$exists
    ]);
}
    
}