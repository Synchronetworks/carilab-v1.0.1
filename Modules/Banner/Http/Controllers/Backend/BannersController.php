<?php

namespace Modules\Banner\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Modules\Banner\Models\Banner;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Banner\Http\Requests\BannerRequest;
use App\Trait\ModuleTrait;
use App\Trait\ActivityLogger;

class BannersController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\BannerExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.banner', // module title
            'banners', // module name
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
        $type = 'banner';
        $module_action = __('messages.list');
        $module_title = trans('messages.app_banner');
        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.lbl_name'),
            ],
            [
                'value' => 'description',
                'text' => __('messages.lbl_description'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ]
        ];
        $export_url = route('backend.banners.export');

        return view('banner::backend.index', compact('module_action','module_title', 'filter', 'export_import', 'export_columns', 'export_url','type'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'messages.banner'; 
        $messageKey = __('messages.bulk_action');

        return $this->performBulkAction(Banner::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Banner::query()->withTrashed();

        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }
        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        return $datatable->eloquent($query)
        ->editColumn('name', function ($data) {
            $defaultImage = setBaseUrlWithFileName();
            $image = $data->getBannerImageAttribute() ? $data->getBannerImageAttribute() : $defaultImage;
            $name = $data->name;
            
            return view('category::backend.catagories_detail', ['image' => $image , 'name' => $name])->render();
           })
          ->addColumn('check', function ($data) {
              return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" data-type="banner" onclick="dataTableRowCheck('.$data->id.',this)">';
          })
          ->addColumn('description', function ($data) {
            return '<p class="mb-0 line-count-2">' . ($data->description ?? '--') . '</p>';
        })
          ->addColumn('action', function ($data) {
              return view('banner::backend.action', compact('data'));
          })
          ->editColumn('status', function ($data) {
              return $data->getStatusLabelAttribute();
          })
          ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
          ->rawColumns(['action', 'status', 'check','name','description'])
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
        $assets = ['textarea'];
        $module_action = __('messages.new');
        $module_title = __('messages.banner');
      return view('banner::backend.create',compact('module_action','module_title'));
    }

    public function store(BannerRequest $request)
    {
        $data = $request->all();
        $banner = Banner::create($data);
        $this->logActivity('create',$banner,'banner_create');
        if ($request->hasFile('banner_image')) {
            storeMediaFile($banner, $request->file('banner_image'), 'banner_image');
        }
        $message=__('messages.record_add');
        return redirect()->route('backend.banners.index')->with('success', $message);

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
        $banner = Banner::findOrFail($id);
        if($banner == null){
            return redirect()->route('backend.categories.index')->with('error', __('messages.record_not_found'));
        }
        $module_action = __('messages.edit');
        $module_title = __('messages.banner');
    return view('banner::backend.edit', compact('banner','module_action','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(BannerRequest $request, $id)
    {
        $data = $request->all();
        $data = $request->except('banner_image');

        $banner = Banner::where('id',$id)->first();
        $banner->update($data);
        $this->logActivity('update',$banner,'banner_update');
        if ($request->hasFile('banner_image')) {
            if ($banner->getMedia('banner_image')->first()) {
                $banner->clearMediaCollection('banner_image');
            }
            storeMediaFile($banner, $request->file('banner_image'), 'banner_image');
        }
        $message=__('messages.update_form');
        return redirect()->route('backend.banners.index')->with('success',$message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Banner::findOrFail($id);
        $data->delete();
        $this->logActivity('delete',$data,'banner_delete');
        $message = __('messages.delete_form', ['form' => __('messages.app_banner')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function restore($id)
    {
        $data = Banner::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'banner_restore');
        $message=__('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = Banner::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'banner_force_delete');
       
        $message = __('messages.permanent_delete_form', ['form' => __('messages.app_banner')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }
}