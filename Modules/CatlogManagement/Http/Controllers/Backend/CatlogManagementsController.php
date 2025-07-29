<?php

namespace Modules\CatlogManagement\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\CatlogManagement\Models\CatlogManagement;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\CatlogManagement\Http\Requests\CatlogManagementRequest;
use App\Trait\ModuleTrait;
use Modules\Category\Models\Category;
use App\Models\User;
use Modules\Lab\Models\Lab;
use Modules\Constant\Models\Constant;
use Modules\PackageManagement\Models\PackageManagement;
class CatlogManagementsController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\CatlogManagementExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.lbl_test_case', // module title
            'catlogmanagements', // module name
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
        $lab_id = $request->lab_id ?? null;
        $package_id = $request->package_id ?? null;

        $module_action = __('messages.list');
        $module_title=__('messages.test_case_list');
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.lbl_name'),
            ],
            [
                'value' => 'test',
                'text' => __('messages.lbl_test_category'),
            ],
            [
                'value' => 'vendor',
                'text' => __('messages.lbl_vendor'),
            ],
            [
                'value' => 'lab_count',
                'text' => __('messages.labs'),
            ],
            [
                'value' => 'price',
                'text' => __('messages.lbl_price'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ]
        ];
        $export_url = route('backend.catlogmanagements.export');

        return view('catlogmanagement::backend.index', compact('lab_id','package_id','module_action', 'filter', 'export_import', 'export_columns', 'export_url','module_title'));
    }
    public function index_list(Request $request)
    {
        $lab_id = $request->lab_id ?? null;
        $vendor_id = $request->vendor_id ?? null;
        $catalogs = CatlogManagement::query();
        
        if($vendor_id){
            $catalogs =  $catalogs->where('vendor_id', $vendor_id);
        }
        if($lab_id){
            $catalogs =  $catalogs->where('lab_id', $lab_id);
        }
        $catalogs = $catalogs->where('status',1)->get();
        return response()->json($catalogs);
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.lbl_test_case'); // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(CatlogManagement::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = CatlogManagement::MyCatlogManagement()->whereNull('parent_id');

        $filter = $request->filter;
        $lab_id = $request->lab_id ?? null;
        $package_id = $request->package_id ?? null;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }

            // Vendor Filter
        if (!empty($filter['vendor_id'])) {
            $query->where('vendor_id', $filter['vendor_id']);
        }

        // Category Filter
        if (!empty($filter['category_id'])) {
            $query->where('category_id', $filter['category_id']);
        }

        // Test Type Filter
        if (!empty($filter['test_type_id'])) {
            $query->whereJsonContains('type', $filter['test_type_id']);
        }

        // Lab Filter
        if (!empty($filter['lab_id'])) {
            $query->where('lab_id', $filter['lab_id']);
        }        

        if (isset($filter['vendor'])) {
            $vendorName = $filter['vendor'];
            $query->whereHas('vendor', function ($subQuery) use ($vendorName) {
                $subQuery->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$vendorName%"])
                         ->orWhere('email', 'like', '%' . $vendorName . '%');
            });
        }
        if (isset($lab_id) && $lab_id != null) {
            $query = CatlogManagement::MyCatlogManagement()->where('lab_id',$lab_id);
        }
        if (isset($package_id) && $package_id != null) {
            $query = $query->whereHas('packageCatlogMapping', function($q) use($package_id){
                $q->where('package_id', $package_id);
            });
        }
        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        return $datatable->eloquent($query)
          ->editColumn('category_id', fn($data) => optional($data->category)->name)

        ->editColumn('name', function ($data) {
            $defaultImage = setBaseUrlWithFileName();
            $image = $data->getTestImageAttribute() ? $data->getTestImageAttribute() : $defaultImage;
            $name = $data->name;
            
            return view('catlogmanagement::backend.catlog_detail', ['image' => $image , 'name' => $name])->render();
           })
           
          ->addColumn('vendor', function($data) {
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
        ->editColumn('lab_id', function ($data) {
            $query = CatlogManagement::where('name', $data->name)->where('code', $data->code);
        
            
            if (auth()->user()->hasRole('vendor')) {
                $query->where('vendor_id', auth()->id());
            }
        
            
            $labIds = $query->distinct()->pluck('lab_id')->implode(',');
        
            
            $count = $labIds ? substr_count($labIds, ',') + 1 : 0;

            $testcaseId = $data->parent_id ?? $data->id;
        

            return $count > 0 
                ? '<a href="' . route('backend.labs.lab_index', ['lab_ids' => $labIds, 'testcase_id' => $testcaseId]) . '">' . $count . ' Lab</a>'
                : '<span class="text-muted">No Labs</span>';
        })            

        
          ->editColumn('price', fn($data) =>  \Currency::format($data->price) ?? 0)

          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" data-type="catlogmanagement" onclick="dataTableRowCheck('.$data->id.', this)">';
          })
          ->addColumn('action', function ($data) {
              return view('catlogmanagement::backend.action', compact('data'));
          })
          ->editColumn('status', function ($data) {
              return $data->getStatusLabelAttribute();
          })
          ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
          ->rawColumns(['action', 'status', 'check','name','lab_id'])
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
        if(auth()->user()->hasRole('vendor')){
            $auth_user = User::where('id', auth()->id())
            ->where('user_type', 'vendor')
            ->first();
            
            
            if (!$auth_user || $auth_user->user_type !== 'vendor') {
                return redirect()
                    ->route('backend.catlogmanagements.index')
                    ->with('error', __('messages.vendor_not_found'));
            }

           
            if ($auth_user->testCaseLimitReach()) {
                return redirect()
                    ->route('backend.catlogmanagements.index')
                    ->with('error', __('messages.cannot_add_test_case'));
            }
            
        }
        $categories = Category::where('status',1)->get();
        $vendors = User::where('user_type', 'vendor')->get();
        $labs = Lab::myLabs()->get();
        $test_types = Constant::where('type','test_type')->get();
        $equipments = Constant::where('type','equipment')->get();
        $assets = ['textarea'];
        $module_action=__('messages.add');
        $module_title=__('messages.lbl_test_case');
       
        return view('catlogmanagement::backend.create', compact('categories', 'vendors', 'labs','test_types','equipments','assets','module_title','module_action'));
    }

    public function store(CatlogManagementRequest $request)
    {
        $data = $request->validated();
        if(auth()->user()->hasRole('vendor')){
            $auth_user = User::where('id', $data['vendor_id'])
            ->where('user_type', 'vendor')
            ->first();
            
            // Check if the user exists and has the user_type 'vendor'
            if (!$auth_user || $auth_user->user_type !== 'vendor') {
                return redirect()
                    ->route('backend.catlogmanagements.index')
                    ->with('error', __('messages.vendor_not_found'));
            }

            // Check if the vendor has reached their lab creation limit
            if ($auth_user->testCaseLimitReach()) {
                return redirect()
                    ->route('backend.catlogmanagements.index')
                    ->with('error', __('messages.cannot_add_test_case'));
            }
            
        }
        // Convert equipment array to comma-separated string
        if (isset($data['equipment']) && is_array($data['equipment'])) {
            $data['equipment'] = $data['equipment']; // No need for json_encode
        }
        
        if (isset($data['type']) && is_array($data['type'])) {
            $data['type'] = $data['type']; // No need for json_encode
        }

        $catlogmanagement = CatlogManagement::create($data);
        $this->logActivity('create',$catlogmanagement,'catlog_create');
        // Handle file uploads if present
        if ($request->hasFile('test_image')) {
            storeMediaFile($catlogmanagement, $request->file('test_image'), 'test_image');
        }

        if ($request->hasFile('guidelines_pdf')) {
            storeMediaFile($catlogmanagement, $request->file('guidelines_pdf'), 'guidelines_pdf');
        }
        $message=__('messages.record_add');
        return redirect()->route('backend.catlogmanagements.index')
            ->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $assets = ['textarea'];
        $data = CatlogManagement::MyCatlogManagement()->where('id',$id)->first();
        if ($data == null) {
            return redirect()->route('backend.catlogmanagements.index')->with('error',  __('messages.record_not_found'));
        }
        $categories = Category::all();
        $vendors = User::where('user_type', 'vendor')->get();
        $labs = Lab::where('vendor_id', $data->vendor_id)->get();
        $test_types = Constant::where('type','test_type')->get();
        $equipments = Constant::where('type','equipment')->get();
        $module_action=__('messages.edit');
        $module_title=__('messages.lbl_test_case');
        return view('catlogmanagement::backend.edit', compact('module_action','data', 'categories', 'vendors', 'labs','test_types','equipments','assets','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CatlogManagementRequest $request, $id)
    {
        $data = $request->all();
        $catlogmanagement = CatlogManagement::findOrFail($id);
        if (isset($data['equipment']) && is_array($data['equipment'])) {
            $data['equipment'] = $data['equipment']; // No need for json_encode
        }
        
        if (isset($data['type']) && is_array($data['type'])) {
            $data['type'] = $data['type']; // No need for json_encode
        }

        $catlogmanagement->update($data);
        $this->logActivity('update',$catlogmanagement,'catlog_update');
         // Handle file uploads if present
         if ($request->hasFile('test_image')) {
            if ($catlogmanagement->test_image) {
                $catlogmanagement->clearMediaCollection('test_image');
            }   
            storeMediaFile($catlogmanagement, $request->file('test_image'), 'test_image');
        }

        if ($request->hasFile('guidelines_pdf')) {
            if ($catlogmanagement->guidelines_pdf) {
                $catlogmanagement->clearMediaCollection('guidelines_pdf');
            }
            storeMediaFile($catlogmanagement, $request->file('guidelines_pdf'), 'guidelines_pdf');
        }
        $message=__('messages.update_form');
        return redirect()->route('backend.catlogmanagements.index')->with('success',$message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = CatlogManagement::MyCatlogManagement()->where('id',$id)->first();
        $data->delete();
        $this->logActivity('delete',$data,'catlog_delete');
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = CatlogManagement::withTrashed()->MyCatlogManagement()->where('id',$id)->first();
        $data->restore();   
        $this->logActivity('restore',$data,'catlog_restore');
        $message=__('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = CatlogManagement::withTrashed()->MyCatlogManagement()->where('id',$id)->first();
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'catlog_force_delete');
        $message=__('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }

    public function test_list(Request $request)
    {
        $lab_id = $request->lab_id ?? null;
        $test_type = $request->test_type ?? null;
        $vendor_id = $request->vendor_id ?? null;
        
        // Initialize query builders instead of arrays
        $catalogs = null;
        $packages = null;

        if($test_type == 'test_case') {
            $catalogs = CatlogManagement::query()->where('status', '1');
            
            if($lab_id) {
                $catalogs->where('lab_id', $lab_id);
            }
            if($vendor_id !== null) {
                $catalogs->where('vendor_id', $vendor_id);
            }
            
            $result = $catalogs->get();
            
        } else if($test_type == 'test_package') {
            $packages = PackageManagement::query()->where('status', '1');
            
            if($lab_id) {
                $packages->where('lab_id', $lab_id);
            }
            if($vendor_id !== null) {
                $packages->where('vendor_id', $vendor_id);
            }
            $result = $packages->get();
        } else {
            $result = collect([]); // Empty collection if no test type specified
        }

        return response()->json(['data' => $result]);
    }
}