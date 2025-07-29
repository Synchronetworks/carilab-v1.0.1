<?php

namespace Modules\Category\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Category\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Category\Http\Requests\CategoryRequest;
use App\Trait\ModuleTrait;

class CategoriesController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\CategoryExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.category_list', // module title
            'categories', // module name
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
        $type = 'category';
        $module_action = __('messages.list');
        $module_title=__('messages.test_category_list');
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
        $export_url = route('backend.categories.export');

        return view('category::backend.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url','module_title','type'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.lbl_category'); // Adjust as necessary for dynamic use
        $messageKey = __('messages.bulk_action'); // You might want to adjust this based on the action

        return $this->performBulkAction(Category::class, $ids, $actionType, $messageKey, $moduleName);
    }

    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Category::MyCategory();

        $filter = $request->filter;

        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }

        if (isset($filter['description'])) {
            $descriptionKeyword = $filter['description'];
            $query->where('description', 'like', '%' . $descriptionKeyword . '%');
        }
        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        return $datatable->eloquent($query)

            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" data-type="category" onclick="dataTableRowCheck(' . $data->id . ', this)">';
            })

            ->editColumn('name', function ($data) {
                $defaultImage = setBaseUrlWithFileName();
                $image = $data->getCategoryImageAttribute() ? $data->getCategoryImageAttribute() : $defaultImage;
                $name = $data->name;

                return view('category::backend.catagories_detail', ['image' => $image, 'name' => $name])->render();
            })
            ->editColumn('description', fn($data) => $data->description)
            ->filterColumn('description', function ($query, $keyword) {
                if (!empty($keyword)) {
                    $query->where('description', 'like', '%' . $keyword . '%');
                }
            })

            ->addColumn('status', function ($row) {
                $checked = $row->status ? 'checked="checked"' : ''; // Check if the status is true
            $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
        
            return '
                <div class="form-check form-switch ">
                    <input type="checkbox" data-url="' . route('backend.categories.update_status', $row->id) . '" 
                           data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                           id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                           ' . $checked . ' ' . $disabled . '>
                </div>
            ';
            })
            ->addColumn('action', function ($data) {
                return view('category::backend.action', compact('data'));
            })

            ->editColumn('updated_at', fn($data) => formatUpdatedAt($data->updated_at))
            ->rawColumns(['action', 'status', 'check', 'description', 'name'])
            ->orderColumns(['id', 'description'], ':column $1')
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
        $module_title=__('messages.add_category');
        return view('category::backend.create', compact('assets','module_title'));
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->all();
        $category = Category::create($data);

        if ($request->hasFile('category_image')) {
            storeMediaFile($category, $request->file('category_image'), 'category_image');
        }

        $this->logActivity('create',$category,'category_create');
        $message=__('messages.record_add');
        return redirect()->route('backend.categories.index')->with('success', $message);

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
        $category = Category::where('id', $id)->first();
        if ($category == null) {
            return redirect()->route('backend.categories.index')->with('error', __('messages.record_not_found'));
        }
        $module_title=__('messages.edit_category');
        return view('category::backend.edit', compact('category', 'assets','module_title'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $data = $request->all();
        $data = $request->except('category_image');

        $category = Category::where('id', $id)->first();
        $category->update($data);

        if ($request->hasFile('category_image')) {
            if ($category->getMedia('category_image')->first()) {
                $category->clearMediaCollection('category_image');
            }
            storeMediaFile($category, $request->file('category_image'), 'category_image');
        }
        $this->logActivity('update',$category,'category_update');
        $message=__('messages.update_form');
        return redirect()->route('backend.categories.index')->with('success',$message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Category::findOrFail($id);
        if ($data == null) {
            return redirect()->route('backend.categories.index')->with('error', __('messages.record_not_found'));
        }
        $data->delete();
        $this->logActivity('delete',$data,'category_delete');
        $message = __('messages.delete_form');
        return response()->json(['message' => $message, 'type' => 'DELETE_FORM']);
    }

    public function restore($id)
    {
        $data = Category::withTrashed()->findOrFail($id);
        if ($data == null) {
            return redirect()->route('backend.categories.index')->with('error', __('messages.record_not_found') );
        }
        $data->restore();
        $this->logActivity('restore',$data,'category_restore');
        $message=__('messages.restore_form');
        return response()->json(['message' => $message]);
    }

    public function forceDelete($id)
    {
        $data = Category::withTrashed()->findOrFail($id);
        if ($data == null) {
            return redirect()->route('backend.categories.index')->with('error', __('messages.record_not_found') );
        }
        if ($data->getMedia('category_image')->first()) {
            $data->clearMediaCollection('category_image');
        }
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'category_force_delete');
        $message=__('messages.permanent_delete_form');
        return response()->json(['message' => $message]);
    }
    public function update_status($id)
    {
        $category = Category::findOrFail($id);
        $category->status = !$category->status;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => __('messages.status_updated')
        ]);
    }
}