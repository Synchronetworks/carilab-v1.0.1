<?php
namespace Modules\Collector\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Modules\Vendor\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Vendor\Http\Requests\VendorRequest;
use App\Trait\ModuleTrait;
use Modules\Collector\Models\CollectorDocument;
use Modules\Collector\Models\Collector;
use  App\Models\User;
use Modules\Document\Models\Document;
use App\Trait\ActivityLogger;
use Modules\Collector\Http\Requests\CollectorDocumentRequest;
class CollectorDocumentController extends Controller
{
    use ActivityLogger;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     protected string $exportClass = '\App\Exports\CollectorDocumentExport';
     use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'messages.collectordocument', // module title
            'collectors', // module name
            'fa-solid fa-clipboard-list' // module icon
        );
    }

    public function index(Request $request)
    {
       

        $filter = [
            'status' => $request->status,
        ];
        $module_action = __('messages.list');
        $module_title = __('messages.collector_document_list');
        $export_import = true;
        $export_columns = [
            [
                'value' => 'collector',
                'text' => __('messages.collector'),
            ],
            [
                'value' => 'document',
                'text' => __('messages.document'),
            ],
            [
                'value' => 'is_verified',
                'text' => __('messages.is_verified'),
            ],
            [
                'value' => 'status',
                'text' => __('messages.lbl_status'),
            ],
        ];
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.collectordocument')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        $export_url = route('backend.collectordocument.export');
        return view('collector::backend.CollectorDocument.view', compact('pageTitle','auth_user','assets','filter','module_title','export_import', 'export_columns', 'export_url'));

    }

    public function index_data(DataTables $datatable,Request $request)
    {
        $collectordata = $request->collectordocument;

        if(!empty($collectordata) && $collectordata!=="null" ){

            $query = CollectorDocument::withTrashed()->myDocument()->where('collector_id',$collectordata);
        }else{
            $query = CollectorDocument::withTrashed()->myDocument();
        }
        $collector_id = $request->query('collector_id');
        if ($collector_id) {
            $query = CollectorDocument::withTrashed()->myDocument()->where('collector_id',$collector_id);
        }
        if (auth()->user()->user_type === 'vendor') {
            $query->whereHas('collectorVendorMapping', function ($q) {
                $q->where('vendor_id', auth()->id());
            });
        }        
        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('is_verified', $filter['column_status']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query;
        }


        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="collectordocument" onclick="dataTableRowCheck('.$row->id.',this)">';
            })
           

            ->orderColumn('collector_id', function ($query, $order) {
                $query->select('collector_documents.*')
                      ->join('users', 'users.id', '=', 'collector_documents.collector_id')
                      ->orderBy('users.first_name', $order)
                      ->orderBy('users.last_name', $order);
            })
            ->editColumn('is_verified', function ($row) {
                $userType = auth()->user()->user_type;
            
                if (in_array($userType, ['admin', 'demo_admin','vendor'])) {
                    $checked = $row->is_verified ? 'checked="checked"' : ''; 
                    $disabled = $row->trashed() ? 'disabled' : '';
            
                    return '
                        <div class="form-check form-switch">
                            <input type="checkbox" data-url="' . route('backend.collectordocument.update_required', $row->id) . '" 
                                   data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                                   id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                                   ' . $checked . ' ' . $disabled . '>
                        </div>
                    ';
                } else {
                    
                    return $row->is_verified
                        ? '<span class="badge bg-success">' . __("messages.verified") . '</span>'
                        : '<span class="badge bg-danger">' . __("messages.not_verified") . '</span>';
                }
            })

            ->editColumn('status', function ($row) {
                $checked = $row->status ? 'checked="checked"' : ''; 
                $disabled = $row->trashed() ? 'disabled' : ''; 
            
                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.collectordocument.update_status', $row->id) . '" 
                               data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                               id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                               ' . $checked . ' ' . $disabled . '>
                    </div>
                ';
            })      

            ->editColumn('collector_id', function ($query) {
                return view('collector::backend.CollectorDocument.user', compact('query'));
            })

            ->editColumn('document_id' , function ($query){
                return ($query->document_id != null && isset($query->document)) ? $query->document->name : '';
            })
            ->orderColumn('document_id', function ($query, $order) {
                $query->select('collector_documents.*')
                      ->join('documents', 'documents.id', '=', 'collector_documents.document_id')
                      ->orderBy('documents.name', $order);   
            })
            ->filterColumn('document_id', function ($query, $keyword) {
                $query->whereHas('document', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->filterColumn('collector_id',function($query,$keyword){
                $query->whereHas('collectors',function ($q) use($keyword){
                    $q->where('first_name','like','%'.$keyword.'%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
                });
            })
            ->addColumn('action', function($data){
                return view('collector::backend.CollectorDocument.action',compact('data'))->render();
            })
            ->addIndexColumn()
            ->rawColumns(['check','vendor_id', 'action','is_verified','status'])
            ->toJson();
    }

    /* bulck action method */
    public function bulk_action(Request $request)
    {
        $ids = explode(',', $request->rowIds);
        $actionType = $request->action_type;
        $moduleName = __('messages.collectordocument');
       

        return $this->performBulkAction(CollectorDocument::class, $ids, $actionType, $moduleName);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function create(Request $request)
    {
        $id = $request->id;
        $auth_user = authSession();
        $collector_document = CollectorDocument::find($id);

        $pageTitle = trans('messages.update_form_title', ['form' => trans('messages.collectordocument')]);
        $collectordata = null;
        $collector=null;
        // If ID is not provided, fetch all vendors
        if (empty($id)) {
            $collector = User::where('user_type', 'collector')->get();
            if(auth()->user()->hasRole('vendor')){
                $collector = User::where('user_type', 'collector')->whereHas('collectorVendormapping', function($q){
                    $q->where('vendor_id',auth()->id());
                })->get();
            }
            $pageTitle = trans('messages.add_button_form', ['form' => trans('messages.collectordocument')]);
            $collector_document = new CollectorDocument;
        } else {

            $collector = User::where('user_type', 'collector')->get();
            if(auth()->user()->hasRole('vendor')){
                $collector = User::where('user_type', 'collector')->whereHas('collectorVendormapping', function($q){
                    $q->where('vendor_id',auth()->id());
                })->get();
            }
            $collectordocument = $request->collectordocument;
            $collectordata = User::with('collectordocument')->where('user_type', 'collector')->where('id', $collector_document->collector_id)->first();
        }

        $documents=document::where('status',1)->where('user_type','collector')->get();
        return view('collector::backend.CollectorDocument.create', compact('pageTitle', 'collector_document','documents', 'auth_user', 'collectordata', 'collector'));
    }
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   

    public function store(CollectorDocumentRequest $request)
{
    $data = $request->all();
    if (auth()->user()->hasRole('Collector')) {
        $data['collector_id'] = auth()->id();
    }
    $data['is_verified'] = !empty($data['is_verified']) ? $data['is_verified'] : 0;
    $data['collector_id'] = !empty($data['collector_id']) ? $data['collector_id'] : auth()->id();
    $result = CollectorDocument::updateOrCreate(
        ['id' => $request->id],
        $data 
    );
    $this->logActivity('create',$result,'collector_document_create');
    if( !empty($request->collector_document)){
        storeMediaFile($result, $request->collector_document, 'collector_document');
    }
    $message = __('messages.record_update', ['form' => __('messages.collectordocument')]);
    if ($result->wasRecentlyCreated) {
        $message = __('messages.record_save', ['form' => __('messages.collectordocument')]);
    }
    if ($request->is('api/*')) {
        return comman_message_response($message);
    }

    return redirect(route('backend.collectordocument.index'))->withSuccess($message);
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $filter = [
            'status' => $request->status,
        ];
   
        $auth_user = authSession();
        if ($id != auth()->id() && !auth()->user()->hasRole(['admin', 'demo_admin'])) {
            return redirect(route('home'))->withErrors(trans('messages.demo_permission_denied'));
        }
        $collectordata = User::with(['collectorDocument' => function ($query) {
            $query->withTrashed();
        }])->where('user_type', 'collector')->where('id', $id)->first();

        $filter = [
            'is_verified' => $request->is_verified,
        ];
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.collectordocument')] );
        $assets = ['datatable'];
       

        return view('collector::backend.CollectorDocument.view', compact('pageTitle' ,'collectordata' ,'auth_user','assets','filter' ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        if(request()->is('api/*')){
            return comman_message_response( __('messages.demo_permission_denied') );
        }
           
        
        $collector_document = CollectorDocument::find($id);

        if( $collector_document!='') {
            $collector_document->delete();
            $this->logActivity('delete',$collector_document,'collector_document_delete');
        }

        $message = __('messages.delete_form', ['form' => __('collectordocument.title')]);
        if(request()->is('api/*')){
            return response()->json(['message' => $message, 'status' => true], 200);
        }

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function action(Request $request){
        $id = $request->id;

        $collector_document  = collectordocument::withTrashed()->where('id',$id)->first();
        $msg = __('messages.not_found_entry',['name' => __('messages.collectordocument')] );
        if($request->type == 'restore') {
            $collector_document->restore();
            $msg = __('messages.msg_restored',['name' => __('messages.collectordocument')] );
        }
        if($request->type === 'forcedelete'){
            $collector_document->forceDelete();
            $msg = __('messages.msg_forcedelete',['name' => __('messages.collectordocument')] );
        }
        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }


    public function update_required(Request $request, CollectorDocument $id)
    {
        
        $id->update(['is_verified' => $request->status]);
        $this->logActivity('update',$id,'collector_document_update');
        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

    public function restore($id)
    {
        $data = CollectorDocument::withTrashed()->findOrFail($id);
        $data->restore();
        $this->logActivity('restore',$data,'collector_document_restore');
        $message = __('messages.restore_form', ['form' => __('collectordocument.title')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function forceDelete($id)
    {
       
        $data = CollectorDocument::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $this->logActivity('force_delete',$data,'collector_document_force_delete');
        $message = __('messages.permanent_delete_form', ['form' => __('collectordocument.title')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function update_status(Request $request, CollectorDocument $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

    public function checkRequired(Request $request)
{
    $document = Document::find($request->document_id);
    return response()->json([
        'is_required' => $document ? $document->is_required == 1 : false
    ]);
}
}