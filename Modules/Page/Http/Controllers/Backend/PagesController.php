<?php

namespace Modules\Page\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use App\Trait\ActivityLogger;
use Modules\Page\Models\Page;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Page\Http\Requests\PageRequest;
use App\Trait\ModuleTrait;
use Stichoza\GoogleTranslate\GoogleTranslate;
class PagesController extends Controller
{
    use ActivityLogger;
    protected string $exportClass = '\App\Exports\PageExport';

    use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
    }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'Pages', // module title
            'pages', // module name
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

        $module_action = __('messages.list');

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => __('messages.name'),
            ]
        ];
        $export_url = route('backend.pages.export');

        return view('page::backend.pages.index', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url'));
    }

    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = 'Page';

        return $this->performBulkAction(Page::class, $ids, $actionType, $moduleName);
    }

    public function update_status(Request $request, Page $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }
    public function index_data(Datatables $datatable, Request $request)
    {
        $query = Page::query()->withTrashed();
        $filter = $request->filter;
        if (isset($filter['name'])) {
            $query->where('name', $filter['name']);
        }
        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }
        return $datatable->eloquent($query)
            ->editColumn('name', fn($data) => $data->name)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-' . $data->id . '"  name="datatable_ids[]" value="' . $data->id . '" data-type="pages" onclick="dataTableRowCheck(' . $data->id . ',this)">';
            })
            ->addColumn('action', function ($data) {
                return view('page::backend.pages.action', compact('data'));
            })
            ->editColumn('status', function ($row) {
                $checked = $row->status ? 'checked="checked"' : ''; // Check if the status is active
                $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
    
                return '
                <div class="form-check form-switch ">
                    <input type="checkbox" data-url="' . route('backend.pages.update_status', $row->id) . '"
                           data-token="' . csrf_token() . '" class="switch-status-change form-check-input"
                           id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '"
                           ' . $checked . ' ' . $disabled . '>
                </div>
            ';
            })
            ->editColumn('updated_at', function ($data) {
                $diff = \Carbon\Carbon::now()->diffInHours($data->updated_at);
                return $diff < 25 ? $data->updated_at->diffForHumans() : $data->updated_at->isoFormat('llll');
            })
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

    public function create()
    {
        $assets = ['textarea'];
        $module_title = __('messages.add_new_page');
        return view('page::backend.pages.create', compact('assets', 'module_title'));
    }


    public function page($slug)
    {
        $data = Page::where('slug', $slug)->first();
        $navs = Page::all();
        $module_action = __('messages.show');
        return view('page::backend.pages.page', array_merge(compact('data', 'navs', 'module_action'), ['noLayout' => true]));
    }

    public function store(PageRequest $request)
    {
        $data = $request->all();
        $page = Page::create($data);
        $this->logActivity('create',$page,'page_create');
        $message = trans('messages.create_form');

        return redirect()->route('backend.pages.index')->with('success', $message);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */


    public function edit($id)
    {
        $data = Page::findOrFail($id);
        $module_title = __('messages.edit_page');

        $assets = ['textarea'];
        return view('page::backend.pages.edit', compact('data', 'assets', 'module_title'));

    }

    public function privacyPolicy()
    {
        $data = Page::where('slug','privacy-policy')->first();
        $module_title = __('messages.edit_page');

        $assets = ['textarea'];
        return view('page::backend.pages.edit', compact('data', 'assets', 'module_title')); // Make sure this view exists
    }

    public function dataDeletion()
    {
        $data = Page::where('slug','data-deletation-request')->first();
        $module_title = __('messages.edit_page');

        $assets = ['textarea'];
        return view('page::backend.pages.edit', compact('data', 'assets', 'module_title'));;
    }

    public function helpSupport()
    {
        $data = Page::where('slug','help-and-support')->first();
       
        $module_title = __('messages.edit_page');

        $assets = ['textarea'];
        return view('page::backend.pages.edit', compact('data', 'assets', 'module_title'));
    }

    public function termsAndConditions()
    {
        $data = Page::where('slug','terms-conditions')->first();

        $module_title = __('messages.edit_page');

        $assets = ['textarea'];
        return view('page::backend.pages.edit', compact('data', 'assets', 'module_title'));
    }

    public function refundPolicy()
    {
        $data = Page::where('slug','refund-and-cancellation-policy')->first();
        $module_title = __('messages.edit_page');

        $assets = ['textarea'];
        return view('page::backend.pages.edit', compact('data', 'assets', 'module_title'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(PageRequest $request, Page $page)
    {
        $requestData = $request->all();
        $page->update($requestData);
        $this->logActivity('update',$page,'page_update');
        $message = trans('messages.update_form', ['type' => 'Viedo']);

        return redirect()->route('backend.pages.index')->with('success', $message);
    }
    public function show($slug, Request $request)
    {
        // Find the page by slug
        $page = Page::where('slug', $slug)->firstOrFail();

        $currentLang = app()->getLocale();

        $page->description = GoogleTranslate::trans($page->description, $currentLang);
        $page->name = GoogleTranslate::trans($page->name, $currentLang);

        // Pass the page data to the view
        return view('page::backend.pages.show', compact('page'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */

    public function destroy($id)
    {
        $data = Page::where('id', $id)->first();
        $data->delete();
        $this->logActivity('delete',$data,'page_delete');
        $message = trans('messages.delete_form', ['form' => 'Page']);
        return response()->json(['message' => $message, 'status' => true], 200);

    }

    public function restore($id)
    {
        $data = Page::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'page_restore');
        $message = trans('messages.restore_form', ['form' => 'Page']);
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function forceDelete($id)
    {
        $data = Page::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'page_force_delete');
        $message = trans('messages.permanent_delete_form', ['form' => 'Page']);
        return response()->json(['message' => $message, 'status' => true], 200);
    }
}
