<?php
namespace Modules\Vendor\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Modules\Vendor\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Modules\Vendor\Http\Requests\VendorRequest;
use App\Trait\ModuleTrait;
use Modules\Vendor\Models\VendorDocument;
use Modules\Collector\Models\Collector;
use  App\Models\User;
use Modules\Document\Models\Document;
class VendorDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     protected string $exportClass = '\App\Exports\VendorDocumentExport';
     use ModuleTrait {
        initializeModuleTrait as private traitInitializeModuleTrait;
        }

    public function __construct()
    {
        $this->traitInitializeModuleTrait(
            'vendor.title', // module title
            'vendors', // module name
            'fa-solid fa-clipboard-list' // module icon
        );
    }

    public function index(Request $request)
    {

        $filter = [
            'status' => $request->status,
        ];
        $module_action = __('messages.list');
        $module_title = trans('messages.vendor_document_list');
        $export_import = true;
        $export_columns = [
            [
                'value' => 'vendor',
                'text' => __('messages.vendor'),
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
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.vendorDocument')] );
        $auth_user = authSession();
        $assets = ['datatable'];
        $export_url = route('backend.vendordocument.export');
        return view('vendor::backend.VendorDocuments.view', compact('pageTitle','auth_user','assets','filter', 'export_import', 'export_columns', 'export_url','module_title'));

    }

    public function index_data(DataTables $datatable,Request $request)
    {
        $vendordata = $request->vendorDocument;
        if(!empty($vendordata)){

            $query = VendorDocument::myDocument()->where('vendor_id',$vendordata);
        }else{
            $query = VendorDocument::myDocument();
        }

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('is_verified', $filter['column_status']);
            }
            if (isset($filter['vendor_id'])) {
                $query->where('vendor_id', $filter['vendor_id']);
            }
        }
        if (auth()->user()->hasAnyRole(['admin'])) {
            $query;
        }
        
        if (isset($filter['column_status'])) {
            $query->where('status', $filter['column_status']);
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($row) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$row->id.'"  name="datatable_ids[]" value="'.$row->id.'" data-type="vendorDocument" onclick="dataTableRowCheck('.$row->id.',this)">';
            })
           
            ->editColumn('is_verified', function ($row) {
                $userType = auth()->user()->user_type;
            
                // If the user is admin or demo_admin, show the toggle switch
                if (in_array($userType, ['admin', 'demo_admin'])) {
                    $checked = $row->is_verified ? 'checked="checked"' : ''; // Check if the status is true
                    $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
            
                    return '
                        <div class="form-check form-switch">
                            <input type="checkbox" data-url="' . route('backend.vendordocument.update_required', $row->id) . '" 
                                   data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                                   id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                                   ' . $checked . ' ' . $disabled . '>
                        </div>
                    ';
                } else {
                    // For other users, show a static badge
                    return $row->is_verified
                        ? '<span class="badge bg-success-subtle">' . __("messages.verified") . '</span>'
                        : '<span class="badge bg-danger-subtle">' . __("messages.not_verified") . '</span>';
                }
            })
            ->editColumn('status', function ($row) {
                $checked = $row->status ? 'checked="checked"' : ''; // Check if the status is true
                $disabled = $row->trashed() ? 'disabled' : ''; // Disable if the record is soft-deleted
            
                return '
                    <div class="form-check form-switch ">
                        <input type="checkbox" data-url="' . route('backend.vendordocument.update_status', $row->id) . '" 
                               data-token="' . csrf_token() . '" class="switch-status-change form-check-input"  
                               id="datatable-row-' . $row->id . '" name="status" value="' . $row->id . '" 
                               ' . $checked . ' ' . $disabled . '>
                    </div>
                ';
            })      

            ->editColumn('vendor_id', function ($query) {
                return view('vendor::backend.VendorDocuments.user', compact('query'));
            })

            ->editColumn('document_id' , function ($query){
                return ($query->document_id != null && isset($query->document)) ? $query->document->name : '';
            })
            ->orderColumn('document_id', function ($query, $order) {
                $query->select('vendor_documents.*')
                      ->join('documents', 'documents.id', '=', 'vendor_documents.document_id')
                      ->orderBy('documents.name', $order);   
            })
            ->filterColumn('document_id', function ($query, $keyword) {
                $query->whereHas('document', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->filterColumn('vendor_id', function ($query, $keyword) {
                $query->whereHas('vendors', function ($q) use ($keyword) {
                    $q->where('first_name', 'like', '%' . $keyword . '%')
                      ->orWhere('email', 'like', '%' . $keyword . '%'); // Added `orWhere` instead of another `where`
                });
            })
            ->addColumn('action', function($data){
                return view('vendor::backend.VendorDocuments.action',compact('data'))->render();
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

        $message = __('messages.bulk_action_update');

        switch ($actionType) {
            case 'change-featured':
                $branches = VendorDocument::whereIn('id', $ids)->update(['is_verified' => $request->is_verified]);
                $message = __('messages.bulk_vendor_document_feature_update');
                break;

            case 'delete':
                VendorDocument::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_vendor_document_delete');
                break;

            case 'restore':
                VendorDocument::whereIn('id', $ids)->restore();
                $message = __('messages.bulk_vendor_document_restore');
                break;

            case 'permanently-delete':
                VendorDocument::whereIn('id', $ids)->forceDelete();
                $message = __('messages.bulk_vendor_document_permanent_delete');
                break;

            default:
                return response()->json(['status' => false,'is_verified' => false, 'message' => 'Action Invalid']);
                break;
        }

        return response()->json(['status' => true, 'is_verified' => true, 'message' => $message]);
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
        $vendor_document = VendorDocument::find($id);

        if ($vendor_document !== null) {
            $vendor_document = $vendor_document->myDocument()->where('id', $id)->first();
            if($vendor_document == null){
                return redirect()->route('backend.vendors.details', ['id' => $auth_user->id])->with('error',  __('messages.record_not_found'));
            }
        } 
        $pageTitle = trans('messages.update_form_title', ['form' => trans('messages.vendordocument')]);
        $vendordata = null;
        $vendor=null;
        // If ID is not provided, fetch all vendors
        if (empty($id)) {
            $vendor = User::where('user_type', 'vendor')->get();
            $pageTitle = trans('messages.add_button_form', ['form' => trans('messages.vendordocument')]);
            $vendordocument = $request->vendordocument;
            if(auth()->user()->hasRole('vendor')){
                if(auth()->id() != $vendordocument){
                    return redirect()->route('backend.vendors.details', ['id' => $auth_user->id])->with('error', "You Can't create document.");
                }
            }
            $vendor_document = new VendorDocument;
        } else {
            // Fetch the specific vendor data
            $vendordata = User::with('vendordocument')->where('user_type', 'vendor')->where('id', $vendor_document->vendor_id)->first();
        }

        $documents=document::where('status',1)->where('user_type','vendor')->get();
        $module_action = trans('messages.new');
        $module_title = trans('messages.vendor_document');
        return view('vendor::backend.VendorDocuments.create', compact('pageTitle', 'vendor_document','documents', 'auth_user', 'vendordata', 'vendor','module_action','module_title'));
    }
   

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   

    public function store(Request $request)
{
    $data = $request->all();
    if (auth()->user()->hasRole('vendor')) {
        $data['vendor_id'] = auth()->id();
    }
    $data['is_verified'] = !empty($data['is_verified']) ? $data['is_verified'] : 0;
    $data['vendor_id'] = !empty($data['vendor_id']) ? $data['vendor_id'] : auth()->id();
    $result = VendorDocument::updateOrCreate(
        ['id' => $request->id],
        $data 
    );
    if( !empty($request->vendor_document)){
        storeMediaFile($result, $request->vendor_document, 'vendor_document');
    }
    $message = __('messages.record_update', ['form' => __('messages.vendordocument')]);
    if ($result->wasRecentlyCreated) {
        $message = __('messages.record_save', ['form' => __('messages.vendordocument')]);
    }
    if ($request->is('api/*')) {
        return comman_message_response($message);
    }
    if (auth()->user()->hasRole('vendor')) {
        return redirect(route('backend.vendors.details', ['id' => auth()->id()]))->withSuccess($message);
    }
    // Otherwise, redirect to the vendor document page with a success message
    return redirect(route('backend.vendordocument.index'))->withSuccess($message);
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $auth_user = authSession();
        if ($id != auth()->id() && !auth()->user()->hasRole(['admin', 'demo_admin'])) {
            return redirect(route('home'))->withErrors(trans('messages.demo_permission_denied'));
        }
        $vendordata = User::with(['vendorDocument' => function ($query) {
            $query->withTrashed();
        }])->where('user_type', 'vendor')->where('id', $id)->first();

        $filter = [
            'is_verified' => $request->is_verified,
        ];
        $pageTitle = trans('messages.list_form_title',['form' => trans('messages.vendorDocument')] );
        $assets = ['datatable'];


        return view('vendor::backend.VendorDocuments.view', compact('pageTitle' ,'vendordata' ,'auth_user','assets','filter' ));
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
           
        
        $vendor_document = VendorDocument::find($id);

        if( $vendor_document!='') {
            $vendor_document->delete();
        }

        $message = __('messages.delete_form', ['form' => __('vendordocument.title')]);
        if(request()->is('api/*')){
            return response()->json(['message' => $message, 'status' => true], 200);
        }

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function action(Request $request){
        $id = $request->id;

        $vendor_document  = VendorDocument::withTrashed()->where('id',$id)->first();
        $msg = __('messages.not_found_entry',['name' => __('messages.vendorDocument')] );
        if($request->type == 'restore') {
            $vendor_document->restore();
            $msg = __('messages.msg_restored',['name' => __('messages.vendorDocument')] );
        }
        if($request->type === 'forcedelete'){
            $vendor_document->forceDelete();
            $msg = __('messages.msg_forcedelete',['name' => __('messages.vendorDocument')] );
        }
        return comman_custom_response(['message'=> $msg , 'status' => true]);
    }


    public function update_required(Request $request, VendorDocument $id)
    {
        
        $id->update(['is_verified' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

    public function restore($id)
    {
        $data = VendorDocument::withTrashed()->findOrFail($id);
        $data->restore();
        $message = __('messages.restore_form', ['form' => __('vendordocument.title')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function forceDelete($id)
    {
        $data = VendorDocument::withTrashed()->findOrFail($id);
        $data->forceDelete();
        $message = __('messages.permanent_delete_form', ['form' => __('vendordocument.title')]);
        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function update_status(Request $request, VendorDocument $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('messages.status_updated')]);
    }

}
