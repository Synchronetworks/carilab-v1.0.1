<?php

namespace Modules\PackageManagement\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\PackageManagement\Models\PackageManagement;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\PackageManagement\Http\Requests\PackageManagementRequest;
use App\Trait\ModuleTrait;
use Modules\Lab\Models\Lab;
use Modules\CatlogManagement\Models\CatlogManagement;
use App\Models\User;
use Modules\PackageManagement\Models\PackageCatlogMapping;

class PackageManagementsController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\PackageManagementExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'packagemanagement.title', // module title
            'packagemanagements', // module name
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
        $type='packagemanagement';
        $module_action = __('messages.list');
        $module_title = trans('messages.packages');
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.lbl_name'),
            ],
            [
                'value' => 'vendor',
                'text' => __('messages.lbl_vendor'),
            ],
            [
                'value' => 'price',
                'text' => __('messages.lbl_price'),
            ],
            [
                'value' => 'discount_price',
                'text' => __('messages.lbl_discount_price'),
            ],
            [
                'value' => 'lab_count',
                'text' => __('messages.lbl_lab_count'),
            ],
            [
                'value' => 'test_case_count',
                'text' => __('messages.lbl_test_case_count'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ]
        ];
        $export_url = route('backend.packagemanagements.export');

        return view('packagemanagement::backend.index', compact('module_action','module_title', 'filter', 'export_import', 'export_columns', 'export_url','type'));
    }
    public function index_list(Request $request)
    {
        $lab_id = $request->lab_id ?? null;

        $packagemanagement = PackageManagement::query();

        if ($lab_id) {
            $packagemanagement =  $packagemanagement->where('lab_id', $lab_id);
        }
        $packagemanagement = $packagemanagement->get();
        return response()->json($packagemanagement);
    }
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'PackageManagement'; // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(PackageManagement::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = PackageManagement::MyPackageManagement()
                 ->where('parent_id',null);

        $filter = $request->filter;

            // Vendor Filter
        if (!empty($filter['vendor_id'])) {
            $query->where('vendor_id', $filter['vendor_id']);
        }

        // Lab Filter
        if (!empty($filter['lab_id'])) {
            $query->where('lab_id', $filter['lab_id']);
        }

        // Test Category Filter
        if (!empty($filter['category_id'])) {
            $query->whereHas('packageCatlogMapping.catalog', function($q) use ($filter) {
                $q->where('category_id', $filter['category_id']);
            });
        }

        // Test Case Filter
        if (!empty($filter['test_id'])) {
            $query->whereHas('packageCatlogMapping', function($q) use ($filter) {
                $q->where('catalog_id', $filter['test_id']);
            });
        }

        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        return $datatable->eloquent($query)
            ->editColumn('name', function ($data) {
                $defaultImage = setBaseUrlWithFileName();
                $image = $data->getPackageImageAttribute() ? $data->getPackageImageAttribute() : $defaultImage;

                return '<div class="d-flex align-items-center gap-3"><img src="' . $image . '" class="avatar avatar-50 rounded-pill"><h6 class="m-0">' . $data->name . '</h6></div>';
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
            ->editColumn('lab_id', function ($data) {
                $query = PackageManagement::where('name', $data->name);
            
                // If the user is a vendor, filter by vendor_id
                if (auth()->user()->hasRole('vendor')) {
                    $query->where('vendor_id', auth()->id());
                }
            
                // Get unique lab IDs as a comma-separated string
                $labIds = $query->distinct()->pluck('lab_id')->implode(',');
            
                // Count labs (handle empty result properly)
                $count = $labIds ? substr_count($labIds, ',') + 1 : 0;
            
                // Return formatted link if labs exist, otherwise show "No Labs"
                return $count > 0 
                    ? '<a href="' . route('backend.labs.lab_index', ['lab_ids' => $labIds, 'testpackage_id' => $data->id]) . '">' . $count . ' Lab</a>'
                    : '<span class="text-muted">No Labs</span>';
            })            
            ->addColumn('catalogs', function ($data) {
                $count = PackageCatlogMapping::where('package_id', $data->id)->count(); // Adjust model name if different
                return '<a href="' . route('backend.catlogmanagements.index', ['package_id' => $data->id]) . '">' .
                    $count . ' Cases</a>';
            })
            ->addColumn('discount_price', function ($data) {
                return $data->discount_type == 'percentage' ? $data->discount_price . '%' : \Currency::format($data->discount_price) ?? 0;
            })
            ->filterColumn('discount_price', function ($query, $keyword) {
                // Remove any non-numeric characters except for the decimal point
                $cleanedKeyword = preg_replace('/[^0-9.]/', '', $keyword);
            
                // Check if the cleaned keyword is not empty
                if ($cleanedKeyword !== '') {
                    $query->whereRaw("CAST(REGEXP_REPLACE(discount_price, '[^0-9.]', '') AS DECIMAL(10, 2)) LIKE ?", ["%{$cleanedKeyword}%"]);
                }
            })
            ->addColumn('price', function ($data) {
                return \Currency::format($data->price) ?? 0;
            })
            ->filterColumn('price', function ($query, $keyword) {
                // Remove any non-numeric characters except for the decimal point
                $cleanedKeyword = preg_replace('/[^0-9.]/', '', $keyword);
            
                // Check if the cleaned keyword is not empty
                if ($cleanedKeyword !== '') {
                    $query->whereRaw("CAST(REGEXP_REPLACE(price, '[^0-9.]', '') AS DECIMAL(10, 2)) LIKE ?", ["%{$cleanedKeyword}%"]);
                }
            })
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" data-type="packagemanagement" onclick="dataTableRowCheck(' . $data->id . ',this)">';
            })
            ->addColumn('action', function ($data) {
                return view('packagemanagement::backend.action', compact('data'));
            })
            ->editColumn('status', function ($data) {
                return $data->getStatusLabelAttribute();
            })
            ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
            ->rawColumns(['action', 'status', 'check', 'name', 'catalogs','lab_id','vendor'])
            ->orderColumns(['id','price','discount_price'], ':column $1')
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
            
            // Check if the user exists and has the user_type 'vendor'
            if (!$auth_user || $auth_user->user_type !== 'vendor') {
                return redirect()
                    ->route('backend.packagemanagements.index')
                    ->with('error', __('messages.vendor_not_found'));
            }

            // Check if the vendor has reached their lab creation limit
            if ($auth_user->testPackageLimitReach()) {
                return redirect()
                    ->route('backend.packagemanagements.index')
                    ->with('error', __('messages.you_cannot_add_test_package'));
            }
            
        }
        $vendors = User::where('user_type', 'vendor')->where('status', 1)->get();
        $labs = Lab::where('status', 1)->get();
        $catalogs = CatlogManagement::where('status', 1)->get();
        $assets = ['textarea'];

        return view('packagemanagement::backend.create', compact('labs', 'vendors', 'catalogs', 'assets'));
    }

    public function store(PackageManagementRequest $request)
    {
        $data = $request->all();
        if(auth()->user()->hasRole('vendor')){
            $auth_user = User::where('id', $data['vendor_id'])
            ->where('user_type', 'vendor')
            ->first();
            
            // Check if the user exists and has the user_type 'vendor'
            if (!$auth_user || $auth_user->user_type !== 'vendor') {
                return redirect()
                    ->route('backend.packagemanagements.index')
                    ->with('error', __('messages.vendor_not_found'));
            }

            // Check if the vendor has reached their lab creation limit
            if ($auth_user->testPackageLimitReach()) {
                return redirect()
                    ->route('backend.packagemanagements.index')
                    ->with('error', __('messages.you_cannot_add_test_package'));
            }
            
        }
        $packagemanagement = PackageManagement::create($data);
        $this->logActivity('create',$packagemanagement,'package_create');
        if (!empty($request->catalog_id)) {
            // Handle multiple tax IDs
            $packageCatlogMapping = [];
            foreach ((array)$request->catalog_id as $catalogId) {
                $packageCatlogMapping[] = [
                    'package_id' => $packagemanagement->id,
                    'catalog_id' => $catalogId,
                ];
            }
            PackageCatlogMapping::insert($packageCatlogMapping);
        }
        if ($request->hasFile('package_image')) {
            storeMediaFile($packagemanagement, $request->file('package_image'), 'package_image');
        }
        $message=__('messages.record_add');
        return redirect()->route('backend.packagemanagements.index')->with('success', $message);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = PackageManagement::MyPackageManagement()->with(['packageCatlogMapping'])
            ->where('id', $id)
            ->first();

        if ($data == null) {
            return redirect()->route('backend.packagemanagements.index')->with('error',  __('messages.record_not_found'));
        }
        $data['catalog_id'] = $data->packageCatlogMapping->pluck('catalog_id')->toArray() ?? null;
        $vendors  = User::where('user_type', 'vendor')->where('status', 1)->get();
        $catalogs = CatlogManagement::where('status', 1)->get();
        if ($data->vendor_id) {
            $labs = Lab::where('status', 1)->get();
            $catalogs = CatlogManagement::where('status', 1)->get();
        }
        if ($data->lab_id) {
            $catalogs = CatlogManagement::where('lab_id', $data->lab_id)->where('status', 1)->get();
        }
        $assets = ['textarea'];
        $module_action = trans('messages.edit');
        $module_title = trans('messages.package');
        return view('packagemanagement::backend.edit', compact('data', 'labs', 'catalogs', 'vendors', 'assets','module_action','module_title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(PackageManagementRequest $request, $id)
    {
        $requestData = $request->all();
        $packagemanagement = PackageManagement::findOrFail($id);
        $packagemanagement->update($requestData);
        $this->logActivity('update',$packagemanagement,'package_update');
        if (!empty($request->catalog_id)) {
            // Handle multiple tax IDs
            PackageCatlogMapping::where('package_id', $packagemanagement->id)->forceDelete();
            $packageCatlogMapping = [];
            foreach ((array)$request->catalog_id as $catalogId) {
                $packageCatlogMapping[] = [
                    'package_id' => $packagemanagement->id,
                    'catalog_id' => $catalogId,
                ];
            }
            PackageCatlogMapping::insert($packageCatlogMapping);
        }
        if ($request->hasFile('package_image')) {
            if ($packagemanagement->package_image) {
                $packagemanagement->clearMediaCollection('package_image');
            }
            storeMediaFile($packagemanagement, $request->file('package_image'), 'package_image');
        }
        $message=__('messages.update_form');
        return redirect()->route('backend.packagemanagements.index')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = PackageManagement::MyPackageManagement()
        ->where('id', $id)
        ->first();
        $data->delete();
        $this->logActivity('delete',$data,'package_delete');
        PackageCatlogMapping::where('package_id', $data->id)->forceDelete();
        $message = __('messages.delete_form');
        return response()->json(['message' =>  $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = PackageManagement::withTrashed()->MyPackageManagement()
        ->where('id', $id)
        ->first();
        $data->restore();
        $this->logActivity('restore',$data,'package_restore');
        PackageCatlogMapping::where('package_id', $data->id)->restore();
        $message = __('messages.restore_form');
        return response()->json(['message' =>  $message]);
    }

    public function forceDelete($id)
    {
        $data = PackageManagement::withTrashed()->MyPackageManagement()
        ->where('id', $id)
        ->first();
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'package_force_delete');
        PackageCatlogMapping::where('package_id', $data->id)->forceDelete();
        $message = __('messages.permanent_delete_form');
        return response()->json(['message' =>  $message]);
    }
}
